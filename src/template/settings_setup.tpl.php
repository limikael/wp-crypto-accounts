<form method="post" action="options.php">
    <?php settings_fields( 'blockchainaccounts' ); ?>
    <?php do_settings_sections( 'blockchainaccounts' ); ?>

    <p>
        You need to set up this site to talk to a wallet provider.
    </p>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Wallet Service</th>
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
            <th scope="row">Block.io Api Key</th>
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

        <tr valign="top">
            <th scope="row">Withdraw Processing</th>
            <td>
                <select name="blockchainaccounts_withdraw_processing"
                        id="blockchainaccounts_withdraw_processing">
                    <option value="manual"
                        <?php if (get_option("blockchainaccounts_withdraw_processing")=="manual") { ?>
                            selected
                        <?php } ?>
                    >Manual</option>
                    <option value="auto"
                        <?php if (get_option("blockchainaccounts_withdraw_processing")=="auto") { ?>
                            selected
                        <?php } ?>
                    >Automatic</option>
                </select>
                <p class="description">
                    Do the withdrawals need manual approval, or should they be performed automatically?
                </p>
            </td>
        </tr>

        <tr valign="top" style="display: none" id="blockchainaccounts_block_io_password">
            <th scope="row">Block.io Password</th>
            <td>
                <input type="text"
                    name="blockchainaccounts_block_io_password"
                    value="<?php echo esc_attr(get_option("blockchainaccounts_block_io_password")); ?>" 
                    class="regular-text"/>
                <p class="description">
                    Your block.io password is needed in order to perform withdrawals automatically.
                </p>
            </td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>
