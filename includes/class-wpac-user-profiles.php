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
            add_action( 'edit_user_profile_update', [ $this, 'profile_update'], 9999 );
            add_action( 'personal_options_update', [ $this, 'profile_update'], 9999 );
        }

        public function after_profile_update( $user_id, $old_user_data ){
            $airtable_id = get_user_meta( $user_id, '_airtable_id', true );
            $user_login = $old_user_data->user_login;
            $record = $this->update_airtable_record( $airtable_id, $user_login );
        }

        public function profile_update( $user_id ){

        }

        public function update_airtable_record( $airtable_id, $user_login ){
            $record = new ArrayObject();
            $query = new AirpressQuery( "Employer", 0 );
    		$employers = new AirpressCollection( $query );

            if( $airtable_id ){
                $user['id'] = $airtable_id;
                $record = new AirpressRecord( $user, $employers );
            }

            // no record yet - user likely doesn't have an airtable id
            if( ! sizeof( $record ) ){
                $record = $employers->lookup( 'Website user_login', $user_login );
            }

            // still no record - user most likely doesn't exist in airtable
            if( ! sizeof( $record ) ){

            }

            $user_fields = [
                'Website first_name' => 'Bob',
                'Website last_name' => 'Burgerman'
            ];

            $record->update( $user_fields );
        }

    }

}

new WPAC_User_Profiles();
