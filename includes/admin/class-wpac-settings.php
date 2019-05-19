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
        // Save settings if data has been posted
        if ( ! empty( $_POST ) && isset( $_POST[ 'save_wpac_settings' ] ) ) {
            $this->save();
        }

        global $wp_roles;
        $configs = get_airpress_configs( "airpress_cx", false );
        $wpac_config = get_option( 'wpac_config' );
        $wpac_table_name = get_option( 'wpac_table_name' );
        $wpac_roles = get_option( 'wpac_roles' );
        $current_users_mapping = get_option( 'users_table_mapping' );
        $current_usermeta_mapping = get_option( 'usermeta_table_mapping' );

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
        $meta_keys = array_map(
                    function ( $object ) {
                        return $object->meta_key;
                    },
                    $meta_keys
                );
        $meta_keys = apply_filters( 'wpac_settings_user_meta_keys', $meta_keys );
        return $meta_keys;
    }

    public static function save(){
        if ( empty( $_REQUEST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'wpac-settings' ) ) {
            die('Action failed. Please refresh the page and retry.');
        }

        $post_vars = $_POST;

        if( isset( $post_vars[ 'wpac_config' ] ) ){
            update_option( 'wpac_config', sanitize_text_field( $post_vars[ 'wpac_config' ] ), 'no' );
        }

        if( isset( $post_vars[ 'wpac_table_name' ] ) ){
            update_option( 'wpac_table_name', sanitize_text_field( $post_vars[ 'wpac_table_name' ] ), 'no' );
        }

        if( isset( $post_vars[ 'wpac_roles' ] ) ){
            global $wp_roles;
            $wpac_roles = [];

            foreach ( $wp_roles->roles as $role ) {
                $name = $role[ 'name' ];
                if( isset( $post_vars[ 'wpac_roles' ][ $name ] ) ){
                    $wpac_roles[ $name ] = 'selected';
                }
                else {
                    $wpac_roles[ $name ] = '';
                }
            }

            update_option( 'wpac_roles', $wpac_roles, 'no' );
        }

        if( isset( $post_vars[ 'users' ] ) ){
            $users_table_mapping = [];

            foreach ( $post_vars[ 'users' ] as $key => $mapped_field ) {
                if( isset( $post_vars[ 'users' ][ $key ] ) && $post_vars[ 'users' ][ $key ] ){
                    $users_table_mapping[ $key ] = sanitize_text_field( $mapped_field );
                }
            }

            update_option( 'users_table_mapping', $users_table_mapping, 'no' );
        }


        if( isset( $post_vars[ 'usermeta' ] ) ){
            $usermeta_table_mapping = [];

            foreach ( $post_vars[ 'usermeta' ] as $key => $mapped_field ) {
                if( isset( $post_vars[ 'usermeta' ][ $key ] ) && $post_vars[ 'usermeta' ][ $key ] ){
                    $usermeta_table_mapping[ $key ] = sanitize_text_field( $mapped_field );
                }
            }

            update_option( 'usermeta_table_mapping', $usermeta_table_mapping, 'no' );
        }

    }

    /* used in WPAC_User_Profiles class */
    public static function get_current_config(){
        $config = new stdClass();

        $config->wpac_config = intval(
            str_replace(
                'Config-',
                '',
                get_option( 'wpac_config' )
            )
        );

        $config->wpac_table_name = get_option( 'wpac_table_name' );

        $selected_roles = [];
        $wpac_roles = get_option( 'wpac_roles' );
        foreach( $wpac_roles as $role_name => $selected ){
            if( $selected ){
                $selected_roles[] = $role_name;
            }
        }
        $config->wpac_roles = $selected_roles;

        return $config;
    }
}

endif;

new WPAC_Settings();
