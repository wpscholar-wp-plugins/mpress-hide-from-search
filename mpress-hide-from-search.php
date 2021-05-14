<?php
/**
 * Hide from Search
 *
 * @package           HideFromSearch
 * @author            Micah Wood
 * @copyright         Copyright 2020 by Micah Wood - All rights reserved.
 * @license           GPL2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Hide from Search
 * Plugin URI:        https://wordpress.org/plugins/mpress-hide-from-search/
 * Description:       Hide individual WordPress pages from search engines and/or WordPress search results.
 * Version:           1.1.3
 * Requires PHP:      5.6
 * Requires at least: 5.0
 * Author:            Micah Wood
 * Author URI:        https://wpscholar.com
 * Text Domain:       mpress-hide-from-search
 * Domain Path:       /languages
 * License:           GPL V2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

define( 'HIDE_FROM_SEARCH_VERSION', '1.1.3' );
define( 'HIDE_FROM_SEARCH_FILE', __FILE__ );

require dirname( __FILE__ ) . '/vendor/autoload.php';

// Check plugin requirements
global $pagenow;
if ( 'plugins.php' === $pagenow ) {
	$plugin_check = new WP_Forge_Plugin_Check( __FILE__ );

	$plugin_check->min_php_version = '5.6';
	$plugin_check->min_wp_version  = '5.0';
	$plugin_check->check_plugin_requirements();
}

require dirname( __FILE__ ) . '/includes/Plugin.php';
