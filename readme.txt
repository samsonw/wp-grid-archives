=== Grid Archives ===
Contributors: samsonw
Tags: archive, archives, grid
Requires at least: 2.5
Tested up to: 3.0
Stable tag: 0.8.1
License: GPLv3

Grid Archives offers a grid style archives page for WordPress.


== Description ==

*Grid Archives* offers a grid style archives page for WordPress, just put "[grid_archives]" in your Post or Page to show the grid-style archives.

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


== Changelog ==

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

