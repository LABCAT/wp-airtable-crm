<?php
/**
 * Admin View: Settings
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="wrap woocommerce">
    <form method="post" id="mainform" action="" enctype="multipart/form-data">
        <h2>User Fields - Airtable Mapping</h2>
        <h4>Main User Table</h4>
        <table class="form-table">
            <tbody>
                <?php
                    foreach ( $this->user_table_columns() as $column ) {
                        ?>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo 'users[' . $column . ']'; ?>">
                                    <?php echo $column; ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="<?php echo 'users[' . $column . ']'; ?>" value=""/>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
        <h4>User Meta Fields</h4>
        <table class="form-table">
            <tbody>
                <?php
                    foreach ( $this->user_meta_keys() as $column ) {
                        ?>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo 'usermeta[' . $column . ']'; ?>">
                                    <?php echo $column; ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="<?php echo 'usermeta[' . $column . ']'; ?>" value=""/>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
        <p class="submit">
            <input name="save_wpac_settings" class="button-primary save-button" type="submit" value="Save Changes" />
            <?php wp_nonce_field( 'wpac-settings' ); ?>
        </p>
    </form>
</div>
