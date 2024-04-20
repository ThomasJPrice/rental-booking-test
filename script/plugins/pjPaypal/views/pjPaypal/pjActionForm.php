<div class="modal fade" id="modalPaypal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="myModalLabel"><?php __('plugin_paypal_payment_title') ?></h4>
			</div>
			<div class="modal-body">
				<div id="paypal-button-container"></div>
			</div>
		</div>
	</div>
</div>

<script>
(function ($, undefined) {
	var PAYPAL_SCRIPT = 'https://www.paypal.com/sdk/js?client-id=<?php echo $tpl['arr']['merchant_id'];?>&currency=<?php echo $tpl['arr']['currency_code']; ?>';
	var script = document.createElement('script');
	script.setAttribute('src', PAYPAL_SCRIPT);
	document.head.appendChild(script);

	$("#modalPaypal").on("hidden.bs.modal", function () {
		// Cancel the order if the popup is closed by user.
		window.location.href = "<?php echo $tpl['arr']['failure_url']; ?>";
	});

	$("#modalPaypal").on("shown.bs.modal", function () {
		setTimeout(function() {
			paypal.Buttons({
			    createOrder() {
			        // This function sets up the details of the transaction, including the amount and line item details.
			        return fetch("<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjPaypal&action=pjActionCreateOrder&foreign_id=<?php echo $tpl['arr']['option_foreign_id'];?>", {
			            method: "POST",
			            headers: {
			                "Content-Type": "application/json",
			            },
			            body: JSON.stringify({
			                cart: {
			                	reference_id: "<?php echo $tpl['arr']['custom']; ?>",
			                    amount: "<?php echo $tpl['arr']['amount']; ?>",
			                    currency: "<?php echo $tpl['arr']['currency_code']; ?>",
			                    description: "<?php echo htmlspecialchars($tpl['arr']['item_name']); ?>"
			                }
			            })
			        }).then(function(res) {
			            return res.json();
			        }).then(function(res) {
			            return res.id;
			        });
			    },
			    onApprove(data) {
			    	return fetch("<?php echo $tpl['arr']['notify_url']; ?>", {
			            method: "POST",
			            body: JSON.stringify({
			            	custom: "<?php echo $tpl['arr']['custom']; ?>",
			            	paypal_order_id: data.orderID
			            })
			        })
			        .then((response) => response.json())
			        .then((details) => {
			        	window.location.href = "<?php echo $tpl['arr']['return_url']; ?>";
			        });
			    },
			    /*onCancel(data) {
			    	window.location.href = "<?php echo $tpl['arr']['failure_url']; ?>";
		      	},*/
		      	onError(err) {
			    	$('#modalPaypal').find('.modal-body').html(err);
		      	}
			}).render('#paypal-button-container');
		}, 2000);
	});

	$('#modalPaypal .modal-dialog').css('z-index', 1040);
	$('#modalPaypal').modal("show");
})((window.pjQ && window.pjQ.jQuery) || jQuery);
</script>