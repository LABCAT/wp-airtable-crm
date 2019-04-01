<?php
/**
 * WPAC_Settings
 *
 * @class     AXI_Xero_Settings
 * @version   1.0.0
 * @package   WP_Airtable_CRM/WPAC_Settings
 * @category  Admin
 * @author   My Site Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPAC_Settings', false ) ) :

/**
 * WPAC_Settings Class.
 *
 */
class WPAC_Settings {


    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_menu', [ $this, 'add_settings_page_to_payments_submenu' ], 999);
    }

    public function add_settings_page_to_payments_submenu(){
        add_users_page(
            'Airtable CRM Settings',
            'Airtable CRM Settings',
            'manage_options',
            'wpac_settings',
            [ $this, 'output' ]
        );
    }

    public function output(){
        echo '<pre>'; print_r($_POST);  echo '</pre>';
        die('sdfsdf');
        // Save settings if data has been posted
        if ( ! empty( $_POST ) && isset( $_POST[ 'save_wpac_settings' ] ) ) {
            $this->save();
        }
        // $record = new ArrayObject();
        // $query = new AirpressQuery( "Employer", 0 );
        // $employers = new AirpressCollection( $query );
        //echo '<pre>'; print_r($this->user_meta_keys());  echo '</pre>';
        include_once( AIRTABLE_CRM_PLUGIN_DIR . '/includes/admin/views/html-admin-wpac-settings.php' );
    }

    public function register_settings() {
        register_setting( 'wpjac-settings', 'users_table_mapping' );
        register_setting( 'wpac-settings', 'usermeta_table_mapping' );
    }

    public function user_table_columns() {
        global $wpdb;
        $col_names = $wpdb->get_col("DESC {$wpdb->users}", 0);
        return $col_names;
    }

    public function user_meta_keys() {
        global $wpdb;
        $sql = "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta";
        $meta_keys = $wpdb->get_results( $sql );
        return array_map(
                    function ( $object ) {
                        return $object->meta_key;
                    },
                    $meta_keys
                );
    }

    public static function save(){
        if ( empty( $_REQUEST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'wpac-settings' ) ) {
            die('Action failed. Please refresh the page and retry.');
        }

        $post_vars = $_POST;

        echo '<pre>'; print_r($post_vars);  echo '</pre>';
        die('sdfsf');

    }

}

endif;

new WPAC_Settings();
