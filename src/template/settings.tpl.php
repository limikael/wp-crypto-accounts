<?php if (isset($message)) { ?>
    <div class="updated settings-error notice is-dismissible">
        <p><b><?php echo $message; ?></b></p>
    </div>
<?php } ?>

<?php if (isset($error)) { ?>
    <div class="updated settings-error notice is-dismissible">
        <p><b><?php echo $error; ?></b></p>
    </div>
<?php } ?>

<div class="wrap">
    <h2>Crypto Accounts</h2>

    <h3>About</h3>
    <p>
        This plugin enables all users to deposit and withdraw bitcoins from a personal
        account.
    </p>

    <h3>Setup</h3>
    <form method="post" action="options.php">
        <?php settings_fields( 'blockchainaccounts' ); ?>
        <?php do_settings_sections( 'blockchainaccounts' ); ?>

        <p>
            You need to set up this site to talk to a wallet provider.
        </p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Wallet service</th>
                <td>
                    <select name="blockchainaccounts_wallet_type"
                            id="blockchainaccounts_wallet_type">
                        <option value="">None (disabled)</option>
                        <option value="block_io"
                            <?php if (get_option("blockchainaccounts_wallet_type")=="block_io") { ?>
                                selected
                            <?php } ?>
                        >block.io</option>
                        <!--<option value="blocktrail_io">blocktrail.com</option>-->
                    </select>
                    <p class="description">
                        Select the wallet provider you are using.
                    </p>
                </td>
            </tr>

            <tr valign="top" style="display: none" id="blockchainaccounts_block_io_api_key">
                <th scope="row">Block.io api key</th>
                <td>
                    <input type="text"
                        name="blockchainaccounts_block_io_api_key"
                        value="<?php echo esc_attr(get_option("blockchainaccounts_block_io_api_key")); ?>" 
                        class="regular-text"/>
                    <p class="description">
                        Enter your API key from block.io. <br/>
                        You can find it on the Dashboard by clicking "Show API Keys".
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>