<table class="blockchainaccounts-transaction-table" cellpadding="0" cellspacing="0">
	<tr>
		<th>Label</th>
		<th>Time</th>
		<th>Amount</th>
		<th>Balance</th>
	</tr>

	<?php foreach ($transactions as $transaction) { ?>
		<?php $item=$transaction["item"]; ?>
		<tr>
			<td>
				<?php echo $item->id; ?>. 
				<?php echo $item->notice; ?>
			</td>
			<td>
				<?php echo $transaction["time"]; ?>
			</td>
			<td>
				<?php echo $transaction["amount"]; ?>
			</td>
			<td>
				<?php if ($item->state=="complete") { ?>
					<?php echo $transaction["balance"]; ?>
				<?php } else { ?>
					<?php echo $item->state; ?>
				<?php } ?>
			</td>
		</tr>
	<?php } ?>

</table>