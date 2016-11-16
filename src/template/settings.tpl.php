<?php if (isset($message)) { ?>
    <div class="updated settings-error notice is-dismissible">
        <p><b><?php echo $message; ?></b></p>
    </div>
<?php } ?>

<?php if (isset($error)) { ?>
    <div class="error settings-error notice is-dismissible">
        <p><b><?php echo $error; ?></b></p>
    </div>
<?php } ?>

<div class="wrap">
    <h2>Crypto Accounts</h2>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab <?php echo ($tab=="setup"?"nav-tab-active":"nav-tab"); ?>" 
            href="<?php echo admin_url("options-general.php?page=blockchainaccounts_settings&tab=setup"); ?>">
            Setup
        </a>
        <?php
            if (get_option("blockchainaccounts_wallet_type") &&
                    get_option("blockchainaccounts_withdraw_processing")=="manual") {
        ?>
            <a class="nav-tab <?php echo ($tab=="withdraw"?"nav-tab-active":"nav-tab"); ?>"
                href="<?php 
                    echo admin_url("options-general.php?page=blockchainaccounts_settings&tab=withdraw");
                ?>">
                Pending Withdrawals
            </a>
        <?php } ?>
    </h2>

    <?php 
        switch ($tab) {
            case "setup":
                require __DIR__."/settings_setup.tpl.php";
                break;

            case "withdraw":
                require __DIR__."/settings_withdraw.tpl.php";
                break;
        }
    ?>
</div>