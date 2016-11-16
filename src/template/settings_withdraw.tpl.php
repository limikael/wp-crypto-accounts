<form method="post" action="options-general.php?page=blockchainaccounts_settings&tab=withdraw">
	<p>Use this page to actually perform the user withdrawals from the system.</p>
	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<td class='check-column'>
					<input type="checkbox" checked="true"/>
				</td>
				<th><b>User</b></th>
				<th><b>When</b></th>
				<th><b>Amount</b></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($transactions as $transaction) { ?>
				<tr>
					<th scope='row' class='check-column'>
						<input type='checkbox'
							name="transactionIds[]"
							value="<?php echo $transaction["id"]; ?>"
							checked="true"
						/>
					</th>
					<td><?php echo $transaction["user"];?></td>
					<td><?php echo $transaction["when"];?></td>
					<td><?php echo $transaction["amount"];?></td>
				</tr>
			<?php } ?>

			<?php if (!$transactions) { ?>
				<tr>
					<td colspan="4"><i>The are currently no pending transactions.</i></td>
				</tr>				
			<?php } ?>
		</tbody>

		<tfoot>
			<tr>
				<td class='check-column'>
					<input type="checkbox" checked="true"/>
				</td>
				<th><b>User</b></th>
				<th><b>When</b></th>
				<th><b>Amount</b></th>
			</tr>
		</tfoot>
	</table>
	<p>Total amount for withdrawal requests: <?php echo $totalAmount; ?></p>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php echo $passwordLabel; ?></th>
            <td>
                <input type="password"
                    name="password"
                    value="" 
                    class="regular-text"/>
                <p class="description">
                    Enter the password for the wallet service. <br/>
                    This will not be stored on the system.
                </p>
            </td>
        </tr>
    </table>

    <?php submit_button("Perform Withdrawals"); ?>
</form>
