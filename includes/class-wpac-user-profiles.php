<?php
/**
 * WPAC_User_Profiles
 *
 * @class     WPAC_User_Profiles
 * @version   1.0.0
 * @package   WP_Airtable_CRM
 * @category  Class
 * @author   My Site Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPAC_User_Profiles', false ) ) {

    /**
     * WPAC_User_Profiles Class.
     *
     */
    class WPAC_User_Profiles {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'profile_update', [ $this, 'after_profile_update'], 9999, 2 );
        }

        public function after_profile_update( $user_id, $old_user_data ){
            $airtable_id = get_user_meta( $user_id, '_airtable_id', true );
            $user_data = get_userdata( $user_id );
            $record = $this->update_airtable_record( $airtable_id, $user_data );
        }

        public function update_airtable_record( $airtable_id, $user_data ){
            $record = new ArrayObject();
            $query = new AirpressQuery( "Employer", 0 );
            $employers = new AirpressCollection( $query );
            if( $airtable_id ){
                $user['id'] = $airtable_id;
                $record = new AirpressRecord( $user, $employers );
            }

            // no record yet - user likely doesn't have an airtable id stored in usermeta
            // lookup via email
            if( ! $record || ! sizeof( $record ) ){

                $airtable_field = $this->get_mapped_fields( 'user_email' );
                if( ! empty( $airtable_field ) ){
                    $record = $employers->lookup( $airtable_field , $user_data->user_email );
                }
            }

            // still no record - user may have changed their email so check by user login
            if( ! $record || ! sizeof( $record ) ){

                $airtable_field = $this->get_mapped_fields( 'user_login' );
                if( ! empty( $airtable_field ) ){
                    $record = $employers->lookup( $airtable_field , $user_data->user_login );
                }
            }

            $user_fields = [];
            $mapped_fields = $this->get_mapped_fields();

            foreach( $mapped_fields as $wp_field => $airtable_field  ){
                if( isset( $user_data->$wp_field ) ){
                    $user_fields[ $airtable_field ] = (string)$user_data->$wp_field;
                }
                else {
                    $user_fields[ $airtable_field ] = (string)get_user_meta( $user_data->ID, $wp_field, true );
                }
            }


            // still no record - now we need to create one
            if( ! $record || ! sizeof( $record ) ){
                $new_user[ 'fields' ] = $user_fields;
                $record = $employers->createRecord( $new_user[ 'fields' ] );
            }

            // test the record exists just in case it still hasn't been found or created
            if( $record && sizeof( $record ) ){
                $record->update( $user_fields );

                if( ! $airtable_id ){
                    update_user_meta( $user_id, '_airtable_id', $record->record_id() );
                }
            }

        }


        public function get_mapped_fields( $single_field = '' ){
            $mapped_fields = [];
            $current_users_mapping = get_option( 'users_table_mapping' );
            if( is_array( $current_users_mapping ) ){
                $mapped_fields = array_merge( $mapped_fields, $current_users_mapping );
            }

            $current_usermeta_mapping = get_option( 'usermeta_table_mapping' );
            if( is_array( $current_usermeta_mapping ) ){
                $mapped_fields = array_merge( $mapped_fields, $current_usermeta_mapping );
            }

            if( $single_field ){
                if( isset( $mapped_fields[ $single_field ] ) ){
                    return $mapped_fields[ $single_field ];
                }
                else {
                    return [];
                }
            }
            return $mapped_fields;
        }

    }

}

new WPAC_User_Profiles();
