<?php

namespace HideFromSearch;

/**
 * Class MetaBox
 *
 * @package HideFromSearch
 */
class MetaBox {

	/**
	 * Nonce action for bulk edit.
	 */
	const BULK_NONCE_ACTION = 'bulk_hide_from_search';

	/**
	 * Nonce field name for bulk edit.
	 */
	const BULK_NONCE_NAME = 'bulk_hide_from_search_nonce';

	/**
	 * Register WordPress hooks for the meta box.
	 */
	public static function initialize() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( __CLASS__, 'quick_edit' ), 10, 2 );
		add_action( 'bulk_edit_custom_box', array( __CLASS__, 'bulk_edit' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Register meta box with WordPress.
	 */
	public static function add_meta_box() {
		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'hide-from-search',
				esc_html__( 'Hide from Search', 'mpress-hide-from-search' ),
				array( __CLASS__, 'render' ),
				$post_type,
				'side',
				'low'
			);
			add_filter( "manage_{$post_type}_posts_columns", array( __CLASS__, 'add_columns' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( __CLASS__, 'render_column' ), 10, 2 );
		}
	}

	/**
	 * Add a hidden data column used to pass values to the Quick Edit form.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public static function add_columns( $columns ) {
		$columns['hide_from_search'] = '';
		return $columns;
	}

	/**
	 * Render hidden column data attributes consumed by Quick Edit JavaScript.
	 *
	 * @param string $column  The column name.
	 * @param int    $post_id The current post ID.
	 */
	public static function render_column( $column, $post_id ) {
		if ( 'hide_from_search' !== $column ) {
			return;
		}
		$hide_from_wp      = (bool) get_post_meta( $post_id, '_hide_from_search_wp', true );
		$hide_from_engines = (bool) get_post_meta( $post_id, '_hide_from_search_engines', true );
		echo '<span'
			. ' data-hide-from-search-wp="' . ( $hide_from_wp ? '1' : '0' ) . '"'
			. ' data-hide-from-search-engines="' . ( $hide_from_engines ? '1' : '0' ) . '"'
			. '></span>';
	}

	/**
	 * Render fields inside the Quick Edit form.
	 *
	 * @param string $column    The column that triggered the action.
	 * @param string $post_type The current post type.
	 *
	 * @throws \WP_Forge\Container\NotFoundException If requested property doesn't exist.
	 */
	public static function quick_edit( $column, $post_type ) {
		if ( 'hide_from_search' !== $column ) {
			return;
		}
		$container = Plugin::container();
		wp_nonce_field( HIDE_FROM_SEARCH_FILE, $container->get( 'nonce_name' ) );
		$fields = $container->get( 'fields' );
		foreach ( $fields as $meta_key => $field ) {
			if ( ! $field ) {
				continue;
			}
			if ( '_hide_from_search_wp' === $meta_key ) {
				$post_type_obj = get_post_type_object( $post_type );
				if ( $post_type_obj && $post_type_obj->exclude_from_search ) {
					continue;
				}
			}
			echo '<fieldset class="inline-edit-col-left">';
			echo '<div class="inline-edit-col">';
			echo '<label class="alignleft">';
			echo '<input type="checkbox" name="' . esc_attr( $meta_key ) . '" value="1" />';
			echo '<span class="checkbox-title">' . esc_html( $field['description'] ) . '</span>';
			echo '</label>';
			echo '</div>';
			echo '</fieldset>';
		}
	}

	/**
	 * Render fields inside the Bulk Edit form.
	 *
	 * @param string $column    The column that triggered the action.
	 * @param string $post_type The current post type.
	 *
	 * @throws \WP_Forge\Container\NotFoundException If requested property doesn't exist.
	 */
	public static function bulk_edit( $column, $post_type ) {
		if ( 'hide_from_search' !== $column ) {
			return;
		}
		wp_nonce_field( self::BULK_NONCE_ACTION, self::BULK_NONCE_NAME );
		$container = Plugin::container();
		$fields    = $container->get( 'fields' );
		foreach ( $fields as $meta_key => $field ) {
			if ( ! $field ) {
				continue;
			}
			if ( '_hide_from_search_wp' === $meta_key ) {
				$post_type_obj = get_post_type_object( $post_type );
				if ( $post_type_obj && $post_type_obj->exclude_from_search ) {
					continue;
				}
			}
			echo '<fieldset class="inline-edit-col-left">';
			echo '<div class="inline-edit-col">';
			echo '<label>';
			echo '<span class="title">' . esc_html( $field['description'] ) . '</span>';
			echo '<select name="' . esc_attr( $meta_key ) . '">';
			echo '<option value="">' . esc_html__( '— No Change —', 'mpress-hide-from-search' ) . '</option>';
			echo '<option value="1">' . esc_html__( 'Yes', 'mpress-hide-from-search' ) . '</option>';
			echo '<option value="0">' . esc_html__( 'No', 'mpress-hide-from-search' ) . '</option>';
			echo '</select>';
			echo '</label>';
			echo '</div>';
			echo '</fieldset>';
		}
	}

	/**
	 * Enqueue admin scripts and styles on the post list table page.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public static function enqueue_scripts( $hook_suffix ) {
		if ( 'edit.php' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_script(
			'hide-from-search-quick-edit',
			plugins_url( 'assets/js/quick-edit.js', HIDE_FROM_SEARCH_FILE ),
			array( 'jquery', 'inline-edit-post' ),
			HIDE_FROM_SEARCH_VERSION,
			true
		);
		wp_add_inline_style( 'common', '.column-hide_from_search { display: none; }' );
	}

	/**
	 * Render the meta box.
	 *
	 * @throws \WP_Forge\Container\NotFoundException If requested property doesn't exist.
	 */
	public static function render() {
		$container = Plugin::container();

		wp_nonce_field( HIDE_FROM_SEARCH_FILE, $container->get( 'nonce_name' ) );

		$fields = $container->get( 'fields' );
		foreach ( $fields as $meta_key => $field ) {
			if ( $field && $field['should_render']() ) {
				$is_hidden = (bool) get_post_meta( get_the_ID(), $meta_key, true );
				echo '<p>';
				echo '<label>';
				echo '<input type="checkbox" name="' . esc_attr( $meta_key ) . '" value="1"' . checked( $is_hidden, true, false ) . ' />';
				echo esc_html( $field['description'] );
				echo '</label>';
				echo '</p>';
			}
		}
	}

	/**
	 * Save the submitted post meta.
	 *
	 * @param int      $id   The post ID.
	 * @param \WP_Post $post The post instance.
	 *
	 * @throws \WP_Forge\Container\NotFoundException If requested property doesn't exist.
	 */
	public static function save( $id, \WP_Post $post ) {
		$container = Plugin::container();

		$plugin_file = $container->get( 'file' );
		$meta_keys   = array_keys( $container->get( 'fields' ) );
		$nonce_name  = $container->get( 'nonce_name' );

		$is_bulk_edit = isset( $_POST[ self::BULK_NONCE_NAME ] )
			&& wp_verify_nonce( sanitize_key( $_POST[ self::BULK_NONCE_NAME ] ), self::BULK_NONCE_ACTION );

		$is_edit = isset( $_POST[ $nonce_name ] )
			&& wp_verify_nonce( sanitize_key( $_POST[ $nonce_name ] ), $plugin_file );

		if ( ! $is_edit && ! $is_bulk_edit ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! current_user_can( $post_type_object->cap->edit_post, $id ) ) {
			return;
		}

		if ( $is_bulk_edit ) {
			// Tri-state: '' = no change, '1' = hide, '0' = unhide.
			foreach ( $meta_keys as $meta_key ) {
				if ( ! isset( $_POST[ $meta_key ] ) || '' === $_POST[ $meta_key ] ) {
					continue;
				}
				if ( '0' === $_POST[ $meta_key ] ) {
					delete_post_meta( $id, $meta_key );
				} else {
					update_post_meta( $id, $meta_key, 1 );
				}
			}
		} else {
			// Regular edit or Quick Edit: checkbox (present = hide, absent = unhide).
			foreach ( $meta_keys as $meta_key ) {
				if ( empty( $_POST[ $meta_key ] ) ) {
					delete_post_meta( $id, $meta_key );
				} else {
					update_post_meta( $id, $meta_key, 1 );
				}
			}
		}
	}
}
