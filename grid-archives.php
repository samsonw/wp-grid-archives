<?php
/* 
Plugin Name: Grid Archives
Plugin URI: http://blog.samsonis.me/tag/grid-archives/
Version: 0.8.0
Author: <a href="http://blog.samsonis.me/">Samson Wu</a>
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

define('GRID_ARCHIVES_VERSION', '0.8.0');

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

        function GridArchives() {
            $this->plugin_url = WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__));

            add_action('wp_print_styles', array(&$this, 'load_styles'));
            add_shortcode('grid_archives', array(&$this, 'display_archives'));

            // admin menu
            add_action('admin_menu', array(&$this, 'grid_archives_settings'));

            // invalidate cache
            add_action('save_post', array(&$this, 'delete_cache'));
            add_action('edit_post', array(&$this, 'delete_cache'));
            add_action('delete_post', array(&$this, 'delete_cache'));

            register_activation_hook(__FILE__, array(&$this, 'install'));
        }

        // Grab all posts and filter them into an array
        private function get_posts() {
            // If we have a non-expire cached copy of the filtered posts array, use that instead
            if($posts = get_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY)) {
                return $posts;
            }

            // Get a simple array of all posts
            $rawposts = get_posts('numberposts=-1');

            // Trim some memory
            foreach ( $rawposts as $key => $rawpost )
                $rawposts[$key]->post_content = $this->get_excerpt($rawposts[$key]->post_content, $this->options['post_content_max_len']);

            // Loop through each post and sort it into a structured array
            foreach( $rawposts as $key => $post ) {
                $posts[ mysql2date('Y.m', $post->post_date) ][] = $post;

                $rawposts[$key] = null;
            }
            $rawposts = null; // More memory cleanup

            // Store the results into the WordPress transient, expires in 1 day (24 hours)
            set_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY, $posts, 60*60*24);
            return $posts;
        }

        private function compose_html($posts, $monthly_summaries) {
            $post_date_format = $this->options['post_date_format'];
            if($post_date_format === 'custom'){
                $post_date_format = $this->options['post_date_format_custom'];
            }
            $html = '<div id="grid_archives" class="grid_archives_column">'
                . '<ul>';
            foreach ($posts as $yearmonth => $monthly_posts) {
                $html .= '<li class="ga_year_month">' . $yearmonth;
                if(!empty($monthly_summaries[$yearmonth])){
                    $html .= '<span class="ga_monthly_summary">“' . $monthly_summaries[$yearmonth] . '”';
                }else {
                    $html .= '<span class="ga_monthly_summary">' . $this->options['default_monthly_summary'];
                }
                $html .= '</span></li>';
                foreach ($monthly_posts as $post) {
                    $html .= '<li class="ga_post">'
                        . '<div class="ga_post_main">'
                        . '<a href="' . get_permalink( $post->ID ) . '" title="' . $post->post_title . '">' . $this->get_excerpt($post->post_title, $this->options['post_title_max_len']) . '</a>'
                        . '<p>' . $post->post_content . '</p>'
                        . '</div>';
                    if(!$this->options['post_date_not_display']){
                        $html .= '<p class="ga_post_date">' . mysql2date($post_date_format, $post->post_date) . '</p>';
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</ul>' . '</div>';
            return $html;
        }

        private function get_excerpt($text, $length = 90) {
            if (!$length || mb_strlen($text, 'utf8') <= $length)
                return $text;

            $text = strip_tags($text);
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
            $options = array('post_title_max_len' => 60, 'post_content_max_len' => 90, 'post_date_not_display' => false, 'post_date_format' => 'j M Y', 'post_date_format_custom' => 'j M Y', 'custom_css_styles' => '', 'default_monthly_summary' => '“... ...”', 'monthly_summaries' => "2010.09##It was AWESOME!\n2010.08##Anyone who has never made a mistake has never tried anything new.");
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

                $options = array();

                $options['post_title_max_len'] = (int)$_POST['post_title_max_len'];
                $options['post_content_max_len'] = (int)$_POST['post_content_max_len'];
                $options['post_date_not_display'] = isset($_POST['post_date_not_display']) ? (boolean)$_POST['post_date_not_display'] : false;
                $options['post_date_format'] = $_POST['post_date_format'];
                $options['post_date_format_custom'] = stripslashes($_POST['post_date_format_custom']);
                
                $options['custom_css_styles'] = stripslashes($_POST['custom_css_styles']);
                
                $options['default_monthly_summary'] = htmlspecialchars(stripslashes($_POST['default_monthly_summary']));
                $options['monthly_summaries'] = htmlspecialchars(stripslashes($_POST['monthly_summaries']));

                update_option(GRID_ARCHIVES_OPTION_NAME, $options);

                $this->delete_cache();
                echo '<div class="updated" id="message"><p>Settings saved.</p></div>';
            }
            include_once("grid-archives-options.php");
        }

        function display_archives($atts){
            // $this->options = $this->get_options();
            $posts = $this->get_posts();
            $monthly_summaries = $this->parse_summaries($this->options['monthly_summaries']);
            return $this->compose_html($posts, $monthly_summaries);
        }

        function grid_archives_settings() {
            add_options_page('Grid Archives Settings', 'Grid Archives', 'manage_options', 'grid-archives-settings', array(&$this, 'handle_grid_archives_settings'));
        }

        function load_styles(){
            $this->options = $this->get_options();
            
            $css_url = $this->plugin_url . '/grid-archives.css';
            wp_register_style('grid_archives', $css_url, array(), GRID_ARCHIVES_VERSION, 'screen');
            wp_enqueue_style('grid_archives');
            
            $custom_css_styles = trim($this->options['custom_css_styles']);
            if(!empty($custom_css_styles)) {
                $custom_css_url = $this->plugin_url . '/grid-archives-custom-css.php';
                wp_register_style('grid_archives_custom', $custom_css_url, array(), GRID_ARCHIVES_VERSION, 'screen');
                wp_enqueue_style('grid_archives_custom');
            }
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
