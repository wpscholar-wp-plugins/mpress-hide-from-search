<?php

namespace HideFromSearch;

use WP_Forge\Container\Container;
use WP_Forge\UpgradeHandler\UpgradeHandler;

/**
 * Class Plugin
 *
 * @package HideFromSearch
 */
class Plugin {

	/**
	 * Plugin container.
	 *
	 * @var Container
	 */
	protected static $container;

	/**
	 * Static class constructor.
	 */
	public static function initialize() {
		self::setUpContainer();
		self::registerFields();
		self::setUpHooks();

		if ( is_admin() ) {
			self::handleUpgrades();
			MetaBox::initialize();
		}
	}

	/**
	 * Get the plugin container.
	 *
	 * @return Container
	 */
	public static function container() {
		return self::$container;
	}

	/**
	 * Handle plugin upgrade routines.
	 *
	 * @throws \WP_Forge\Container\NotFoundException If requested property doesn't exist.
	 */
	public static function handleUpgrades() {

		$container = self::container();

		// Handle plugin upgrade routines
		$upgrade_handler = new UpgradeHandler(
			$container->get( 'dir' ) . '/upgrades',
			$container->get( 'db_version' ),
			$container->get( 'version' )
		);

		$did_upgrade = $upgrade_handler->maybe_upgrade();
		if ( $did_upgrade ) {
			update_option( 'hide_from_search_plugin_version', $container->get( 'version' ), true );
		}
	}

	/**
	 * Hide page/post from search engines.
	 */
	public static function hideFromSearchEngines() {
		if ( is_admin() || ! get_option( 'blog_public' ) ) {
			return;
		}
		if ( (bool) get_post_meta( get_the_ID(), '_hide_from_search_engines', true ) ) {
			echo '<meta name="robots" content="noindex,nofollow"/>';
		}
	}

	/**
	 * Filter the 'where' clause to exclude any hidden posts from search
	 *
	 * @param string $where The SQL `where` clause.
	 *
	 * @return string
	 */
	public static function hideFromWordPressSearch( $where ) {

		global $wpdb;

		if ( is_search() && ! is_admin() ) {
			$where .= $wpdb->prepare( "AND ID NOT IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d )", '_hide_from_search_wp', 1 );
		}

		return $where;
	}

	/**
	 * Load plugin text domain.
	 */
	public static function loadTextDomain() {
		load_plugin_textdomain( 'hide-from-search', false, dirname( HIDE_FROM_SEARCH_FILE ) . '/languages' );
	}

	/**
	 * Register fields.
	 */
	public static function registerFields() {

		// Up to WP 5.4
		remove_filter( 'register_meta_args', '_wp_register_meta_args_whitelist' );
		// WP 5.5+
		remove_filter( 'register_meta_args', '_wp_register_meta_args_allowed_list' );

		register_meta(
			'post',
			'_hide_from_search_wp',
			array(
				'type'              => 'boolean',
				'description'       => esc_html__( 'Hide from WordPress search', 'mpress-hide-from-search' ),
				'single'            => true,
				'sanitize_callback' => function ( $value ) {
					return filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
				},
				'auth_callback'     => function ( $allowed, $meta_key, $object_id, $user_id ) {
					return user_can( $user_id, 'edit_post', $object_id );
				},
				'plugin'            => 'hide-from-search',
				'should_render'     => function () {
					return ! get_post_type_object( get_post_type() )->exclude_from_search;
				},
			)
		);

		register_meta(
			'post',
			'_hide_from_search_engines',
			array(
				'type'              => 'boolean',
				'description'       => esc_html__( 'Hide from search engines', 'mpress-hide-from-search' ),
				'single'            => true,
				'sanitize_callback' => function ( $value ) {
					return filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
				},
				'auth_callback'     => function ( $allowed, $meta_key, $object_id, $user_id ) {
					return user_can( $user_id, 'edit_post', $object_id );
				},
				'plugin'            => 'hide-from-search',
				'should_render'     => '__return_true',
			)
		);

	}

	/**
	 * Set up plugin container.
	 */
	public static function setUpContainer() {

		self::$container = new Container(
			array(
				'db_version' => get_option( 'hide_from_search_plugin_version', '0.1' ),
				'dir'        => dirname( HIDE_FROM_SEARCH_FILE ),
				'file'       => HIDE_FROM_SEARCH_FILE,
				'nonce_name' => 'hide_from_search_nonce',
				'version'    => HIDE_FROM_SEARCH_VERSION,
			)
		);

		self::$container->set(
			'fields',
			self::$container->computed(
				function () {
					$keys = get_registered_meta_keys( 'post', '' );

					return array_filter(
						$keys,
						function ( $value ) {
							return is_array( $value ) && isset( $value['plugin'] ) && 'hide-from-search' === $value['plugin'];
						}
					);
				}
			)
		);

	}

	/**
	 * Set up hooks.
	 */
	public static function setUpHooks() {
		add_action( 'plugins_loaded', array( __CLASS__, 'loadTextDomain' ) );
		add_action( 'wp_head', array( __CLASS__, 'hideFromSearchEngines' ), 5 );
		add_filter( 'posts_where', array( __CLASS__, 'hideFromWordPressSearch' ) );
	}

}

Plugin::initialize();
