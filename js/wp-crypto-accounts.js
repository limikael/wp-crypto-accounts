jQuery(function($) {
	function updateCryptoAccountSettings() {
		switch ($("#blockchainaccounts_wallet_type").val()) {
			case "block_io":
				$("#blockchainaccounts_block_io_api_key").show();
				break;

			default:
				$("#blockchainaccounts_block_io_api_key").hide();
				break;
		}
	}

	$(document).ready(function() {
		updateCryptoAccountSettings();

		$("#blockchainaccounts_wallet_type").change(updateCryptoAccountSettings);
	});
});