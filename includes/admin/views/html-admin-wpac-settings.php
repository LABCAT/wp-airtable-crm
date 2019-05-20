<?php
/**
 * Admin View: Settings
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$config_exists = falsel
?>
<div class="wrap woocommerce">
    <form method="post" id="mainform" action="" enctype="multipart/form-data">
        <h3>Configuration</h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpac_config">
                            Airpress Config
                        </label>
                    </th>
                    <td>
                        <select name="wpac_config">
                            <option value="">No Config Selected</option>
                            <?php
                                foreach ( $configs as $config ) {
                                    $sel = '';
                                    $config_str = 'Config-' . $config[ 'id' ];
                                    if( $wpac_config ==  $config_str ){
                                        $sel = ' selected="selected"';
                                        $config_exists = true;
                                    }
                                    echo '<option value="Config-' . $config[ 'id' ] . '"' . $sel . '>' . $config[ 'name' ] . '</option>';
                                }
                            ?>
                        </select>
                        <span class="description">The <a href="/wp-admin/admin.php?page=airpress_cx" target="_blank">Airpress configuration</a> that will be used to connect to the Airtable API.</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="wpac_table_name">
                            Airtable Base/Table Name
                        </label>
                    </th>
                    <td>
                        <input type="text" name="wpac_table_name" value="<?php echo $wpac_table_name; ?>"/>
                        <span class="description">The name of the table in <strong>Airtable</strong> that users will be synchronised with. Note: this value is <strong>case sensitive</strong>.</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <h3>User Roles</h3>
        <span class="description">Airtable Synchronisation will only be applied to users who have a role that from this list of selected roles.</span>
        <table class="form-table">
            <tbody>
                <?php
                    $col_count = 0;
                    $total_roles = count( $wp_roles->roles );
                    foreach ( $wp_roles->roles as $role_key => $role_values ) {
                        $name = $role_values[ 'name' ];
                        $checked = ( isset( $wpac_roles[ $role_key ] ) && $wpac_roles[ $role_key ] === 'selected' ) ? 'checked="checked"' : '';
                        if( $col_count % 4 == 0 ){
                            echo '<tr valign="top">';
                        }
                        ?>
                            <td>
                                <label for="<?php echo 'wpac_roles[' . $role_key . ']'; ?>">
                                    <input type="checkbox" name="<?php echo 'wpac_roles[' . $role_key . ']'; ?>" <?php echo $checked; ?>/>
                                    <?php echo $name;  ?>
                                </label>
                            </td>
                        <?php
                        if( $col_count % 4 == 3 || $col_count == $total_roles ){
                            echo '</tr>';
                        }
                        $col_count++;
                    }
                ?>
            </tbody>
        </table>
        <h2>User Fields - Airtable Mapping</h2>
        <?php
            if( $wpac_config && $config_exists ) {
            ?>
                <h3>Main User Table</h3>
                <table class="form-table">
                    <tbody>
                        <?php
                            foreach ( $this->user_table_columns() as $column ) {
                                $value = isset( $current_users_mapping[ $column ] ) ? $current_users_mapping[ $column ] : '';
                                ?>
                                <tr valign="top">
                                    <th scope="row">
                                        <label for="<?php echo 'users[' . $column . ']'; ?>">
                                            <?php echo $column; ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="<?php echo 'users[' . $column . ']'; ?>" value="<?php echo $value; ?>"/>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                <h3>User Meta Fields</h3>
                <table class="form-table">
                    <tbody>
                        <?php
                            foreach ( $this->user_meta_keys() as $column ) {
                                $value = isset( $current_usermeta_mapping[ $column ] ) ? $current_usermeta_mapping[ $column ] : '';
                                ?>
                                <tr valign="top">
                                    <th scope="row">
                                        <label for="<?php echo 'usermeta[' . $column . ']'; ?>">
                                            <?php echo $column; ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="<?php echo 'usermeta[' . $column . ']'; ?>" value="<?php echo $value; ?>"/>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            <?php
            }
            else {
                echo '<p class="message"><strong>You need to select an <a href="/wp-admin/admin.php?page=airpress_cx" target="_blank">Airpress configuration</a> and "Airtable Base/Table Name" from above before you can set up your field mappings.</strong></p>';
            }
        ?>
        <p class="submit">
            <input name="save_wpac_settings" class="button-primary save-button" type="submit" value="Save Changes" />
            <?php wp_nonce_field( 'wpac-settings' ); ?>
        </p>
    </form>
</div>
