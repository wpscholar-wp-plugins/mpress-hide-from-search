=== Hide from Search ===
Contributors: wpscholar
Donate link: https://www.paypal.me/wpscholar
Tags: search, search engines, WordPress search, hide from search, exclude from search, hide post, hidden posts
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 6.2
Stable tag: 1.1.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Hide individual WordPress pages from search engines and/or WordPress search, such as confirmation and download pages that should only be accessible via a squeeze page.

== Description ==

The **Hide from Search** plugin allows you to hide individual WordPress pages from search engines and/or WordPress search results.

= Why? =

It isn't uncommon to have pages on your site that are public, but not intended to be found. Take, for example, a download page where people who have signed up for your email newsletter can download your amazing white paper.  You don't want just anyone to be able to download your white paper, but the page has to be public because people who sign up for your newsletter aren't going to be logged into your site.  You can't have people who perform a search for the title of your whitepaper finding it in search.  The solution?  Download this plugin and hide your download page from WordPress search as well as search engines!

= How? =

Using this plugin is simple:

1. Install the plugin
2. Activate the plugin
3. Go to a post you want to hide and check the 'Hide from search' checkbox on the bottom right of the screen.
4. Save your changes

= Features =

* Works with custom post types
* No settings page, just a simple, easy-to-use checkbox
* Clean, well written code that won't bog down your site

== Installation ==

= Prerequisites =
If you don't meet the below requirements, I highly recommend you upgrade your WordPress install or move to a web host that supports a more recent version of PHP.

* Requires WordPress version 4.7 or greater
* Requires PHP version 5.6 or greater

= The Easy Way =

1. In your WordPress admin, go to 'Plugins' and then click on 'Add New'.
2. In the search box, type in 'Hide from Search' and hit enter.  This plugin should be the first and likely the only result.
3. Click on the 'Install' link.
4. Once installed, click the 'Activate this plugin' link.

= The Hard Way =

1. Download the .zip file containing the plugin.
2. Upload the file into your `/wp-content/plugins/` directory and unzip
3. Find the plugin in the WordPress admin on the 'Plugins' page and click 'Activate'

= Usage Instructions =

Once the plugin is installed and activated, go to a post you want to hide and check the 'Hide from search' checkbox on
the bottom right of the screen and save your changes.  Viola! The post has been hidden!

== Screenshots ==

1. Using the plugin is simple, just check the box to hide a page or post.

== Changelog ==

= 1.1.6 =
* Version bump to fix automated release process.

= 1.1.5 =
* Fix issue with wp-forge/container package not being compatible with PHP versions older than 8.0

= 1.1.4 =
* Minor maintenance release

= 1.1.3 =
* Fix PHP notice when Yoast SEO is active
* General security updates for dependencies.

= 1.1.2 =
* Fix issue where posts were hidden from users when searching within the WordPress admin area.

= 1.1.1 =
* Bugfix: Function _wp_register_meta_args_whitelist changed to _wp_register_meta_args_allowed_list in WordPress 5.5.

= 1.1 =
* Implemented ability to hide from search engines as well.

= 1.0.2 =
* Tested in WordPress version 5.4

= 1.0.1 =
* Tested in WordPress version 5.3.2

= 1.0.0 =
* Tested in WordPress version 4.7
* Escaped translated strings, for security.
* Converted singleton to static class.
* Updated .pot translation file to include additional plugin information.

= 0.4.3 =
* Tested in WordPress version 4.5.2

= 0.4.2 =
* Tested in WordPress version 4.4.3

= 0.4.1 =
* Tested in WordPress version 4.2.4

= 0.4 =
* Tested in WordPress version 4.0
* Added additional comments to the code
* Tweaked how translations are loaded.

= 0.3 =
* Tested in WordPress version 3.5.1
* Deleted MPRESS_HIDE_FROM_SEARCH_VERSION constant and created a public static variable instead
* Fire class on plugins_loaded action instead of on load.
* Added call to load_plugin_textdomain()

= 0.2 =
* Added MPRESS_HIDE_FROM_SEARCH_VERSION constant
* Made class a true singleton

= 0.1 =
* Initial commit

== Upgrade Notice ==

= 1.1.6 =
* Maintenance release.

= 1.1.5 =
* Fix issue with wp-forge/container package not being compatible with PHP versions older than 8.0

= 1.1.4 =
* Minor maintenance release

= 1.1.3 =
* Bugfix and security release.

= 1.1.2 =
* Fix issue where posts were hidden from users when searching within the WordPress admin area.

= 1.1.1 =
Updated to work with WordPress 5.5.

= 1.1 =
Implemented ability to hide from search engines as well.

= 1.0.2 =

Plugin updated to reflect that is works with WordPress version 5.4.

= 1.0.1 =
Plugin updated to reflect that it works with WordPress version 5.3.2.

= 1.0.0 =
Plugin updated to reflect that it works with WordPress version 4.7. Escaped translated strings for security.

= 0.4.3 =
Plugin updated to reflect that it works with WordPress version 4.5.2

= 0.4.1 =
Plugin updated to reflect that it works with WordPress version 4.2.4

= 0.4 =
Updated plugin structure and how translations are loaded.

= 0.3 =
Made plugin fully translatable and made some minor tweaks to the code.

= 0.2 =
Minor code improvements
