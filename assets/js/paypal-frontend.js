// Read PayPal style config from the data attribute
const configDiv = document.querySelector("[data-warehouse-paypal-config]");
let paypalStyle = {};
if (configDiv) {
    try {
        paypalStyle = JSON.parse(configDiv.getAttribute("data-warehouse-paypal-style")) || {};
    } catch (e) {
        console.error("Invalid PayPal style config:", e);
    }
}

const successUrl = configDiv ? configDiv.getAttribute("data-warehouse-paypal-success-url") : null;
const cancelUrl = configDiv ? configDiv.getAttribute("data-warehouse-paypal-cancel-url") : null;

const paypalButtons = window.paypal.Buttons({
    style: {
        shape: paypalStyle.shape || "rect",
        size: paypalStyle.size || "responsive",
        tagline: paypalStyle.tagline !== undefined ? paypalStyle.tagline : false,
        layout: paypalStyle.layout || "vertical",
        color: paypalStyle.color || "blue",
        label: paypalStyle.label || "paypal",
        height: paypalStyle.height || undefined,
        fundingSource: paypalStyle.fundingSource || undefined,
    },
    message: {
        amount: 100,
    },
    async createOrder() {
        try {
            const response = await fetch("/?rex-api-call=warehouse_order&action=order", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
            });

            const orderData = await response.json();

            if (orderData.id) {
                return orderData.id;
            }
            const errorDetail = orderData?.details?.[0];
            const errorMessage = errorDetail
                ? `${errorDetail.issue} ${errorDetail.description} (${orderData.debug_id})`
                : JSON.stringify(orderData);

            throw new Error(errorMessage);
        } catch (error) {
            console.error(error);
            resultMessage(`Could not initiate PayPal Checkout...<br><br>${error}`);
        }
    },
    async onApprove(data, actions) {
        try {
            const response = await fetch(
                `/?rex-api-call=warehouse_order&action=capture&order_id=${data.orderID}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                }
            );

            const orderData = await response.json();

            const errorDetail = orderData?.details?.[0];

            if (errorDetail?.issue === "INSTRUMENT_DECLINED") {
                return actions.restart();
            } else if (errorDetail) {
                throw new Error(
                    `${errorDetail.description} (${orderData.debug_id})`
                );
            } else if (!orderData.purchase_units) {
                throw new Error(JSON.stringify(orderData));
            } else {
                const transaction =
                    orderData?.purchase_units?.[0]?.payments?.captures?.[0] ||
                    orderData?.purchase_units?.[0]?.payments
                        ?.authorizations?.[0];
                resultMessage(
                    `Transaction ${transaction.status}: ${transaction.id}<br>
          <br>See console for all available details`
                );
                console.log(
                    "Capture result",
                    orderData,
                    JSON.stringify(orderData, null, 2)
                );
                window.location.href = configDiv.getAttribute("data-warehouse-paypal-success-page-url") + '?transaction_id=' + transaction.id + '&transaction_status=' + transaction.status;
            }


        } catch (error) {
            console.error(error);
            resultMessage(
                `Sorry, your transaction could not be processed...<br><br>${error}`
            );
        }
    },
});

paypalButtons.render("#paypal-button-container");


// Example function to show a result to the user. Your site's UI library can be used instead.
function resultMessage(message) {
    const container = document.querySelector("#paypal-result-message");
    container.innerHTML = message;
}
