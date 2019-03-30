<?php
/**
 * Plugin Name: WP Airtable CRM
 * Plugin URI: https://mysite.digital
 * Description:
 * Version: 1.0.0
 * Author: My Site Digital
 * Author URI: https://mysite.digital
 * Requires at least: 5.0.0
 * Tested up to: 5.3
 * Text Domain: wp-airtable-crm
 * Domain Path: /languages/
 * License: GPL2+
 *
 * @package wp-airtable-crm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles core plugin hooks and action setup.
 *
 * @package wp-airtable-crm
 * @since 1.0.0
 */
class WP_Airtable_CRM {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26.0
	 */
	private static $_instance = null;

	/**
	 * REST API instance.
	 *
	 * @var WP_Airtable_CRM_REST_API
	 */
	private $rest_api = null;

	/**
	 * Main WP Airtable CRM Instance.
	 *
	 * Ensures only one instance of WP Airtable CRM is loaded or can be loaded.
	 *
	 * @since  1.26.0
	 * @static
	 * @see WPAC()
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
 	public function __construct() {
 		$this->define_constants();
 		$this->includes();
 	}

 	private function define_constants() {
 		// Define constants.
 		define( 'AIRTABLE_CRM_VERSION', '1.0.0' );
 		define( 'AIRTABLE_CRM_MINIMUM_WP_VERSION', '5.0.0' );
 		define( 'AIRTABLE_CRM_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
 		define( 'AIRTABLE_CRM_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
 		define( 'AIRTABLE_CRM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
 	}

 	public function includes(){
 		include_once( AIRTABLE_CRM_PLUGIN_DIR . '/includes/class-wpac-user-profiles.php' );
 	}
}


/**
 * Main instance of WP Airtable CRM.
 *
 * Returns the main instance of WP Airtable CRM to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WP_Airtable_CRM
 */
function WPAC() {
	return WP_Airtable_CRM::instance();
}

$GLOBALS['airtable_crm'] = WPAC();
