<?php

/**
 * Plugin Name:     Mai Lists
 * Plugin URI:      https://bizbudding.com/mai-design-pack/
 * Description:     Simple and beautiful icon lists and responsive icon feature grids.
 * Version:         1.1.2
 *
 * Author:          BizBudding
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Must be at the top of the file.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Main Mai_Lists_Plugin Class.
 *
 * @since 0.1.0
 */
final class Mai_Lists_Plugin {
	/**
	 * @var   Mai_Lists_Plugin The one true Mai_Lists
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main Mai_Lists_Plugin Instance.
	 *
	 * Insures that only one instance of Mai_Lists_Plugin exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    Mai_Lists_Plugin::setup_constants() Setup the constants needed.
	 * @uses    Mai_Lists_Plugin::includes() Include the required files.
	 * @uses    Mai_Lists_Plugin::hooks() Activate, deactivate, etc.
	 * @see     Mai_Lists_Plugin()
	 * @return  object | Mai_Lists_Plugin The one true Mai_Lists_Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_Lists_Plugin;
			// Methods.
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-lists' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-lists' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'MAI_LISTS_VERSION' ) ) {
			define( 'MAI_LISTS_VERSION', '1.1.2' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_LISTS_PLUGIN_DIR' ) ) {
			define( 'MAI_LISTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Classes Path.
		if ( ! defined( 'MAI_LISTS_CLASSES_DIR' ) ) {
			define( 'MAI_LISTS_CLASSES_DIR', MAI_LISTS_PLUGIN_DIR . 'classes/' );
		}

		// Plugin Includes Path.
		if ( ! defined( 'MAI_LISTS_INCLUDES_DIR' ) ) {
			define( 'MAI_LISTS_INCLUDES_DIR', MAI_LISTS_PLUGIN_DIR . 'includes/' );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MAI_LISTS_PLUGIN_URL' ) ) {
			define( 'MAI_LISTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_LISTS_PLUGIN_FILE' ) ) {
			define( 'MAI_LISTS_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'MAI_LISTS_BASENAME' ) ) {
			define( 'MAI_LISTS_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function includes() {
		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';
		// Classes.
		foreach ( glob( MAI_LISTS_CLASSES_DIR . '*.php' ) as $file ) { include $file; }
		// Includes.
		foreach ( glob( MAI_LISTS_INCLUDES_DIR . '*.php' ) as $file ) { include $file; }
		// Blocks.
		include MAI_LISTS_PLUGIN_DIR . 'blocks/mai-list/block.php';
		include MAI_LISTS_PLUGIN_DIR . 'blocks/mai-list-item/block.php';
	}

	/**
	 * Run the hooks.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'plugins_loaded',    [ $this, 'updater' ], 12 );
		add_action( 'after_setup_theme', [ $this, 'run' ] ); // Plugins loaded is too early to check for engine version.
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @since 0.1.0
	 *
	 * @uses https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return void
	 */
	public function updater() {
		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		// Setup the updater.
		$updater = PucFactory::buildUpdateChecker( 'https://github.com/maithemewp/mai-lists/', __FILE__, 'mai-lists' );

		// Maybe set github api token.
		if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
			$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
		}

		// Add icons for Dashboard > Updates screen.
		if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
			$updater->addResultFilter(
				function ( $info ) use ( $icons ) {
					$info->icons = $icons;
					return $info;
				}
			);
		}
	}

	/**
	 * Runs plugin if Mai Engine is active.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function run() {
		if ( ! class_exists( 'Mai_Engine' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice' ] );
			return;
		}

		if ( ! function_exists( 'mai_get_version' ) || ! version_compare( mai_get_version(), '2.21', '>' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice' ] );
			return;
		}

		new Mai_List_Blocks;
	}

	/**
	 * Displays admin notice.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function admin_notice() {
		printf( '<div class="notice notice-warning"><p>%s</p></div>', __( 'Mai Lists requires Mai Engine plugin version 2.21.0 or later. Please install/upgrade now to use the Mai List block.', 'mai-lists' ) );
	}
}

/**
 * The main function for that returns Mai_Lists_Plugin
 *
 * The main function responsible for returning the one true Mai_Lists_Plugin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Lists_Plugin(); ?>
 *
 * @since 0.1.0
 *
 * @return object|Mai_Lists_Plugin The one true Mai_Lists_Plugin Instance.
 */
function mai_lists() {
	return Mai_Lists_Plugin::instance();
}

// Get Mai_Lists_Plugin Running.
mai_lists();
