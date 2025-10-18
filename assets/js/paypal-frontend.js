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

const successUrl = configDiv ? configDiv.getAttribute("data-warehouse-paypal-success-page-url") : null;
const cancelUrl = configDiv ? configDiv.getAttribute("data-warehouse-paypal-cancel-url") : null;
const errorCreateOrderMsg = configDiv ? configDiv.getAttribute("data-warehouse-paypal-error-create-order") : "Could not initiate PayPal Checkout";
const errorCaptureOrderMsg = configDiv ? configDiv.getAttribute("data-warehouse-paypal-error-capture-order") : "Sorry, your transaction could not be processed";
const technicalDetailsLabel = configDiv ? configDiv.getAttribute("data-warehouse-paypal-error-technical-details") : "Technical Details";

// Helper function to escape HTML to prevent XSS
function escapeHtml(unsafe) {
    const div = document.createElement('div');
    div.textContent = unsafe;
    return div.innerHTML;
}

// Function to show error modal
function showPayPalErrorModal(message, detailedError = null) {
    const modalElement = document.getElementById("paypalErrorModal");
    const modalBody = document.getElementById("paypalErrorModalBody");
    
    if (modalElement && modalBody) {
        // Create error message paragraph
        const messageParagraph = document.createElement('p');
        messageParagraph.textContent = message;
        
        // Clear existing content
        modalBody.innerHTML = '';
        modalBody.appendChild(messageParagraph);
        
        // Add detailed error in a collapsed section for debugging
        if (detailedError) {
            console.error("PayPal Error Details:", detailedError);
            
            const details = document.createElement('details');
            details.className = 'mt-3';
            
            const summary = document.createElement('summary');
            summary.className = 'text-muted small';
            summary.textContent = technicalDetailsLabel;
            
            const pre = document.createElement('pre');
            pre.className = 'small mt-2 p-2 bg-light border rounded';
            pre.textContent = detailedError;
            
            details.appendChild(summary);
            details.appendChild(pre);
            modalBody.appendChild(details);
        }
        
        // Show the modal using Bootstrap 5
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        // Fallback if modal is not available
        console.error("PayPal Error:", message, detailedError);
        alert(message);
    }
}

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
            showPayPalErrorModal(errorCreateOrderMsg, error.message || error.toString());
            throw error; // Re-throw to prevent PayPal from proceeding
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
            showPayPalErrorModal(errorCaptureOrderMsg, error.message || error.toString());
        }
    },
});

paypalButtons.render("#paypal-button-container");


// Example function to show a result to the user. Your site's UI library can be used instead.
function resultMessage(message) {
    const container = document.querySelector("#paypal-result-message");
    container.innerHTML = message;
}
