<div class="wrap">
    <h2>Blockchain Accounts</h2>

    <h3>About</h3>
    <p>
        This plugin enables all users to deposit and withdraw bitcoins from a personal
        account.
    </p>

    <h3>Setup</h3>
    <p>
        This section contains information about how to set up blockchain.info and 
        the wpca command.
    </p>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Notification url</th>
            <td>
                <input type="text"
                    value="<?php echo $notificationUrl; ?>" 
                    class="regular-text"/>
                <p class="description">
                    Copy and paste this into the url settings at blockchain.info
                </p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">WPCA api url</th>
            <td>
                <input type="text"
                    value="<?php echo $wpcaUrl; ?>" 
                    class="regular-text"/>
                <p class="description">
                    Use this address as the --url parameter for wpca when managing withdrawals.
                </p>
            </td>
        </tr>
    </table>

    <h3>Settings</h3>

    <form method="post" action="options.php">
        <?php settings_fields( 'blockchainaccounts' ); ?>
        <?php do_settings_sections( 'blockchainaccounts' ); ?>
        <table class="form-table">
            <?php foreach ($settings as $setting) { ?>
                <tr valign="top">
                    <th scope="row"><?php echo $setting["title"]; ?></th>
                    <td>
                        <input type="text" name="<?php echo $setting["setting"]; ?>" 
                            value="<?php echo esc_attr(get_option($setting["setting"])); ?>" 
                            class="regular-text"/>
                        <p class="description">
                            <?php echo $setting["description"]; ?>
                        </p>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <?php submit_button(); ?>
    </form>
</div>