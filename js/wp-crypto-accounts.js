jQuery(function($) {
	function updateCryptoAccountSettings() {
		var withdrawProcessing = $("#blockchainaccounts_withdraw_processing").val();
		switch ($("#blockchainaccounts_wallet_type").val()) {
			case "block_io":
				$("#blockchainaccounts_block_io_api_key").show();

				if (withdrawProcessing == "auto")
					$("#blockchainaccounts_block_io_password").show();

				else
					$("#blockchainaccounts_block_io_password").hide();
				break;

			default:
				$("#blockchainaccounts_block_io_api_key").hide();
				$("#blockchainaccounts_block_io_password").hide();
				break;
		}
	}

	// Update account settings in admin.
	$(document).ready(function() {
		updateCryptoAccountSettings();

		$("#blockchainaccounts_wallet_type").change(updateCryptoAccountSettings);
		$("#blockchainaccounts_withdraw_processing").change(updateCryptoAccountSettings);
	});

	// Balance update notifications.
	$(document).ready(function() {
		if (!$(".bca-balance").size())
			return;

		function fromSatoshi(denomination, amount) {
			switch (denomination) {
				case "satoshi":
					return amount;
					break;

				case "bits":
					return amount / 100;
					break;

				case "mbtc":
					return amount / 100000;
					break;

				case "btc":
					return amount / 100000000;
					break;

				default:
					throw new Error("Unknown denomination: " + denomination);
					return;
			}
		}

		function onBalanceUpdateSuccess(data) {
			console.log("Got balance update");
			$('.bca-balance').each(function(el) {
				$(this).text(
					fromSatoshi($(this).attr("denomination"), data.balance) + " " +
					$(this).attr("denomination")
				);
			});

			$('.bca-confirming').each(function(el) {
				if (data.confirming) {
					$(this).text(
						" (+ " +
						fromSatoshi($(this).attr("denomination"), data.confirming) + " " +
						$(this).attr("denomination") +
						")"
					);
				} else {
					$(this).text("");
				}
			});

			BCA_ACCOUNT_INFO = data;
			setTimeout(requestBalanceUpdate, 1000);
		}

		function onBalanceUpdateError() {
			console.log("balance update error");
		}

		function requestBalanceUpdate() {
			console.log("Requesting balance update...");

			var data = {
				action: "bca_balance_update",
				balance: BCA_ACCOUNT_INFO.balance,
				confirming: BCA_ACCOUNT_INFO.confirming
			};

			$.ajax({
				url: ajaxurl,
				data: data,
				dataType: "json",
				success: onBalanceUpdateSuccess,
				error: onBalanceUpdateError
			});
		}

		setTimeout(requestBalanceUpdate, 1000);
	});
});