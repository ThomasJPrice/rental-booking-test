<style>
#paypal-button-dialog {
    background-color: white;
    min-width: 320px;
    max-width: 750px;
    padding: 0;
}
.paypal-button-content {}
.paypal-button-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 5px;
    border-bottom: solid 3px black;
    padding: 15px;
}
.paypal-button-title {
    font-weight: bold;
}
.paypal-button-close {
    border-radius: 3px;
    color: black;
    cursor: pointer;
    display: inline-flex;
    justify-content: center;
    height: 18px;
    line-height: 18px;
    width: 18px;
    font-weight: bold;
    font-size: 26px;
    outline: none;
}
.paypal-button-body {
    display: grid;
    gap: 10px;
    padding: 15px;
}
.paypal-button-alert {
        background-color: #dc3545;
        border-radius: 3px;
        color: white;
        padding: 10px;
    }
</style>

<dialog id="paypal-button-dialog">
    <div class="paypal-button-content">
        <div class="paypal-button-header">
            <div class="paypal-button-title"><?php __('plugin_paypal_payment_title'); ?></div>
            <div class="paypal-button-close" role="button" tabindex="0" title="Close">&times;</div>
        </div>
        <div class="paypal-button-body">
            <div class="paypal-button-alert" role="alert" hidden></div>
            <div id="paypal-button-container"></div>
        </div>
    </div>
</dialog>

<script>
(function (window, document) {
    function loadScript(url, callback) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.async = true;
        if (script.readyState) {
            script.onreadystatechange = function () {
                if (script.readyState === "loaded" || script.readyState === "complete") {
                    script.onreadystatechange = null;
                    if (callback && typeof callback === "function") {
                        callback();
                    }
                }
            };
        } else {
            script.onload = function () {
                if (callback && typeof callback === "function") {
                    callback();
                }
            };
        }
        script.src = url;
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(script);
    }

    function paypalCallback() {

        var redirectToCancelUrl = function() {
            var url = "<?php echo $tpl['arr']['cancel_url']; ?>";
            if (url.trim().length) {
                window.location.href = url;
            }
        };

        var dialog = document.querySelector("#paypal-button-dialog");
        dialog.addEventListener("close", function () {
            // Cancel the order if the popup is closed by user.
            redirectToCancelUrl();
        });

        var btnClose = document.querySelector(".paypal-button-close");
        btnClose.addEventListener("click", function () {
            dialog.close();
        });

        dialog.showModal();

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
                }).then(function (response) {
                    if (response.ok) {
                        return response.json();
                    }
                    return Promise.reject(response);
                }).then(function (data) {
                    return data.id;
                }).catch(function (reason) {
                    console.warn(reason);
                });
            },
            onApprove(data) {
                redirectToCancelUrl = function() {};
                dialog.close();
                var redirect_url = new URL("<?php echo $tpl['arr']['notify_url']; ?>");
                redirect_url.searchParams.set("payment_method", "paypal");
                redirect_url.searchParams.set("custom", "<?php echo $tpl['arr']['custom']; ?>");
                redirect_url.searchParams.set("paypal_order_id", data.orderID);
                window.location.href = redirect_url;
            },
            onCancel(data) {
                redirectToCancelUrl = function() {};
                dialog.close();
                var redirect_url = new URL("<?php echo $tpl['arr']['cancel_url']; ?>");
                redirect_url.searchParams.set("payment_method", "paypal");
                redirect_url.searchParams.set("custom", "<?php echo $tpl['arr']['custom']; ?>");
                redirect_url.searchParams.set("paypal_order_id", data.orderID);
                window.location.href = redirect_url;
            },
            onError(err) {
                var alert = dialog.querySelector("[role='alert']");
                alert.textContent = err;
                alert.removeAttribute("hidden");
            }
        }).render('#paypal-button-container');
    }

    if (!window.paypal) {
        loadScript('https://www.paypal.com/sdk/js?client-id=<?php echo $tpl['arr']['merchant_id']; ?>', paypalCallback);
    } else {
        paypalCallback();
    }
})(window, document);
</script>