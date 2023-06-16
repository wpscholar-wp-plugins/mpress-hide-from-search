<?php
/**
 * Hide from Search
 *
 * @package           HideFromSearch
 * @author            Micah Wood
 * @copyright         Copyright 2020-2023 by Micah Wood - All rights reserved.
 * @license           GPL2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Hide from Search
 * Plugin URI:        https://wordpress.org/plugins/mpress-hide-from-search/
 * Description:       Hide individual WordPress pages from search engines and/or WordPress search results.
 * Version:           1.1.6
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * Author:            Micah Wood
 * Author URI:        https://wpscholar.com
 * Text Domain:       mpress-hide-from-search
 * Domain Path:       /languages
 * License:           GPL V2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

define( 'HIDE_FROM_SEARCH_VERSION', '1.1.6' );
define( 'HIDE_FROM_SEARCH_FILE', __FILE__ );

require __DIR__ . '/vendor/autoload.php';

// Check plugin requirements
global $pagenow;
if ( 'plugins.php' === $pagenow ) {
	$plugin_check = new WP_Forge_Plugin_Check( __FILE__ );

	$plugin_check->min_php_version = '7.4';
	$plugin_check->min_wp_version  = '6.0';
	$plugin_check->check_plugin_requirements();
}

require __DIR__ . '/includes/Plugin.php';
