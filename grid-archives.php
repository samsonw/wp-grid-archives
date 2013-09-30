<?php
/*
Plugin Name: Grid Archives
Plugin URI: http://blog.samsonis.me/tag/grid-archives/
Version: 1.8.0
Author: <a href="http://blog.samsonis.me/">Samson Wu</a>
Modified by: <a href"https://github.com/Globegitter/">Markus Padourek</a>
Description: Grid Archives offers a grid style archives page for WordPress.

**************************************************************************

Copyright (C) 2008 Samson Wu

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************
 */

define('GRID_ARCHIVES_VERSION', '1.8.0');

/**
 * Guess the wp-content and plugin urls/paths
 */
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
    define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


define('GRID_ARCHIVES_POSTS_TRANSIENT_KEY', 'grid_archives_posts');
define('GRID_ARCHIVES_OPTION_NAME', 'grid_archives_options');


if (!class_exists("GridArchives")) {
    class GridArchives {
        var $options;
        var $style;

        function GridArchives() {
            $this->plugin_url = WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__));

            add_action('wp_print_styles', array(&$this, 'load_styles'));
            add_action('admin_print_scripts', array(&$this, 'load_admin_scripts'));
            add_action('wp_print_footer_scripts', array(&$this, 'load_scripts'));
            add_shortcode('grid_archives', array(&$this, 'display_archives'));

            // admin menu
            add_action('admin_menu', array(&$this, 'grid_archives_settings'));

            // invalidate cache
            add_action('save_post', array(&$this, 'delete_cache'));
            add_action('edit_post', array(&$this, 'delete_cache'));
            add_action('delete_post', array(&$this, 'delete_cache'));

            register_activation_hook(__FILE__, array(&$this, 'install'));
        }

        private function retrieve_cache($key) {
            if($caches = get_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY)) {
                return $caches[$key];
            }
            return NULL;
        }

        private function store_cache($key, $value) {
            $caches = array();
            if($stored_caches = get_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY)) {
                $caches = $stored_caches;
            }

            $caches[$key] = $value;

            // Store the results into the WordPress transient, expires in 1 day (24 hours)
            set_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY, $caches, 60*60*24);
        }

        // Grab all posts and filter them into an array
        private function get_posts($category) {
            $category_id = get_cat_ID($category);

            // If we have a non-expire cached copy of the filtered posts array, use that instead
            if($posts = $this->retrieve_cache($category_id)) {
                return $posts;
            }
            // Get a simple array of all posts under category $category_id
            $rawposts = get_posts(array('numberposts' => -1, 'category' => $category_id, 'order' => $this->options['sort_direction']));

            // Trim some memory
            foreach ( $rawposts as $key => $rawpost ){
                if($this->options['featured_image']){
                    if(has_post_thumbnail($rawposts[$key]->ID)){
                        $rawposts[$key]->post_content = get_the_post_thumbnail($rawposts[$key]->ID, array(130, 130));
                    }else{
                        $rawposts[$key]->post_content = '<div class="grid_archives_default_image"><span aria-hidden="true" class="grid_archives_icon_pencil"></span><div>No image</div></div>';
                    }
                }else{
                    $rawposts[$key]->post_content = '<p>' . $this->get_excerpt($rawposts[$key]->post_content, $this->options['post_content_max_len']) . '</p>'; 
                }
            }

            // Loop through each post and sort it into a structured array
            foreach( $rawposts as $key => $post ) {
                $posts[ mysql2date('Y', $post->post_date) ][ mysql2date('Y.m', $post->post_date) ][] = $post;

                $rawposts[$key] = null;
            }
            $rawposts = null; // More memory cleanup

            if($posts === null) {
                $posts = array();
            }

            // Store the results into the WordPress transient, expires in 1 day (24 hours)
            $this->store_cache($category_id, $posts);
            return $posts;
        }

        private function get_date_format($attr) {
            if($attr['month_date_format'] === 'default'){
                $month_date_format = $this->options['month_date_format'];
                if($month_date_format === 'custom'){
                    $month_date_format = $this->options['month_date_format_custom'];
                }
            }else{
                $month_date_format = $attr['month_date_format'];
            }

            if($attr['post_date_format'] === 'default'){
                $post_date_format = $this->options['post_date_format'];
                if($post_date_format === 'custom'){
                    $post_date_format = $this->options['post_date_format_custom'];
                }
            }else{
                $post_date_format = $attr['post_date_format'];
            }

            return array($month_date_format, $post_date_format);
        }

        private function compose_html_classic($posts, $monthly_summaries, $attr) {
            list($month_date_format, $post_date_format) = $this->get_date_format($attr);
            $html = '<div id="grid_archives" class="grid_archives_column">'
                . '<ul>';
            foreach ($posts as $post_year => $yearly_posts) {
                if($this->options['group_by'] === 'y'){
                    $html .= '<li class="ga_year_month">' . $post_year . '</li>';
                }
                foreach ($yearly_posts as $yearmonth => $monthly_posts) {
                    if($this->options['group_by'] === 'ym'){
                        list($year, $month) = explode('.', $yearmonth);
                        $html .= '<li class="ga_year_month">'
                                . '<a href="' . get_month_link( $year, $month ) . '" title="Monthly Archives: ' . $yearmonth . '">'. mysql2date($month_date_format, date('Y-m-d H:i:s', strtotime($year . '-' . $month))) . '</a>';
                        if(!empty($monthly_summaries[$yearmonth])){
                            $html .= '<span class="ga_monthly_summary">“' . $monthly_summaries[$yearmonth] . '”';
                        }else {
                            $html .= '<span class="ga_monthly_summary">' . $this->options['default_monthly_summary'];
                        }
                    }
                    $html .= '</span></li>';
                    foreach ($monthly_posts as $post) {
                        $html .= '<li class="ga_post">'
                            . '<div class="ga_post_main">'
                            . '<a href="' . get_permalink( $post->ID ) . '" title="' . $post->post_title . '">' . $this->get_excerpt($post->post_title, $this->options['post_title_max_len']) . '</a>'
                            . $post->post_content
                            . '</div>';
                        if(!$this->options['post_date_not_display']){
                            $html .= '<p class="ga_post_date">' . mysql2date($post_date_format, $post->post_date) . '</p>';
                        }
                        $html .= '</li>';
                    }
                }
            }
            $html .= '</ul>' . '</div>';
            return $html;
        }

        private function compose_html_compact($posts, $monthly_summaries, $attr) {
            list($month_date_format, $post_date_format) = $this->get_date_format($attr);
            $compact_month_list_date_format = $this->options['compact_month_list_date_format'];
            if($compact_month_list_date_format === 'custom'){
                $compact_month_list_date_format = $this->options['compact_month_list_date_format_custom'];
            }
            $html = '<div id="grid_archives" class="grid_archives_column">';
            $html .= '<ul class="ga_year_list">';
            foreach ($posts as $year => $yearly_posts) {
                $html .= '<li><a href="' . get_year_link($year) . '" title="Archives of Year ' . $year . '">' . $year . '</a></li>';
            }
            $html .= '</ul>';
            foreach ($posts as $post_year => $yearly_posts) {
                $html .= '<div class="ga_pane">';
                if(!$this->options['compact_hide_month_list'] && $this->options['group_by'] === 'ym'){
                    $html .= '<ul class="ga_month_list">';
                    $month_numbers = $this->get_months('numeric');
                    $month_range = $this->options['sort_direction'] === 'desc' ? range( 12, 1 ) : range( 1, 12 );
                    foreach ( $month_range as $i ) {
                        $month_name = mysql2date($compact_month_list_date_format, date('Y-m-d H:i:s', strtotime($post_year . '-' . $month_numbers[$i])));
                        $month_post_count = count($yearly_posts[$post_year . '.' . $month_numbers[$i]]);
                        if ($month_post_count > 0) {
                            $html .= '<li class="ga_active_month"><a href="#' . $post_year . '_' . $month_numbers[$i] . '" title="' . $month_post_count . ' ' . ($month_post_count === 1 ? 'post' : 'posts') . '">' . $month_name . '</a></li>';
                        }else {
                            $html .= '<li><span title="No post">' . $month_name . '</span></li>';
                        }
                    }
                    $html .= '</ul>';
                }
                $html .= '<ul>';
                foreach ($yearly_posts as $yearmonth => $monthly_posts) {
                    if($this->options['group_by'] === 'ym'){ƒ
                        list($year, $month) = explode('.', $yearmonth);
                        $html .= '<li id="' . $year . '_' . $month . '" class="ga_year_month">'
                        . '<a href="' . get_month_link( $year, $month ) . '" title="Monthly Archives: ' . $yearmonth . '">'. mysql2date($month_date_format, date('Y-m-d H:i:s', strtotime($year . '-' . $month))) . '</a>';
                        if(!empty($monthly_summaries[$yearmonth])){
                            $html .= '<span class="ga_monthly_summary">“' . $monthly_summaries[$yearmonth] . '”';
                        }else {
                            $html .= '<span class="ga_monthly_summary">' . $this->options['default_monthly_summary'];
                        }
                    }
                    $html .= '</span></li>';
                    foreach ($monthly_posts as $post) {
                        $html .= '<li class="ga_post">'
                        . '<div class="ga_post_main">'
                        . '<a href="' . get_permalink( $post->ID ) . '" title="' . $post->post_title . '">' . $this->get_excerpt($post->post_title, $this->options['post_title_max_len']) . '</a>'
                        . $post->post_content
                        . '</div>';
                        if(!$this->options['post_date_not_display']){
                            $html .= '<p class="ga_post_date">' . mysql2date($post_date_format, $post->post_date) . '</p>';
                        }
                        $html .= '</li>';
                    }
                }
                $html .= '</ul></div>';
            }
            $html .= '</div>';
            return $html;
        }

        private function get_months( $format = 'long' ) {
            global $wp_locale;
            $months = array();
            foreach ( range( 1, 12 ) as $i ) {
                if ( 'numeric' == $format ) {
                    $months[$i] = zeroise( $i, 2 );
                    continue;
                }
                $month = $wp_locale->get_month( $i );
                if ( 'short' == $format ) {
                    $month = $wp_locale->get_month_abbrev( $month );
                }
                $months[$i] = esc_html( $month );
            }
            return $months;
        }

        private function get_excerpt($text, $length = 90) {
            if (!$length || mb_strlen($text, 'utf8') <= $length)
                return $text;

            $text = strip_tags($text);
            $text = preg_replace('/\(\(([^\)]*?)\)\)/', '(${1})', $text);
            $text = preg_replace('|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);

            $text = mb_substr($text, 0, $length, 'utf8') . " ...";
            return $text;
        }

        private function parse_summaries($str) {
            $summaries = array();
            foreach (explode("\n", trim($str)) as $line) {
                if(strpos($line, '##') !== FALSE){
                    list($yearmonth, $summary) = array_map('trim', explode("##", $line, 2));
                    if (!empty($yearmonth)){
                        $summaries[$yearmonth] = stripslashes($summary);
                    }
                }
            }
            return $summaries;
        }

        private function get_options() {
            $options = array('style_format' => 'classic', 'featured_image' => false, 'group_by' => 'ym', 'compact_hide_month_list' => false, 'compact_month_list_date_format' => 'F', 'compact_month_list_date_format_custom' => 'F', 'sort_direction' => 'desc', 'post_title_max_len' => 60, 'post_content_max_len' => 90, 'post_date_not_display' => false, 'post_date_format' => 'j M Y', 'post_date_format_custom' => 'j M Y', 'month_date_format' => 'Y.m', 'month_date_format_custom' => 'Y.m', 'post_hovered_highlight' => true, 'monthly_summary_hovered_rotate' => true, 'custom_css_styles' => '', 'load_resources_only_in_grid_archives_page' => false, 'grid_archives_page_names' => 'archives, grid-archives', 'default_monthly_summary' => '“... ...”', 'monthly_summaries' => "2010.09##It was AWESOME!\n2010.08##Anyone who has never made a mistake has never tried anything new.");
            $saved_options = get_option(GRID_ARCHIVES_OPTION_NAME);

            if (!empty($saved_options)) {
                foreach ($saved_options as $key => $option)
                    $options[$key] = $option;
            }

            if ($saved_options != $options) {
                update_option(GRID_ARCHIVES_OPTION_NAME, $options);
            }
            return $options;
        }

        function handle_grid_archives_settings() {
            if (!current_user_can('manage_options'))  {
                wp_die( __('You do not have sufficient permissions to access this page.') );
            }

            $options = $this->get_options();

            if (isset($_POST['submit'])) {
                check_admin_referer('grid-archives-nonce');

                $orig_options = $options;
                $options = array();

                $options['style_format'] = $_POST['style_format'];
                $options['featured_image'] = isset($_POST['featured_image']) ? (boolean)$_POST['featured_image'] : false;;
                $options['group_by'] = $_POST['group_by'];

                $options['compact_hide_month_list'] = isset($_POST['compact_hide_month_list']) ? (boolean)$_POST['compact_hide_month_list'] : false;

                $options['compact_month_list_date_format'] = $_POST['compact_month_list_date_format'];
                $options['compact_month_list_date_format_custom'] = stripslashes($_POST['compact_month_list_date_format_custom']);

                $options['sort_direction'] = $_POST['sort_direction'];

                $options['post_title_max_len'] = (int)$_POST['post_title_max_len'];
                $options['post_content_max_len'] = (int)$_POST['post_content_max_len'];
                $options['post_date_not_display'] = isset($_POST['post_date_not_display']) ? (boolean)$_POST['post_date_not_display'] : false;
                $options['post_date_format'] = $_POST['post_date_format'];
                $options['post_date_format_custom'] = stripslashes($_POST['post_date_format_custom']);

                $options['month_date_format'] = $_POST['month_date_format'];
                $options['month_date_format_custom'] = stripslashes($_POST['month_date_format_custom']);

                $options['post_hovered_highlight'] = isset($_POST['post_hovered_highlight']) ? (boolean)$_POST['post_hovered_highlight'] : false;
                $options['monthly_summary_hovered_rotate'] = isset($_POST['monthly_summary_hovered_rotate']) ? (boolean)$_POST['monthly_summary_hovered_rotate'] : false;

                $options['load_resources_only_in_grid_archives_page'] = isset($_POST['load_resources_only_in_grid_archives_page']) ? (boolean)$_POST['load_resources_only_in_grid_archives_page'] : false;
                $options['grid_archives_page_names'] = $options['load_resources_only_in_grid_archives_page'] ? stripslashes($_POST['grid_archives_page_names']) : $orig_options['grid_archives_page_names'];

                $options['custom_css_styles'] = stripslashes($_POST['custom_css_styles']);

                $options['default_monthly_summary'] = htmlspecialchars(stripslashes($_POST['default_monthly_summary']));
                $options['monthly_summaries'] = htmlspecialchars(stripslashes($_POST['monthly_summaries']));

                update_option(GRID_ARCHIVES_OPTION_NAME, $options);

                $this->delete_cache();
                echo '<div class="updated" id="message"><p>Settings saved.</p></div>';
            }
            include_once("grid-archives-options.php");
        }

        private function get_style_format($style){
            switch ($style) {
                case 'classic':
                case 'compact':
                    break;
                case 'default':
                default:
                    $style = $this->options['style_format'];
                    break;
            }
            return $style;
        }

        function display_archives($atts){
            extract( shortcode_atts( array(
                'category' => 'General',
                'style' => 'default',
                'month_date_format' => 'default',
                'post_date_format' => 'default'
                ), $atts ) );
            $this->style = $this->get_style_format($style);
            $posts = $this->get_posts($category);
            $monthly_summaries = $this->parse_summaries($this->options['monthly_summaries']);
            return call_user_func(array($this, 'compose_html_' . $this->style), $posts, $monthly_summaries, array('month_date_format' => $month_date_format, 'post_date_format' => $post_date_format));
        }

        function grid_archives_settings() {
            add_options_page('Grid Archives Settings', 'Grid Archives', 'manage_options', 'grid-archives-settings', array(&$this, 'handle_grid_archives_settings'));
        }

        private function load_extra_resources(){
            if($this->options['load_resources_only_in_grid_archives_page']){
                $load_extra = false;
                // if enabled, only load resources file (css, js etc) in those specific files
                foreach(array_map('trim', explode(",", $this->options['grid_archives_page_names'])) as $page_name){
                   $load_extra = is_page($page_name);
                   if($load_extra) break;
               }
            }else{
                // disabled, load
               $load_extra = true;
            }
            return $load_extra;
        }

        function load_styles(){
            $this->options = $this->get_options();

            if($this->load_extra_resources()){
                $css_url = $this->plugin_url . '/grid-archives.css';
                wp_register_style('grid_archives', $css_url, array(), GRID_ARCHIVES_VERSION, 'screen');
                wp_enqueue_style('grid_archives');

                if($this->options['post_hovered_highlight'] || $this->options['monthly_summary_hovered_rotate']) {
                    $effect_css_url = $this->plugin_url . '/grid-archives-effect-css.php';
                    wp_register_style('grid_archives_effect', $effect_css_url, array(), GRID_ARCHIVES_VERSION, 'screen');
                    wp_enqueue_style('grid_archives_effect');
                }

                $custom_css_styles = trim($this->options['custom_css_styles']);
                if(!empty($custom_css_styles)) {
                    $custom_css_url = $this->plugin_url . '/grid-archives-custom-css.php';
                    wp_register_style('grid_archives_custom', $custom_css_url, array(), GRID_ARCHIVES_VERSION, 'screen');
                    wp_enqueue_style('grid_archives_custom');
                }
            }
        }

        function load_scripts(){
            if($this->load_extra_resources() && 'compact' === $this->style){
                $jquery_tools_url = $this->plugin_url . '/jquery.tools.tabs.min.js';
                wp_register_script('jquery.tools', $jquery_tools_url, 'jquery' , '1.2.5');
                $js_url = $this->plugin_url . '/grid-archives.js';
                wp_register_script('grid_archives', $js_url, array('jquery', 'jquery.tools') , GRID_ARCHIVES_VERSION);
                wp_print_scripts('grid_archives');
            }
        }

        function load_admin_scripts(){
            $admin_script_url = $this->plugin_url . '/grid-archives-options.js';
            wp_register_script('grid_archives_admin_script', $admin_script_url, 'jquery', GRID_ARCHIVES_VERSION);
            wp_enqueue_script('grid_archives_admin_script');
        }

        function delete_cache() {
            delete_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY);
        }

        function install() {
            $this->options = $this->get_options();
        }
    }
}

if (class_exists("GridArchives")) {
    $grid_archives = new GridArchives();
}

?>
