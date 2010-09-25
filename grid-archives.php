<?php
/* 
Plugin Name: Grid Archives
Plugin URI: http://blog.samsonis.me/
Version: 0.5.0
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

define('GRID_ARCHIVES_VERSION', '0.5.0');

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


if (!class_exists("GridArchives")) {
    class GridArchives {
        function GridArchives() {
            $this->plugin_url = WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__));

            add_action('wp_print_styles', array(&$this, 'load_styles'));
            add_shortcode('grid_archives', array(&$this, 'display_archives'));

            // invalidate cache
            add_action('save_post', array(&$this, 'delete_cache'));
            add_action('edit_post', array(&$this, 'delete_cache'));
            add_action('delete_post', array(&$this, 'delete_cache'));
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
                $rawposts[$key]->post_content = $this->get_excerpt($rawposts[$key]->post_content);

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
            $html = '<div id="grid_archives" class="grid_archives_column">'
                . '<ul>';
            foreach ($posts as $yearmonth => $monthly_posts) {
                $html .= '<li class="ga_year_month">' . $yearmonth;
                if(!empty($monthly_summaries[$yearmonth])){
                    $html .= '<span class="ga_monthly_summary">“' . $monthly_summaries[$yearmonth] . '”';
                }else {
                    $html .= '<span class="ga_monthly_summary">“... ...”';
                }
                $html .= '</span></li>';
                foreach ($monthly_posts as $post) {
                    $html .= '<li class="ga_post">'
                        . '<div class="ga_post_main">'
                        . '<a href="' . get_permalink( $post->ID ) . '" title="' . $post->post_title . '">' . $this->get_excerpt($post->post_title, 60) . '</a>'
                        . '<p>' . $post->post_content . '</p>'
                        . '</div>'
                        . '<p class="ga_post_date">' . mysql2date('j M Y', $post->post_date) . '</p>'
                        . '</li>';
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

        private function parse_summaries($str){
            $summaries = array();
            foreach (explode("\n", $str) as $line) {
                list($yearmonth, $summary) = array_map('trim', explode("#", $line, 2));
                if (!empty($yearmonth))
                    $summaries[$yearmonth] = stripslashes($summary);
            }
            return $summaries;
        }

        function display_archives($atts){
            $posts = $this->get_posts();
            // TODO make this an option
            $summaries_str = "2010.09#Kindle, Kindle ...\n2010.08#Avatar, 3D, IMAX\n2010.07#世界杯……\n";
            $monthly_summaries = $this->parse_summaries($summaries_str);
            return $this->compose_html($posts, $monthly_summaries);
        }

        function load_styles(){
            $css_url = $this->plugin_url . '/grid-archives.css';
            wp_register_style('grid_archives', $css_url, array(), GRID_ARCHIVES_VERSION, 'screen');
            wp_enqueue_style('grid_archives');
        }

        function delete_cache() {
            delete_transient(GRID_ARCHIVES_POSTS_TRANSIENT_KEY);
        }
    }
}

if (class_exists("GridArchives")) {
    $grid_archives = new GridArchives();
}

?>
