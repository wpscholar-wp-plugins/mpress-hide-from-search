<?php

namespace HideFromSearch;

/**
 * Class MetaBox
 *
 * @package HideFromSearch
 */
class MetaBox {

	/**
	 * Register WordPress hooks for the meta box.
	 */
	public static function initialize() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
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
		}
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

		foreach ( $meta_keys as $meta_key ) {
			if ( isset( $_POST[ $nonce_name ] ) && wp_verify_nonce( $_POST[ $nonce_name ], $plugin_file ) ) {
				$post_type_object = get_post_type_object( $post->post_type );
				if ( current_user_can( $post_type_object->cap->edit_post, $id ) ) {
					if ( empty( $_POST[ $meta_key ] ) ) {
						delete_post_meta( $id, $meta_key );
					} else {
						update_post_meta( $id, $meta_key, 1 );
					}
				}
			}
		}
	}

}
