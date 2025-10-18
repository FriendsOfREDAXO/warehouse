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

    /**
     * Animates the cart button in the header with a zoom effect
     */
    function animateCartButton() {
        const cartButtons = document.querySelectorAll('[data-warehouse-cart-count]');
        cartButtons.forEach(button => {
            const parentElement = button.closest('a, button');
            if (parentElement) {
                parentElement.classList.add('warehouse-cart-zoom');
                setTimeout(() => {
                    parentElement.classList.remove('warehouse-cart-zoom');
                }, 400);
            }
        });
    }

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
    // Shared Cart Display Update Logic
    // ========================================
    
    /**
     * Updates cart totals in the DOM
     * @param {Object} totals - Cart totals data
     * @param {Element} container - Container element
     * @param {string} prefix - Attribute prefix (e.g., 'cart', 'offcanvas')
     */
    function updateCartTotals(totals, container, prefix = 'cart') {
        if (!totals) return;

        const selectors = {
            subtotalByMode: `[data-warehouse-${prefix}-subtotal-by-mode]`,
            tax: `[data-warehouse-${prefix}-tax]`,
            shipping: `[data-warehouse-${prefix}-shipping]`,
            total: `[data-warehouse-${prefix}-total]`
        };

        // Update subtotal by mode (net/gross)
        updateElement(container, selectors.subtotalByMode, totals.subtotal_by_mode_formatted);

        // Update tax total
        updateElement(container, selectors.tax, totals.tax_total_formatted);

        // Update shipping costs
        updateElement(container, selectors.shipping, totals.shipping_costs_formatted);

        // Update cart total by mode (final total)
        updateElement(container, selectors.total, totals.cart_total_by_mode_formatted);
    }

    /**
     * Updates a single element's text content if element exists
     * @param {Element} container - Container element
     * @param {string} selector - CSS selector
     * @param {string} value - Value to set
     */
    function updateElement(container, selector, value) {
        const element = container.querySelector(selector);
        if (element && value) {
            element.textContent = value;
        }
    }

    /**
     * Removes deleted items from cart display
     * @param {Object} cartData - Cart data with items
     * @param {Element} container - Container element
     * @param {string} itemSelector - Selector for item parent to remove (e.g., '.card-body', 'tr', 'li')
     */
    function removeDeletedItems(cartData, container, itemSelector) {
        if (!cartData.cart || !cartData.cart.items) return;

        const currentItemKeys = Object.keys(cartData.cart.items);
        container.querySelectorAll('[data-warehouse-item-key]').forEach(element => {
            const itemKey = element.getAttribute('data-warehouse-item-key');
            if (itemKey && !currentItemKeys.includes(itemKey)) {
                const itemContainer = element.closest(itemSelector);
                if (itemContainer) {
                    itemContainer.remove();
                }
            }
        });
    }

    /**
     * Handles delete button click with confirmation
     * @param {Event} e - Click event
     * @param {Function} callback - Callback function on success
     */
    function handleDeleteClick(e, callback) {
        e.preventDefault();
        const { warehouseArticleId, warehouseVariantId, warehouseConfirm } = e.currentTarget.dataset;
        
        if (!warehouseConfirm || confirm(warehouseConfirm)) {
            updateCart('delete', warehouseArticleId, warehouseVariantId, 1, null, callback);
        }
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
                    const { warehouseCartQuantity, warehouseMode, warehouseArticleId, warehouseVariantId, warehouseAmount = 1 } = this.dataset;

                    // Show loading state
                    const loadingElements = cartPageContainer.querySelectorAll(
                        `[data-warehouse-cart-quantity][data-warehouse-article-id="${warehouseArticleId}"][data-warehouse-variant-id="${warehouseVariantId || ''}"]`
                    );
                    loadingElements.forEach(el => el.classList.add('opacity-50'));

                    updateCart(warehouseCartQuantity, warehouseArticleId, warehouseVariantId, warehouseAmount, warehouseMode, 
                        (data) => updateCartPageDisplay(data, cartPageContainer),
                        () => loadingElements.forEach(el => el.classList.remove('opacity-50'))
                    );
                });
            });

            // Handle delete button clicks
            cartPageContainer.querySelectorAll('[data-warehouse-cart-delete]').forEach(button => {
                button.addEventListener('click', (e) => handleDeleteClick(e, (data) => updateCartPageDisplay(data, cartPageContainer)));
            });

            // Handle quantity input changes
            cartPageContainer.querySelectorAll('[data-warehouse-cart-input]').forEach(input => {
                input.addEventListener('change', function() {
                    const { warehouseArticleId, warehouseVariantId } = this.dataset;
                    const newAmount = parseInt(this.value, 10);

                    if (newAmount > 0) {
                        updateCart('set', warehouseArticleId, warehouseVariantId, newAmount, 'set',
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

        // Update cart totals
        updateCartTotals(cartData.totals, container, 'cart');

        // Legacy support: Update old subtotal attribute if exists
        if (cartData.totals) {
            updateElement(container, '[data-warehouse-cart-subtotal]', cartData.totals.total_formatted);
        }

        // Remove deleted items from DOM
        removeDeletedItems(cartData, container, '.card-body');

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
                button.addEventListener('click', (e) => handleDeleteClick(e, (data) => updateOffcanvasCartDisplay(data, offcanvasCart)));
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
                updateElement(container, `[data-warehouse-item-amount="${itemKey}"]`, item.amount);

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

        // Update cart totals
        updateCartTotals(cartData.totals, container, 'offcanvas');

        // Legacy support: Update old subtotal attribute if exists
        if (cartData.totals) {
            updateElement(container, '[data-warehouse-offcanvas-subtotal]', cartData.totals.subtotal_formatted);
        }

        // Remove deleted items from DOM
        removeDeletedItems(cartData, container, 'li');

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
                    const { warehouseCartQuantity, warehouseMode, warehouseArticleId, warehouseVariantId, warehouseAmount = 1 } = this.dataset;

                    // Show loading state
                    const loadingElements = cartTable.querySelectorAll(
                        `[data-warehouse-cart-quantity][data-warehouse-article-id="${warehouseArticleId}"][data-warehouse-variant-id="${warehouseVariantId || ''}"]`
                    );
                    loadingElements.forEach(el => {
                        el.classList.add('disabled');
                        if (el.tagName === 'BUTTON') {
                            el.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                        }
                    });

                    updateCart(warehouseCartQuantity, warehouseArticleId, warehouseVariantId, warehouseAmount, warehouseMode,
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
                        () => loadingElements.forEach(el => el.classList.remove('disabled'))
                    );
                });
            });

            // Handle delete button clicks
            cartTable.querySelectorAll('[data-warehouse-cart-delete]').forEach(button => {
                button.addEventListener('click', (e) => handleDeleteClick(e, (data) => updateCartTableDisplay(data, cartTable)));
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
                updateElement(container, `[data-warehouse-item-amount="${itemKey}"]`, item.amount);

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

        // Update cart totals
        updateCartTotals(cartData.totals, container, 'cart');

        // Legacy support: Update old subtotal attribute if exists
        if (cartData.totals) {
            updateElement(container, '[data-warehouse-table-subtotal]', cartData.totals.subtotal_by_mode_formatted);
        }

        // Remove deleted items from table
        removeDeletedItems(cartData, container, 'tr');

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
                    const submitValue = formData.get('submit');
                    
                    // Check for active variant
                    let variantId = null;
                    const activeVariant = articleDetail.querySelector('[data-warehouse-variant].active');
                    if (activeVariant) {
                        variantId = activeVariant.dataset.warehouseVariantId;
                    }

                    // Determine if instant checkout was clicked
                    const isInstantCheckout = submitValue === 'checkout';
                    
                    // Get the clicked submit button
                    const clickedButton = e.submitter || Array.from(detailForm.querySelectorAll('button[type="submit"]')).find(btn => btn.value === submitValue);

                    updateCart('add', articleId, variantId, orderCount, null,
                        (data) => {
                            if (isInstantCheckout) {
                                // Redirect to checkout page
                                // Get the checkout URL from data attribute
                                const checkoutUrl = detailForm.dataset.warehouseCheckoutUrl;
                                if (checkoutUrl) {
                                    window.location.href = checkoutUrl;
                                } else {
                                    console.error('Warehouse: Checkout URL not configured in form data attribute');
                                }
                            } else {
                                // Show success feedback on the add to cart button
                                if (clickedButton && clickedButton.getAttribute('value') === 'cart') {
                                    const originalText = clickedButton.textContent;
                                    const successText = clickedButton.dataset.warehouseSuccessText || 'Zum Warenkorb hinzugefügt';
                                    
                                    // Change button text and style
                                    clickedButton.textContent = successText;
                                    clickedButton.classList.add('warehouse-btn-success-feedback');
                                    clickedButton.disabled = true;
                                    
                                    // Animate cart button in header
                                    animateCartButton();
                                    
                                    // Reset button after 2 seconds
                                    setTimeout(() => {
                                        clickedButton.textContent = originalText;
                                        clickedButton.classList.remove('warehouse-btn-success-feedback');
                                        clickedButton.disabled = false;
                                    }, 2000);
                                }
                                
                                // Blur the button
                                if (clickedButton) {
                                    clickedButton.blur();
                                }
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
    // Add to Cart Links (for category list view)
    // ========================================
    
    function initAddToCartLinks() {
        // Find all links that have action=add_to_cart in their href
        const addToCartLinks = document.querySelectorAll('a[href*="action=add_to_cart"]');
        
        addToCartLinks.forEach(link => {
            // Skip if already initialized
            if (link.hasAttribute(INIT_ATTR)) return;
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Parse URL to extract parameters
                const url = new URL(this.href, window.location.origin);
                const params = new URLSearchParams(url.search);
                
                const artId = params.get('art_id');
                const orderCount = params.get('order_count') || 1;
                
                if (artId) {
                    // Show loading state on the link
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    this.classList.add('disabled');
                    
                    // Call API to add to cart
                    updateCart('add', artId, null, orderCount, null,
                        (data) => {
                            // Reset link state
                            this.innerHTML = originalText;
                            this.classList.remove('disabled');
                            
                            // Optional: Show success feedback if configured
                            const successText = this.dataset.warehouseSuccessText;
                            if (successText) {
                                this.innerHTML = successText;
                                setTimeout(() => {
                                    this.innerHTML = originalText;
                                }, 1500);
                            }
                        },
                        (error) => {
                            // Reset link state on error
                            this.innerHTML = originalText;
                            this.classList.remove('disabled');
                        }
                    );
                }
            });
            
            // Mark as initialized
            link.setAttribute(INIT_ATTR, 'true');
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
        initAddToCartLinks();
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
