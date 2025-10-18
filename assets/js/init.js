/**
 * Warehouse Frontend JavaScript
 * Consolidated scripts for cart, checkout, and article interactions
 * Uses data-warehouse-* attributes for consistent DOM selection
 */

(function() {
    'use strict';

    // ========================================
    // Constants
    // ========================================
    
    /**
     * Data attribute name used to mark elements as initialized
     * @constant {string}
     */
    const INIT_ATTR = 'data-warehouse-initialized';

    // ========================================
    // Global Cart Count Update
    // ========================================
    
    /**
     * Updates all elements displaying the cart item count
     * @param {number} itemsCount - Number of items in cart
     */
    function updateGlobalCartCount(itemsCount) {
        document.querySelectorAll('[data-warehouse-cart-count]').forEach(element => {
            element.textContent = itemsCount;
        });
    }
    
    // Make globally available for backward compatibility
    window.updateGlobalCartCount = updateGlobalCartCount;

    // ========================================
    // Cart API Communication
    // ========================================
    
    /**
     * Generic cart update function
     * @param {string} action - API action (add, modify, delete, empty, set)
     * @param {string} articleId - Article ID
     * @param {string|null} variantId - Variant ID (optional)
     * @param {number} amount - Amount to add/modify
     * @param {string|null} mode - Mode for modify action (+, -, set)
     * @param {Function|null} onSuccess - Success callback
     * @param {Function|null} onError - Error callback
     */
    function updateCart(action, articleId, variantId = null, amount = 1, mode = null, onSuccess = null, onError = null) {
        // Build API URL
        let url = `index.php?rex-api-call=warehouse_cart_api&action=${action}`;
        
        if (articleId) {
            url += `&article_id=${encodeURIComponent(articleId)}`;
        }
        
        if (variantId && variantId !== 'null' && variantId !== '') {
            url += `&variant_id=${encodeURIComponent(variantId)}`;
        }
        
        if (amount && action !== 'empty') {
            url += `&amount=${encodeURIComponent(amount)}`;
        }
        
        if (mode) {
            url += `&mode=${encodeURIComponent(mode)}`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update global cart count
                if (data.totals && data.totals.items_count !== undefined) {
                    updateGlobalCartCount(data.totals.items_count);
                }
                
                if (onSuccess) {
                    onSuccess(data);
                }
            } else {
                console.error('Cart update failed:', data);
                if (onError) {
                    onError(data);
                } else {
                    alert('Fehler beim Aktualisieren des Warenkorbs.');
                }
            }
        })
        .catch(error => {
            console.error('Cart update error:', error);
            if (onError) {
                onError(error);
            } else {
                alert('Fehler beim Aktualisieren des Warenkorbs.');
            }
        });
    }

    // ========================================
    // Currency Formatter
    // ========================================
    
    /**
     * Format currency value
     * @param {number} value - Numeric value
     * @param {string} currency - Currency code (default: EUR)
     * @param {string} locale - Locale (default: de-DE)
     * @returns {string} Formatted currency string
     */
    function formatCurrency(value, currency = 'EUR', locale = 'de-DE') {
        const formatter = new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency
        });
        return formatter.format(value);
    }

    // ========================================
    // Cart Page Handlers
    // ========================================
    
    function initCartPage() {
        const cartPageContainers = document.querySelectorAll('[data-warehouse-cart-page]');
        if (!cartPageContainers.length) return;

        cartPageContainers.forEach(cartPageContainer => {
            // Skip if already initialized to prevent duplicate event listeners
            if (cartPageContainer.hasAttribute(INIT_ATTR)) return;

            // Initialize tooltips if Bootstrap is available
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = cartPageContainer.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            }

            // Handle quantity button clicks
            cartPageContainer.querySelectorAll('[data-warehouse-cart-quantity]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.dataset.warehouseCartQuantity;
                    const mode = this.dataset.warehouseMode;
                    const articleId = this.dataset.warehouseArticleId;
                    const variantId = this.dataset.warehouseVariantId;
                    const amount = this.dataset.warehouseAmount || 1;

                    // Show loading state - only for quantity buttons, not delete buttons
                    const loadingElements = cartPageContainer.querySelectorAll(
                        `[data-warehouse-cart-quantity][data-warehouse-article-id="${articleId}"][data-warehouse-variant-id="${variantId || ''}"]`
                    );
                    loadingElements.forEach(el => el.classList.add('opacity-50'));

                    updateCart(action, articleId, variantId, amount, mode, 
                        (data) => updateCartPageDisplay(data, cartPageContainer),
                        () => {
                            loadingElements.forEach(el => el.classList.remove('opacity-50'));
                        }
                    );
                });
            });

            // Handle delete button clicks
            cartPageContainer.querySelectorAll('[data-warehouse-cart-delete]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const articleId = this.dataset.warehouseArticleId;
                    const variantId = this.dataset.warehouseVariantId;
                    const confirmMsg = this.dataset.warehouseConfirm || '';

                    if (!confirmMsg || confirm(confirmMsg)) {
                        updateCart('delete', articleId, variantId, 1, null,
                            (data) => updateCartPageDisplay(data, cartPageContainer)
                        );
                    }
                });
            });

            // Handle quantity input changes
            cartPageContainer.querySelectorAll('[data-warehouse-cart-input]').forEach(input => {
                input.addEventListener('change', function() {
                    const articleId = this.dataset.warehouseArticleId;
                    const variantId = this.dataset.warehouseVariantId;
                    const newAmount = parseInt(this.value, 10);

                    if (newAmount > 0) {
                        updateCart('set', articleId, variantId, newAmount, 'set',
                            (data) => updateCartPageDisplay(data, cartPageContainer)
                        );
                    } else {
                        this.value = 1; // Reset to minimum
                    }
                });

                // Prevent non-numeric input
                input.addEventListener('keypress', function(e) {
                    if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                        e.preventDefault();
                    }
                });
            });

            // Mark as initialized after successful setup
            cartPageContainer.setAttribute(INIT_ATTR, 'true');
        });
    }

    function updateCartPageDisplay(cartData, container) {
        // Update item quantities and totals
        if (cartData.items) {
            Object.entries(cartData.items).forEach(([itemKey, item]) => {
                // Update quantity input
                const quantityInput = container.querySelector(`[data-warehouse-item-key="${itemKey}"]`);
                if (quantityInput) {
                    quantityInput.value = item.amount;
                }

                // Update item total
                const itemTotal = container.querySelector(`[data-warehouse-item-total="${itemKey}"]`);
                if (itemTotal && item.current_total !== undefined) {
                    itemTotal.textContent = formatCurrency(item.current_total);
                }
            });
        }

        // Update all cart totals if available
        if (cartData.totals) {
            // Update subtotal by mode (net/gross)
            const subtotalByModeElement = container.querySelector('[data-warehouse-cart-subtotal-by-mode]');
            if (subtotalByModeElement && cartData.totals.subtotal_by_mode_formatted) {
                subtotalByModeElement.textContent = cartData.totals.subtotal_by_mode_formatted;
            }

            // Update tax total
            const taxElement = container.querySelector('[data-warehouse-cart-tax]');
            if (taxElement && cartData.totals.tax_total_formatted) {
                taxElement.textContent = cartData.totals.tax_total_formatted;
            }

            // Update shipping costs
            const shippingElement = container.querySelector('[data-warehouse-cart-shipping]');
            if (shippingElement && cartData.totals.shipping_costs_formatted) {
                shippingElement.textContent = cartData.totals.shipping_costs_formatted;
            }

            // Update cart total by mode (final total)
            const totalElement = container.querySelector('[data-warehouse-cart-total]');
            if (totalElement && cartData.totals.cart_total_by_mode_formatted) {
                totalElement.textContent = cartData.totals.cart_total_by_mode_formatted;
            }

            // Legacy support: Update old subtotal attribute if exists
            const subtotalElement = container.querySelector('[data-warehouse-cart-subtotal]');
            if (subtotalElement && cartData.totals.total_formatted) {
                subtotalElement.textContent = cartData.totals.total_formatted;
            }
        }

        // Remove deleted items from DOM
        if (cartData.cart && cartData.cart.items) {
            const currentItemKeys = Object.keys(cartData.cart.items);
            container.querySelectorAll('[data-warehouse-item-key]').forEach(element => {
                const itemKey = element.getAttribute('data-warehouse-item-key');
                if (itemKey && !currentItemKeys.includes(itemKey)) {
                    const itemContainer = element.closest('.card-body');
                    if (itemContainer) {
                        itemContainer.remove();
                    }
                }
            });
        }

        // If cart is empty, reload page
        if (cartData.totals && cartData.totals.items_count === 0) {
            window.location.reload();
        }
    }

    // ========================================
    // Offcanvas Cart Handlers
    // ========================================
    
    function initOffcanvasCart() {
        const offcanvasCarts = document.querySelectorAll('[data-warehouse-offcanvas-cart]');
        if (!offcanvasCarts.length) return;

        offcanvasCarts.forEach(offcanvasCart => {
            // Skip if already initialized to prevent duplicate event listeners
            if (offcanvasCart.hasAttribute(INIT_ATTR)) return;

            // Handle empty cart button
            const emptyCartBtn = offcanvasCart.querySelector('[data-warehouse-cart-empty]');
            if (emptyCartBtn) {
                emptyCartBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const confirmMsg = this.dataset.warehouseConfirm || '';
                    
                    if (!confirmMsg || confirm(confirmMsg)) {
                        updateCart('empty', null, null, 1, null,
                            (data) => updateOffcanvasCartDisplay(data, offcanvasCart)
                        );
                    }
                });
            }

            // Handle delete button clicks
            offcanvasCart.querySelectorAll('[data-warehouse-cart-delete]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const articleId = this.dataset.warehouseArticleId;
                    const variantId = this.dataset.warehouseVariantId;
                    const confirmMsg = this.dataset.warehouseConfirm || '';

                    if (!confirmMsg || confirm(confirmMsg)) {
                        updateCart('delete', articleId, variantId, 1, null,
                            (data) => updateOffcanvasCartDisplay(data, offcanvasCart)
                        );
                    }
                });
            });

            // Mark as initialized after successful setup
            offcanvasCart.setAttribute(INIT_ATTR, 'true');
        });
    }

    function updateOffcanvasCartDisplay(cartData, container) {
        // Update item quantities and totals
        if (cartData.items) {
            Object.entries(cartData.items).forEach(([itemKey, item]) => {
                // Update item amount
                const itemAmount = container.querySelector(`[data-warehouse-item-amount="${itemKey}"]`);
                if (itemAmount) {
                    itemAmount.textContent = item.amount;
                }

                // Update item total
                const itemTotal = container.querySelector(`[data-warehouse-item-total="${itemKey}"]`);
                if (itemTotal && item.current_total !== undefined) {
                    itemTotal.textContent = formatCurrency(item.current_total);
                }

                // Update item price
                const itemPrice = container.querySelector(`[data-warehouse-item-price="${itemKey}"]`);
                if (itemPrice && item.current_price !== undefined) {
                    itemPrice.textContent = formatCurrency(item.current_price);
                }
            });
        }

        // Update offcanvas subtotal
        const subtotalElement = container.querySelector('[data-warehouse-offcanvas-subtotal]');
        if (subtotalElement && cartData.totals && cartData.totals.subtotal_formatted) {
            subtotalElement.textContent = cartData.totals.subtotal_formatted;
        }

        // Remove deleted items from DOM
        if (cartData.cart && cartData.cart.items) {
            const currentItemKeys = Object.keys(cartData.cart.items);
            container.querySelectorAll('[data-warehouse-item-key]').forEach(element => {
                const itemKey = element.getAttribute('data-warehouse-item-key');
                if (itemKey && !currentItemKeys.includes(itemKey)) {
                    const listItem = element.closest('li');
                    if (listItem) {
                        listItem.remove();
                    }
                }
            });
        }

        // If cart is empty, show empty message
        if (cartData.totals && cartData.totals.items_count === 0) {
            const cartContent = container.querySelector('[data-warehouse-offcanvas-body]');
            if (cartContent) {
                const emptyMsg = container.dataset.warehouseEmptyMessage || 'Der Warenkorb ist leer.';
                cartContent.innerHTML = `<div class="alert alert-info">${emptyMsg}</div>`;
            }
        }
    }

    // ========================================
    // Cart Table Handlers
    // ========================================
    
    function initCartTable() {
        const cartTables = document.querySelectorAll('[data-warehouse-cart-table]');
        if (!cartTables.length) return;

        cartTables.forEach(cartTable => {
            // Skip if already initialized to prevent duplicate event listeners
            if (cartTable.hasAttribute(INIT_ATTR)) return;

            // Handle quantity button clicks
            cartTable.querySelectorAll('[data-warehouse-cart-quantity]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.dataset.warehouseCartQuantity;
                    const mode = this.dataset.warehouseMode;
                    const articleId = this.dataset.warehouseArticleId;
                    const variantId = this.dataset.warehouseVariantId;
                    const amount = this.dataset.warehouseAmount || 1;

                    // Show loading state - only for quantity buttons, not delete buttons
                    const loadingElements = cartTable.querySelectorAll(
                        `[data-warehouse-cart-quantity][data-warehouse-article-id="${articleId}"][data-warehouse-variant-id="${variantId || ''}"]`
                    );
                    loadingElements.forEach(el => {
                        el.classList.add('disabled');
                        if (el.tagName === 'BUTTON') {
                            el.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                        }
                    });

                    updateCart(action, articleId, variantId, amount, mode,
                        (data) => {
                            updateCartTableDisplay(data, cartTable);
                            // Reset loading state
                            loadingElements.forEach(el => {
                                el.classList.remove('disabled');
                                if (el.tagName === 'BUTTON' && el.dataset.warehouseOriginalText) {
                                    el.innerHTML = el.dataset.warehouseOriginalText;
                                }
                            });
                        },
                        () => {
                            loadingElements.forEach(el => el.classList.remove('disabled'));
                        }
                    );
                });
            });

            // Handle delete button clicks
            cartTable.querySelectorAll('[data-warehouse-cart-delete]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const articleId = this.dataset.warehouseArticleId;
                    const variantId = this.dataset.warehouseVariantId;
                    const confirmMsg = this.dataset.warehouseConfirm || '';

                    if (!confirmMsg || confirm(confirmMsg)) {
                        updateCart('delete', articleId, variantId, 1, null,
                            (data) => updateCartTableDisplay(data, cartTable)
                        );
                    }
                });
            });

            // Handle next button with loading animation
            cartTable.querySelectorAll('[data-warehouse-cart-next]').forEach(element => {
                element.addEventListener('click', function(event) {
                    const loadingButton = event.target.closest('[data-warehouse-cart-next]');
                    if (loadingButton) {
                        loadingButton.classList.add('disabled');
                        loadingButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    }
                });
            });

            // Mark as initialized after successful setup
            cartTable.setAttribute(INIT_ATTR, 'true');
        });
    }

    function updateCartTableDisplay(cartData, container) {
        // Update item quantities and totals
        if (cartData.items) {
            Object.entries(cartData.items).forEach(([itemKey, item]) => {
                // Update quantity display
                const quantitySpan = container.querySelector(`[data-warehouse-item-amount="${itemKey}"]`);
                if (quantitySpan) {
                    quantitySpan.textContent = item.amount;
                }

                // Update item total
                const itemTotal = container.querySelector(`[data-warehouse-item-total="${itemKey}"]`);
                if (itemTotal && item.current_total !== undefined) {
                    itemTotal.textContent = formatCurrency(item.current_total);
                }

                // Update minus button disabled state based on quantity
                const row = container.querySelector(`[data-article-key="${itemKey}"]`);
                if (row) {
                    const minusButton = row.querySelector('[data-warehouse-cart-quantity="modify"][data-warehouse-original-text="-"]');
                    if (minusButton) {
                        if (item.amount <= 1) {
                            minusButton.setAttribute('disabled', 'disabled');
                        } else {
                            minusButton.removeAttribute('disabled');
                        }
                    }
                }
            });
        }

        // Update all cart totals if available
        if (cartData.totals) {
            // Update subtotal by mode (net/gross)
            const subtotalByModeElement = container.querySelector('[data-warehouse-cart-subtotal-by-mode]');
            if (subtotalByModeElement && cartData.totals.subtotal_by_mode_formatted) {
                subtotalByModeElement.textContent = cartData.totals.subtotal_by_mode_formatted;
            }

            // Update tax total
            const taxElement = container.querySelector('[data-warehouse-cart-tax]');
            if (taxElement && cartData.totals.tax_total_formatted) {
                taxElement.textContent = cartData.totals.tax_total_formatted;
            }

            // Update shipping costs
            const shippingElement = container.querySelector('[data-warehouse-cart-shipping]');
            if (shippingElement && cartData.totals.shipping_costs_formatted) {
                shippingElement.textContent = cartData.totals.shipping_costs_formatted;
            }

            // Update cart total by mode (final total)
            const totalElement = container.querySelector('[data-warehouse-cart-total]');
            if (totalElement && cartData.totals.cart_total_by_mode_formatted) {
                totalElement.textContent = cartData.totals.cart_total_by_mode_formatted;
            }

            // Legacy support: Update old subtotal attribute if exists
            const subtotalElement = container.querySelector('[data-warehouse-table-subtotal]');
            if (subtotalElement && cartData.totals.subtotal_by_mode_formatted) {
                subtotalElement.textContent = cartData.totals.subtotal_by_mode_formatted;
            }
        }

        // Remove deleted items from table
        if (cartData.cart && cartData.cart.items) {
            const currentItemKeys = Object.keys(cartData.cart.items);
            container.querySelectorAll('[data-warehouse-item-key]').forEach(element => {
                const itemKey = element.getAttribute('data-warehouse-item-key');
                if (itemKey && !currentItemKeys.includes(itemKey)) {
                    const tableRow = element.closest('tr');
                    if (tableRow) {
                        tableRow.remove();
                    }
                }
            });
        }

        // If cart is empty, reload page
        if (cartData.totals && cartData.totals.items_count === 0) {
            window.location.reload();
        }
    }

    // ========================================
    // Article Detail Page Handlers
    // ========================================
    
    function initArticleDetail() {
        const articleDetails = document.querySelectorAll('[data-warehouse-article-detail]');
        if (!articleDetails.length) return;

        articleDetails.forEach(articleDetail => {
            // Skip if already initialized to prevent duplicate event listeners
            if (articleDetail.hasAttribute(INIT_ATTR)) return;

            // Handle quantity switcher buttons
            const quantityButtons = articleDetail.querySelectorAll('[data-warehouse-quantity-switch]');
            const priceElement = articleDetail.querySelector('[data-warehouse-price-display]');
            
            if (priceElement && quantityButtons.length > 0) {
                const basePrice = parseFloat(priceElement.dataset.warehouseBasePrice);
                const bulkPricesData = priceElement.dataset.warehouseBulkPrices;
                const bulkPrices = bulkPricesData ? JSON.parse(bulkPricesData) : [];
                
                quantityButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const inputId = this.dataset.warehouseQuantityInput;
                        const input = document.getElementById(inputId);
                        if (!input) return;

                        let currentValue = parseInt(input.value, 10);
                        const changeValue = parseInt(this.dataset.warehouseQuantitySwitch, 10);
                        
                        if (!isNaN(currentValue)) {
                            currentValue += changeValue;
                            if (currentValue < 1) {
                                currentValue = 1;
                            }
                            input.value = currentValue;
                            updatePriceDisplay(currentValue, basePrice, bulkPrices, priceElement);
                        }
                    });
                });

                // Handle direct input changes
                const quantityInput = articleDetail.querySelector('[data-warehouse-quantity-input]');
                if (quantityInput) {
                    quantityInput.addEventListener('input', function() {
                        const quantity = parseInt(this.value, 10) || 1;
                        updatePriceDisplay(quantity, basePrice, bulkPrices, priceElement);
                    });
                }
            }

            // Handle add to cart form submission
            const detailForm = articleDetail.querySelector('[data-warehouse-add-form]');
            if (detailForm) {
                detailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(detailForm);
                    const articleId = formData.get('article_id');
                    const orderCount = formData.get('order_count');
                    
                    // Check for active variant
                    let variantId = null;
                    const activeVariant = articleDetail.querySelector('[data-warehouse-variant].active');
                    if (activeVariant) {
                        variantId = activeVariant.dataset.warehouseVariantId;
                    }

                    updateCart('add', articleId, variantId, orderCount, null,
                        (data) => {
                            // Optional: Show success feedback
                            const submitBtn = detailForm.querySelector('button[type="submit"]:focus');
                            if (submitBtn) {
                                submitBtn.blur();
                            }
                        }
                    );
                });
            }

            // Mark as initialized after successful setup
            articleDetail.setAttribute(INIT_ATTR, 'true');
        });
    }

    function updatePriceDisplay(quantity, basePrice, bulkPrices, priceElement) {
        let pricePerUnit = basePrice;
        let totalPrice = basePrice * quantity;
        
        // Check if bulk pricing applies
        if (bulkPrices && bulkPrices.length > 0) {
            for (const bulkPrice of bulkPrices) {
                if (quantity >= bulkPrice.min && (bulkPrice.max === null || quantity <= bulkPrice.max)) {
                    pricePerUnit = parseFloat(bulkPrice.price);
                    totalPrice = pricePerUnit * quantity;
                    break;
                }
            }
        }
        
        // Update the price display
        const priceSpan = priceElement.querySelector('[data-warehouse-price-value]');
        if (priceSpan) {
            if (quantity === 1) {
                priceSpan.textContent = formatCurrency(pricePerUnit);
            } else {
                priceSpan.innerHTML = `${formatCurrency(pricePerUnit)} × ${quantity} = <strong>${formatCurrency(totalPrice)}</strong>`;
            }
        }
    }

    // ========================================
    // Checkout Form Handlers
    // ========================================
    
    function initCheckoutForm() {
        const checkoutForms = document.querySelectorAll('[data-warehouse-checkout-form]');
        if (!checkoutForms.length) return;

        checkoutForms.forEach(checkoutForm => {
            // Skip if already initialized to prevent duplicate event listeners
            if (checkoutForm.hasAttribute(INIT_ATTR)) return;

            // Handle different shipping address toggle
            const shippingToggle = checkoutForm.querySelector('[data-warehouse-shipping-toggle]');
            const shippingFields = checkoutForm.querySelector('[data-warehouse-shipping-fields]');
            
            if (shippingToggle && shippingFields) {
                shippingToggle.addEventListener('change', function() {
                    if (this.checked) {
                        shippingFields.style.display = 'block';
                    } else {
                        shippingFields.style.display = 'none';
                        // Clear shipping address fields when hidden
                        const shippingInputs = shippingFields.querySelectorAll('input, textarea');
                        shippingInputs.forEach(input => {
                            if (input.type !== 'hidden') {
                                input.value = '';
                            }
                        });
                    }
                });
                
                // Check if should be shown on load
                const hasShippingData = shippingFields.dataset.warehouseHasData === 'true';
                if (hasShippingData) {
                    shippingToggle.checked = true;
                    shippingFields.style.display = 'block';
                }
            }

            // Mark as initialized after successful setup
            checkoutForm.setAttribute(INIT_ATTR, 'true');
        });
    }

    // ========================================
    // Initialize All Components
    // ========================================
    
    function init() {
        initCartPage();
        initOffcanvasCart();
        initCartTable();
        initArticleDetail();
        initCheckoutForm();
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
