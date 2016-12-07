<form method="get" action="options-general.php">
	<input type="hidden" name="page" value="blockchainaccounts_settings" />
	<input type="hidden" name="tab" value="accounts" />
	<p>Show account history for a particular account.</p>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">Account</th>
            <td>
                <input type="text"
                    name="type"
                    value="<?php echo esc_attr($type); ?>"
                    class="regular-text"/>
                <input type="text"
                    name="id"
                    value="<?php echo esc_attr($id); ?>"
                    class="regular-text"/>
                <p class="description">
                    Entity type and id for the account.
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Balance</th>
            <td>
            	<p><?php echo $balance; ?></p>
            </td>
        </tr>
    </table>

    <?php submit_button("Show transactions"); ?>

	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th><b>Notice</b></th>
				<th><b>Entity</b></th>
				<th><b>When</b></th>
				<th><b>Amount</b></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($transactions as $transaction) { ?>
				<tr>
					<td><?php echo $transaction["notice"];?></td>
					<td><?php echo $transaction["entity"];?></td>
                    <td><?php echo $transaction["when"];?></td>
					<td><?php echo $transaction["amount"];?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

</form>
