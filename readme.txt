=== Grid Archives ===
Contributors: samsonw, globegitter
Tags: archive, archives, grid
Requires at least: 2.5
Tested up to: 3.5.1
Stable tag: 1.8.0
License: GPLv3

Grid Archives offers a grid style archives page for WordPress.


== Description ==

*Grid Archives* offers a grid style archives page for WordPress, just put "[grid_archives]" in your Post or Page to show the grid-style archives.

The default config will display all posts in every categories.  To display posts under one specific category, input [grid_archives category="Category name"] instead.

View [live demo](http://blog.samsonis.me/archives)

**For latest update, please check github repository:**
**http://github.com/samsonw/wp-grid-archives**


== Installation ==

1. Upload and unpack the grid-archives plugin to wordpress plugin directory **"wp-content/plugins/grid-archives"**
2. Activate **Grid Archives** plugin on your *Plugins* page in *Site Admin*.
3. Create a "Archives" page (use "Page Full Width" template to get more width)
4. Put "[grid_archives]" in the content and save it.
5. Check the newly created page


== Frequently Asked Questions ==


== Screenshots ==
http://blog.samsonis.me/archives/

http://blog.samsonis.me/archives-of-the-year/


== Changelog ==

= 1.8.0 =
* Added the option to display featured image instead of post content
* Added the option to group the posts either bey year (new) or year and month (old/default)
* Other minor changes on the settings page
* Tested with wordpress 3.5.1

= 1.7.0 =
* fixed the cache issue

= 1.6.0 =
* added an option to change the post sort direction (asec or desc)
* make the plugin compatible with wordpress 3.4

= 1.5.1 =
* minor css style changes
* make the plugin compatible with wordpress 3.3

= 1.5.0 =
* added month list view in 'compact' mode, the page will smoothly scroll to that month if clicked
* added an option to hide the month list, thus disable the scroll capability
* added an option to customize the date display format for month list

= 1.4.1 =
* tweaked the year list display style
* fixed an issue that the screen will scroll up to the top during year expand
* only load 'compact' style required js in 'compact' mode, not 'classic' mode

= 1.4.0 =
* added an compact (expand) display style
* added 3 shortcode attributes (style, month_date_format, post_date_format), grid-archives now supports [grid_archives style="classic|compact" month_date_format="Y.m" post_date_format="j M Y"]

= 1.3.0 =
* tiny UI tweak in plugin admin setting page, make it compatible with the Refreshed Administative UI of wordpress 3.2.
* make the plugin compatible with wordpress 3.2.

= 1.2.0 =
* added a option to enable load plugin resources only in specific pages and posts.

= 1.1.0 =
* added a shortcode category attribute to display articles under one specific category, grid-archives now support [grid_archives category="Category name"]

= 1.0.2 =
* the plugin is actually compatible with wordpress 3.1

= 1.0.1 =
* fixed a month date format display issue

= 1.0.0 =
* added an option to customize the date display format for months

= 0.9.0 =
* added an option to highlight the post if being mouse hovered over. (IE currently not supported)
* added an option to rotate the monthly summary if being mouse hovered over. (IE currently not supported)
* made the "year.month" text clickable to show all the posts in that particular month.

= 0.8.1 =
* fixed a compatible issue with wp-footnotes.

= 0.8.0 =
* added an option to allow user to custom the display stylesheets, these customizations won't be lost after plugin update.

= 0.7.0 =
* make post date display style as an option, e.g. [displayed using American style (month/day/year)](http://wordpress.org/support/topic/plugin-grid-archives-changing-date-order?replies=4)
* added an option to make monthly summary ("… …") optional

= 0.6.4 =
* explicitly set grid-archives ul.li background to none and transparent, so they won't use theme's default setting

= 0.6.3 =
* added option to not display post date

= 0.6.2 =
* fixed the [magic quotes issue](http://wordpress.org/support/topic/plugin-grid-archives-bug-report-repeatedly-escaping-characters)

= 0.6.1 =
* added color property for a, a:link, a:visited and a:hover in the post box

= 0.6.0 =

* added settings page in admin
* added options in settings for user to specify the maximum post title and maximum post content length
* added options in settings for user to specify the monthly summaries
* remove .grid_archives_column width property
* trim monthly summaries before parse


= 0.5.0 =

* Initial import the working copy of my blog archives page: http://blog.samsonis.me/archives/

