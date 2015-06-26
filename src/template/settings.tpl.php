<div class="wrap">
    <h2>Blockchain Accounts</h2>

    <h3>About</h3>

    <p>
        This plugin enables all users to deposit and withdraw bitcoins from a personal
        account.
    </p>

    <h3>Settings</h3>

    <form method="post" action="options.php">
        <?php settings_fields( 'blockchainaccounts' ); ?>
        <?php do_settings_sections( 'blockchainaccounts' ); ?>
        <table class="form-table">
            <?php foreach ($settings as $setting) { ?>
                <tr valign="top">
                    <th scope="row"><?php echo $setting["title"]; ?></th>
                    <td>
                        <input type="text" name="<?php echo $setting["setting"]; ?>" 
                            value="<?php echo esc_attr(get_option($setting["setting"])); ?>" 
                            class="regular-text"/>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <?php submit_button(); ?>
    </form>
</div>