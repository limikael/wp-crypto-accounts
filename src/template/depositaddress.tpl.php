<b>Deposit Address</b><br/>
<a href="<?php echo $depositLink; ?>"><?php echo $depositAddress; ?></a><br/><br/>
<div id='blockchainaccounts-deposit-qrcode'></div>
<script>
	jQuery(function($) {
		console.log("hello");
		$('#blockchainaccounts-deposit-qrcode').qrcode({
		    "size": 300,
   			"color": "#3a3",
    		"text": "<?php echo $depositLink; ?>"
		});
	});
</script>