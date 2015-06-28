<form action="<?php echo $action ?>" method="POST">
	<?php if (isset($success)) { ?>
		<div class="blockchainaccounts-message-success">
			<?php echo $success; ?>
		</div>
	<?php } ?>

	<?php if (isset($error)) { ?>
		<div class="blockchainaccounts-message-error">
			<?php echo $error; ?>
		</div>
	<?php } ?>

	<table class="form-table blockchainaccounts-withdraw">
        <tr valign="top">
            <th scope="row">Address</th>
            <td>
                <input type="text" name="address" 
                    value="<?php echo $address; ?>" 
                    class="regular-text"/>
                <p class="description">
	                Bitcoin address to withdraw to.
                </p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Amount</th>
            <td>
                <input type="text" name="amount" 
                    value="<?php echo $amount; ?>" 
                    class="regular-text"/>
                <p class="description">
	                Amount in <?php echo $denomination; ?> to withdraw.
                </p>
            </td>
        </tr>
	</table>

    <input type="hidden" name="action" value="withdraw"/>
    <input type="hidden" name="afterWithdraw" value="<?php echo $afterWithdraw; ?>"/>
    <input type="hidden" name="denomination" value="<?php echo $denomination; ?>"/>
	<input type="submit" value="Withdraw"/>
</form>