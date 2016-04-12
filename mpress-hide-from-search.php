<?php

/*
 * Plugin Name: mPress Hide from Search
 * Plugin URI: http://wpscholar.com/wordpress-plugins/mpress-hide-from-search/
 * Description: This plugin allows you to hide individual posts, pages and other post types from the default WordPress search functionality.
 * Version: 0.4.2
 * Author: Micah Wood
 * Author URI: http://wpscholar.com/
 * Text Domain: mpress-hide-from-search
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2014-2016 by Micah Wood - All rights reserved.
 */

if ( ! class_exists( 'mPress_Hide_From_Search' ) ) {

	/**
	 * Class mPress_Hide_From_Search
	 */
	class mPress_Hide_From_Search {

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public static $version = '0.4.2';

		/**
		 * @var mPress_Hide_From_Search
		 */
		private static $instance;

		/**
		 * Meta key
		 *
		 * @var string
		 */
		protected $meta_key = '_mpress_hide_from_search';

		/**
		 * Custom nonce
		 *
		 * @var string
		 */
		protected $nonce_name = 'mpress_hide_from_search_nonce';

		/**
		 * Get singleton instance
		 *
		 * @return mPress_Hide_From_Search
		 */
		public static function get_instance() {
			return isset( self::$instance ) ? self::$instance : new self();
		}

		/**
		 * Setup plugin
		 */
		private function __construct() {
			self::$instance = $this;
			add_action( 'init', array( $this, 'init' ) );
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( $this, 'meta_box' ) );
				add_action( 'save_post', array( $this, 'save_meta' ) );
			} else {
				add_filter( 'posts_where', array( $this, 'exclude_posts_where' ) );
			}
		}

		/**
		 * Load plugin translations
		 */
		public function init() {
			load_plugin_textdomain( 'mpress-hide-from-search', false, dirname( __FILE__ ) . '/languages' );
		}

		/**
		 * Add meta box to all public post types that are searchable
		 */
		public function meta_box() {
			$post_types = get_post_types(
				array(
					'public'              => true,
					'exclude_from_search' => false,
				)
			);
			foreach ( $post_types as $post_type ) {
				add_meta_box(
					'mpress-hide-from-search',
					__( 'Hide from Search', 'mpress-hide-from-search' ),
					array( $this, 'meta_box_content' ),
					$post_type,
					'side',
					'low'
				);
			}
		}

		/**
		 * Render meta box
		 */
		public function meta_box_content() {
			global $post;
			$hidden        = get_post_meta( $post->ID, $this->meta_key, true );
			$post_type_obj = get_post_type_object( $post->post_type );
			wp_nonce_field( __FILE__, $this->nonce_name );
			echo '<input type="checkbox" name="' . $this->meta_key . '" value="1"' . checked( $hidden, true, false ) . ' /> ';
			printf(
				__( 'Hide this %s from WordPress search', 'mpress-hide-from-search' ),
				strtolower( $post_type_obj->labels->singular_name )
			);
		}

		/**
		 * Callback for saving meta
		 *
		 * @param int $post_id
		 */
		public function save_meta( $post_id ) {
			if ( isset( $_POST[ $this->nonce_name ] ) && wp_verify_nonce( $_POST[ $this->nonce_name ], __FILE__ ) ) {
				if ( current_user_can( 'edit_post', $post_id ) ) {
					if ( empty( $_POST[ $this->meta_key ] ) ) {
						delete_post_meta( $post_id, $this->meta_key );
					} else {
						update_post_meta( $post_id, $this->meta_key, 1 );
					}
				}
			}
		}

		/**
		 * Filter the 'where' clause to exclude any hidden posts from search
		 *
		 * @param string $where
		 *
		 * @return string
		 */
		public function exclude_posts_where( $where ) {
			/**
			 * Reference to $wpdb global
			 *
			 * @var wpdb $wpdb
			 */
			global $wpdb;
			if ( is_search() ) {
				$where .= $wpdb->prepare(
					"AND ID NOT IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d )",
					$this->meta_key,
					1
				);
			}

			return $where;
		}

	}

	add_action( 'plugins_loaded', array( 'mPress_Hide_From_Search', 'get_instance' ) );

}