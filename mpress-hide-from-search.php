<?php
/**
 * Main plugin class.
 *
 * @package HideFromSearch
 */

/*
 * Plugin Name: Hide from Search
 * Plugin URI: https://wpscholar.com/wordpress-plugins/mpress-hide-from-search/
 * Description: Hide individual posts, pages and other post types from the default WordPress search functionality.
 * Version: 1.0.2
 * Author: Micah Wood
 * Author URI: https://wpscholar.com/
 * Requires at least: 3.2
 * Requires PHP: 5.3
 * Text Domain: mpress-hide-from-search
 * Domain Path: languages
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2014-2020 by Micah Wood - All rights reserved.
 */

namespace wpscholar\HideFromSearch;

/**
 * Class Plugin
 */
class Plugin {

	/**
	 * Meta key
	 *
	 * @var string
	 */
	protected static $meta_key = '_mpress_hide_from_search';

	/**
	 * Custom nonce
	 *
	 * @var string
	 */
	protected static $nonce_name = 'mpress_hide_from_search_nonce';

	/**
	 * Setup plugin
	 */
	public static function initialize() {

		load_plugin_textdomain( 'mpress-hide-from-search', false, dirname( __FILE__ ) . '/languages' );

		if ( is_admin() ) {

			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
			add_action( 'save_post', array( __CLASS__, 'save_post' ) );

		} else {

			add_filter( 'posts_where', array( __CLASS__, 'exclude_posts_where' ) );

		}
	}

	/**
	 * Add meta boxes to all public post types that are searchable
	 *
	 * @internal
	 */
	public static function add_meta_boxes() {

		$post_types = get_post_types(
			array(
				'public'              => true,
				'exclude_from_search' => false,
			)
		);

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'mpress-hide-from-search',
				esc_html__( 'Hide from Search', 'mpress-hide-from-search' ),
				array( __CLASS__, 'render_meta_box_content' ),
				$post_type,
				'side',
				'low'
			);
		}

	}

	/**
	 * Render meta box content
	 *
	 * @internal
	 */
	public static function render_meta_box_content() {
		$hidden        = (bool) get_post_meta( get_the_ID(), self::$meta_key, true );
		$post_type_obj = get_post_type_object( get_post_type() );
		wp_nonce_field( __FILE__, self::$nonce_name );
		echo '<input type="checkbox" name="' . esc_attr( self::$meta_key ) . '" value="1"' . checked( $hidden, true, false ) . ' /> ';
		printf(
		/* translators: post type */
			esc_html__( 'Hide this %s from WordPress search', 'mpress-hide-from-search' ),
			esc_html( strtolower( $post_type_obj->labels->singular_name ) )
		);
	}

	/**
	 * Callback for saving post meta
	 *
	 * @param int $post_id The post ID.
	 *
	 * @internal
	 */
	public static function save_post( $post_id ) {
		if ( isset( $_POST[ self::$nonce_name ] ) && wp_verify_nonce( $_POST[ self::$nonce_name ], __FILE__ ) ) {
			if ( current_user_can( 'edit_post', $post_id ) ) {
				if ( empty( $_POST[ self::$meta_key ] ) ) {
					delete_post_meta( $post_id, self::$meta_key );
				} else {
					update_post_meta( $post_id, self::$meta_key, 1 );
				}
			}
		}
	}

	/**
	 * Filter the 'where' clause to exclude any hidden posts from search
	 *
	 * @param string $where The SQL `where` clause.
	 *
	 * @return string
	 * @internal
	 */
	public static function exclude_posts_where( $where ) {

		/**
		 * Reference to $wpdb global
		 *
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		if ( is_search() ) {
			$where .= $wpdb->prepare( "AND ID NOT IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d )", self::$meta_key, 1 );
		}

		return $where;
	}

}

add_action( 'init', __NAMESPACE__ . '\\Plugin::initialize' );
