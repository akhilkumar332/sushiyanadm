// /skripte/skripte.js
// Consolidated JavaScript for all pages with CSP compliance (no nonce required)

// Modal handling
function openModal(id) {
    const modal = document.getElementById('myModal' + id);
    if (modal) modal.style.display = "flex";
}

function closeModal(id) {
    const modal = document.getElementById('myModal' + id);
    if (modal) modal.style.display = "none";
}

function openIngredientsModal(id) {
    const modal = document.getElementById('ingredientsModal' + id);
    if (modal) modal.style.display = "flex";
}

function closeIngredientsModal(id) {
    const modal = document.getElementById('ingredientsModal' + id);
    if (modal) modal.style.display = "none";
}

function openTableModal() {
    const modal = document.getElementById('table-modal-backdrop');
    if (modal) modal.style.display = "flex";
}

function closeTableModal() {
    const modal = document.getElementById('table-modal-backdrop');
    if (modal) modal.style.display = "none";
}

function openAddonModal(id) {
    const modal = document.getElementById('addonModal' + id);
    if (modal) modal.style.display = "flex";
}

function closeAddonModal(id) {
    const modal = document.getElementById('addonModal' + id);
    if (modal) modal.style.display = "none";
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target === modals[i]) {
            modals[i].style.display = "none";
        }
    }
});

// Cart management
function updateLocalCart(sessionCart) {
    localStorage.setItem('cart', JSON.stringify(sessionCart));
    updateCartCount();
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '{}');
    let total = 0;
    for (let itemKey in cart) {
        const quantity = typeof cart[itemKey] === 'object' ? (cart[itemKey].quantity || 0) : (cart[itemKey] || 0);
        total += quantity;
    }
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = total;
        cartCountElement.style.display = total > 0 ? 'block' : 'none';
    }
    document.querySelectorAll('.product-count').forEach(span => {
        const itemKey = span.dataset.itemKey;
        const quantity = typeof cart[itemKey] === 'object' ? (cart[itemKey]?.quantity || 0) : (cart[itemKey] || 0);
        span.textContent = quantity;
        const cartDisplay = span.closest('.cart-display');
        if (cartDisplay) {
            cartDisplay.classList.toggle('active', quantity > 0);
        }
    });
}

// Toast notification with language support
function showToast(messageKey, isError = false, additionalText = '') {
    if (typeof Toastify === 'undefined') {
        console.error('Toastify not loaded');
        return;
    }
    const lang = document.getElementById('language-dropdown')?.value || 'de';
    let message = typeof getTranslation === 'function' ? getTranslation(messageKey, lang) : messageKey;
    message += additionalText;
    Toastify({
        text: `<i class="${isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'}"></i> ${message}`,
        duration: 3000,
        gravity: "top",
        position: "right",
        escapeMarkup: false,
        style: { background: isError ? "#dc3545" : "#6A2477" }
    }).showToast();
}

// Update cart quantity function
function updateCart(itemKey, quantity, element, button, addon = null) {
    const BASE_PATH = document.body.dataset.basePath || '/';
    if (button) {
        $(button).addClass('loading').prop('disabled', true);
    }
    $.ajax({
        url: BASE_PATH + 'config/api.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'update', item_key: itemKey, quantity: quantity, addon: addon },
        success: function(response) {
            if (response?.status === 'success') {
                showToast('cart_updated');
                updateLocalCart(response.cart);
            } else {
                const lang = document.getElementById('language-dropdown')?.value || 'de';
                showToast('update_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
            const lang = document.getElementById('language-dropdown')?.value || 'de';
            showToast('update_error', true, getTranslation('server_error_details', lang));
        },
        complete: function() {
            if (button) {
                $(button).removeClass('loading').prop('disabled', false);
            }
        }
    });
}

// Refresh menu content dynamically
function refreshMenuContent(branch) {
    const page = document.body.dataset.page;
    const table = document.body.dataset.table;
    const BASE_PATH = document.body.dataset.basePath || '/';
    if (['yana_menu', 'warmekueche_menu', 'sushi_menu', 'sushi_vegetarisch'].includes(page)) {
        $.ajax({
            url: BASE_PATH + 'config/artikelliste.php',
            type: 'POST',
            data: { table: table, filiale: branch },
            success: function(data) {
                $('.content').html(data);
                attachArtikellisteListeners();
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                showToast('menu_update_error', true);
            }
        });
    }
}

// Reattach event listeners for artikelliste pages
function attachArtikellisteListeners() {
    document.querySelectorAll('.info-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            openModal(this.dataset.id);
        });
    });

    document.querySelectorAll('.ingredients-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            openIngredientsModal(this.dataset.id);
        });
    });

    document.querySelectorAll('.close').forEach(span => {
        span.addEventListener('click', function() {
            closeModal(this.dataset.id);
            closeIngredientsModal(this.dataset.id);
        });
    });

    document.querySelectorAll('.addon-close').forEach(span => {
        span.addEventListener('click', function() {
            closeAddonModal(this.dataset.id);
        });
    });

    $('.btn-increment').off('click').on('click', function() {
        const itemKey = $(this).data('item-key');
        const $countSpan = $(this).closest('.grid-item').find('.product-count');
        const currentQuantity = parseInt($countSpan.text() || 0);
        const newQuantity = currentQuantity + 1;
        const isInsideOutRolls = itemKey.startsWith('insideoutrolls:');
        updateCart(itemKey, newQuantity, $countSpan, this);
        if (isInsideOutRolls) {
            const id = itemKey.split(':')[1];
            setTimeout(() => openAddonModal(id), 500); // Delay to ensure cart update completes
        }
    });

    $('.btn-decrement').off('click').on('click', function() {
        const itemKey = $(this).data('item-key');
        const $countSpan = $(this).closest('.grid-item').find('.product-count');
        const currentQuantity = parseInt($countSpan.text() || 0);
        const newQuantity = Math.max(0, currentQuantity - 1);
        updateCart(itemKey, newQuantity, $countSpan, this);
    });

    $('.addon-form').off('submit').on('submit', function(e) {
        e.preventDefault();
        const itemKey = $(this).data('item-key');
        const $countSpan = $(`.product-count[data-item-key="${itemKey}"]`);
        const selectedAddon = $(this).find('input[name="addon"]:checked').val();
        if (!selectedAddon) {
            showToast('select_one_addon', true);
            return;
        }
        const id = itemKey.split(':')[1];
        const currentQuantity = parseInt($countSpan.text() || 0) || 1; // Ensure at least 1
        updateCart(itemKey, currentQuantity, $countSpan, null, selectedAddon);
        closeAddonModal(id);
    });
}

// Load branches dynamically
function loadBranches() {
    const BASE_PATH = document.body.dataset.basePath || '/';
    $.ajax({
        url: BASE_PATH + 'config/api.php?action=get_branches',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response?.status === 'success' && response.branches) {
                const branchFilter = $('#branch-filter');
                branchFilter.empty();
                branchFilter.append('<option value="">Alle Filialen</option>');
                response.branches.forEach(branch => {
                    branchFilter.append(`<option value="${branch}">${branch}</option>`);
                });
            } else {
                const lang = document.getElementById('language-dropdown')?.value || 'de';
                showToast('branch_load_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
            showToast('branch_load_server_error', true);
        }
    });
}

// Load orders with polling support
function loadOrders(filter = 'daily', order_by = 'desc', page = 1, branch = '', isPolling = false) {
    const BASE_PATH = document.body.dataset.basePath || '/';
    const per_page = 20;
    const activeUrl = `${BASE_PATH}config/api.php?action=get_active_orders&filter=${filter}&order_by=${order_by}&page=${page}&per_page=${per_page}&branch=${encodeURIComponent(branch)}`;
    const completedUrl = `${BASE_PATH}config/api.php?action=get_completed_orders&filter=${filter}&order_by=${order_by}&page=${page}&per_page=${per_page}&branch=${encodeURIComponent(branch)}`;

    function renderOrders(listId, paginationId, data, type) {
        const list = $(`#${listId}`);
        const pagination = $(`#${paginationId}`);
        const existingOrderIds = new Set([...list.find('.order-card')].map(card => parseInt($(card).data('order-id'))));

        if (!isPolling) list.empty();

        if (!data?.orders || data.orders.length === 0) {
            if (!isPolling) list.html('<p>Keine Bestellungen vorhanden.</p>');
        } else {
            data.orders.forEach(order => {
                if (isPolling && existingOrderIds.has(order.id)) return;
                let itemsHtml = '<table class="order-table"><thead><tr><th>Nummer</th><th>Artikel</th><th class="quantity">Menge</th><th class="price">Einzelpreis</th><th class="subtotal">Gesamt</th></tr></thead><tbody>';
                order.items.forEach(item => {
                    // Display add-on name for insideoutrolls items
                    const displayName = item.addon ? `${item.artikelname} (${item.addon})` : item.artikelname;
                    itemsHtml += `<tr>
                        <td>${item.artikelnummer || 'N/A'}</td>
                        <td>${displayName || 'Unbekannt'}</td>
                        <td class="quantity">${item.quantity || 0}</td>
                        <td class="price">${(item.price || 0).toFixed(2)} €</td>
                        <td class="subtotal">${(item.subtotal || 0).toFixed(2)} €</td>
                    </tr>`;
                });
                itemsHtml += '</tbody></table>';
                const timestamp = order.status === 'active' ? order.created_at : order.updated_at;
                const label = order.status === 'active' ? 'Erstellt' : 'Abgeschlossen'; // Could be translated if needed
                const actions = type === 'active'
                    ? `<select class="order-action" data-order-id="${order.id}">
                        <option value="">Aktion wählen</option>
                        <option value="complete">Bestellung abschließen</option>
                        <option value="delete">Bestellung löschen</option>
                    </select>`
                    : `<p>Abgeschlossen</p>`;
                const tableNumber = order.table_number ? `Tisch: ${order.table_number}` : 'Tisch: Nicht angegeben';
                const orderHtml = `
                    <div class="order-card" data-order-id="${order.id}">
                        <h3>Bestellung #${order.id}</h3>
                        <p>Filiale: ${order.branch || 'Nicht angegeben'}</p>
                        <p>${tableNumber}</p>
                        <p>${label}: ${new Date(timestamp).toLocaleString('de-DE')}</p>
                        ${itemsHtml}
                        <div class="order-total">
                            <span>Gesamtbetrag</span>
                            <span>${(order.total || 0).toFixed(2)} €</span>
                        </div>
                        <div class="order-actions">${actions}</div>
                    </div>
                `;
                if (isPolling && type === 'active') {
                    list.prepend(orderHtml);
                    showToast('new_order_received', false, order.id);
                } else {
                    list.append(orderHtml);
                }
            });
        }

        if (!isPolling) {
            const totalPages = Math.ceil((data?.total || 0) / per_page);
            pagination.empty();
            if (totalPages > 1) {
                pagination.append(`
                    <button class="prev-page" ${page === 1 ? 'disabled' : ''}>Vorherige</button>
                    <span>Seite ${page} von ${totalPages}</span>
                    <button class="next-page" ${page === totalPages ? 'disabled' : ''}>Nächste</button>
                `);
                pagination.find('.prev-page').off('click').on('click', () => loadOrders(filter, order_by, page - 1, branch));
                pagination.find('.next-page').off('click').on('click', () => loadOrders(filter, order_by, page + 1, branch));
            }
        }
    }

    $.ajax({
        url: activeUrl,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data?.status === 'success') {
                renderOrders('active-orders-list', 'active-pagination', data, 'active');
            } else {
                console.error('API error for active orders:', data?.message);
                if (!isPolling) {
                    const lang = document.getElementById('language-dropdown')?.value || 'de';
                    showToast('active_orders_error', true, getTranslation('server_error_details', lang) + (data?.message || ''));
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error for active orders:', status, error, 'Response:', xhr.responseText);
            if (!isPolling) showToast('active_orders_error', true);
        }
    });

    if (!isPolling) {
        $.ajax({
            url: completedUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data?.status === 'success') {
                    renderOrders('completed-orders-list', 'completed-pagination', data, 'completed');
                } else {
                    console.error('API error for completed orders:', data?.message);
                    const lang = document.getElementById('language-dropdown')?.value || 'de';
                    showToast('completed_orders_error', true, getTranslation('server_error_details', lang) + (data?.message || ''));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for completed orders:', status, error, 'Response:', xhr.responseText);
                showToast('completed_orders_error', true);
            }
        });
    }
}

// Page-specific logic
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const page = body.dataset.page;
    const BASE_PATH = body.dataset.basePath || '/';
    const sessionCart = JSON.parse(body.dataset.sessionCart || '{}');

    // Restore cart from localStorage if empty session
    if (Object.keys(sessionCart).length === 0 && localStorage.getItem('cart')) {
        fetch(BASE_PATH + 'restore_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'cart=' + encodeURIComponent(localStorage.getItem('cart'))
        })
        .then(response => response.text())
        .then(text => {
            const data = JSON.parse(text.trim());
            if (data.status === 'success') updateLocalCart(data.cart);
            else console.warn('Cart restore failed:', data.message);
        })
        .catch(error => {
            console.error('Cart restore error:', error);
            showToast('cart_restore_error', true);
        });
    }

    // Branch dropdown change handler
    $('#branch-dropdown').off('change').on('change', function() {
        const branch = $(this).val();
        const $spinner = $('#branch-spinner');
        $spinner.show();
        $.ajax({
            url: BASE_PATH + 'config/api.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'set_branch', branch: branch },
            success: function(response) {
                if (response?.status === 'success') {
                    showToast('branch_changed', false, branch);
                    refreshMenuContent(branch);
                    if (page === 'online_orders') {
                        loadOrders($('#date-filter').val(), $('#order-filter').val(), 1, $('#branch-filter').val());
                    }
                } else {
                    const lang = document.getElementById('language-dropdown')?.value || 'de';
                    showToast('branch_change_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                showToast('branch_change_error', true);
            },
            complete: function() {
                $spinner.hide();
            }
        });
    });

    // Index, Sushi, Warmekueche menu loading
    if (['index', 'sushi', 'warmekueche'].includes(page)) {
        const cachedKey = page === 'index' ? 'cachedMenu' : page === 'sushi' ? 'cachedSushiMenu' : 'cachedWarmekuecheMenu';
        const url = BASE_PATH + 'config/load_' + (page === 'index' ? 'menu' : page + '_menu') + '.php';
        const $menuGrid = $('#menu-grid');
        const $loadingSpinner = $('#loading-spinner');
        const cachedMenu = localStorage.getItem(cachedKey);

        $loadingSpinner.show();
        $menuGrid.addClass('loading');

        if (cachedMenu) {
            $menuGrid.html(cachedMenu);
            $menuGrid.find('.grid-item').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
                const categoryName = $(this).find('.category-name').text();
                if (categoryName) $(this).attr('aria-label', categoryName);
            });
            $menuGrid.removeClass('loading').attr('aria-busy', 'false');
            $loadingSpinner.hide();
        } else {
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    $menuGrid.html(data);
                    $menuGrid.find('.grid-item').each(function(index) {
                        $(this).css('animation-delay', (index * 0.1) + 's');
                        const categoryName = $(this).find('.category-name').text();
                        if (categoryName) $(this).attr('aria-label', categoryName);
                    });
                    localStorage.setItem(cachedKey, data);
                    $menuGrid.removeClass('loading').attr('aria-busy', 'false');
                    $loadingSpinner.hide();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                    $menuGrid.html('<p>Fehler beim Laden des Menüs. <button id="retry-menu">Erneut versuchen</button></p>');
                    $menuGrid.removeClass('loading').attr('aria-busy', 'false');
                    $loadingSpinner.hide();
                    $('#retry-menu').off('click').on('click', function() {
                        $menuGrid.html('').addClass('loading').attr('aria-busy', 'true');
                        $loadingSpinner.show();
                        $.ajax({
                            url: url,
                            method: 'GET',
                            success: function(data) {
                                $menuGrid.html(data);
                                $menuGrid.find('.grid-item').each(function(index) {
                                    $(this).css('animation-delay', (index * 0.1) + 's');
                                    const categoryName = $(this).find('.category-name').text();
                                    if (categoryName) $(this).attr('aria-label', categoryName);
                                });
                                localStorage.setItem(cachedKey, data);
                                $menuGrid.removeClass('loading').attr('aria-busy', 'false');
                                $loadingSpinner.hide();
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                                $menuGrid.html('<p>Fehler beim Laden des Menüs. <button id="retry-menu">Erneut versuchen</button></p>');
                                $menuGrid.removeClass('loading').attr('aria-busy', 'false');
                                $loadingSpinner.hide();
                            }
                        });
                    });
                }
            });
        }
    }

    // Cart page
    if (page === 'cart') {
        function updateTotal() {
            let total = 0;
            $('.cart-item').each(function() {
                const subtotal = parseFloat($(this).find('.subtotal').text().replace(' €', '').replace(',', '.'));
                total += subtotal;
            });
            $('.total-amount').text(total.toFixed(2).replace('.', ',') + ' €');
        }

        function removeCartItem(itemKey, element) {
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'remove', item_key: itemKey },
                success: function(response) {
                    if (response?.status === 'success') {
                        element.closest('.cart-item').remove();
                        updateTotal();
                        if ($('.cart-item').length === 0) {
                            $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                            $('.cart-buttons').remove();
                        }
                        showToast('item_removed');
                        updateLocalCart(response.cart);
                    } else {
                        const lang = document.getElementById('language-dropdown')?.value || 'de';
                        showToast('remove_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                    showToast('remove_error', true);
                }
            });
        }

        $('.btn-increment').off('click').on('click', function() {
            const itemKey = $(this).data('item-key');
            const $input = $(this).siblings('.quantity-input');
            let quantity = parseInt($input.val()) + 1;
            updateCart(itemKey, quantity, $input, this);
            $input.val(quantity);
            const price = parseFloat($(this).closest('.cart-item').find('.item-price').data('price'));
            $(this).closest('.cart-item').find('.subtotal').text((price * quantity).toFixed(2).replace('.', ',') + ' €');
            updateTotal();
        });

        $('.btn-decrement').off('click').on('click', function() {
            const itemKey = $(this).data('item-key');
            const $input = $(this).siblings('.quantity-input');
            let quantity = parseInt($input.val()) - 1;
            if (quantity < 0) quantity = 0;
            updateCart(itemKey, quantity, $input, this);
            if (quantity === 0) {
                $(this).closest('.cart-item').remove();
                if ($('.cart-item').length === 0) {
                    $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                    $('.cart-buttons').remove();
                }
            } else {
                $input.val(quantity);
                const price = parseFloat($(this).closest('.cart-item').find('.item-price').data('price'));
                $(this).closest('.cart-item').find('.subtotal').text((price * quantity).toFixed(2).replace('.', ',') + ' €');
            }
            updateTotal();
        });

        $('.quantity-input').off('change').on('change', function() {
            const itemKey = $(this).data('item-key');
            let quantity = parseInt($(this).val());
            if (isNaN(quantity) || quantity < 0) quantity = 0;
            updateCart(itemKey, quantity, $(this));
            if (quantity === 0) {
                $(this).closest('.cart-item').remove();
                if ($('.cart-item').length === 0) {
                    $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                    $('.cart-buttons').remove();
                }
            } else {
                const price = parseFloat($(this).closest('.cart-item').find('.item-price').data('price'));
                $(this).closest('.cart-item').find('.subtotal').text((price * quantity).toFixed(2).replace('.', ',') + ' €');
            }
            updateTotal();
        });

        $('.btn-remove').off('click').on('click', function(e) {
            e.preventDefault();
            const itemKey = $(this).data('item-key');
            removeCartItem(itemKey, $(this));
        });

        updateTotal();
    }

    // Final order page
    const pagePath = window.location.pathname;
    if (pagePath !== '/index.php' && pagePath !== '/view/online-orders.php') {
        const inactivityTimeout = 2 * 60 * 1000; // 2 minutes
        const timerThreshold = 3 * 30 * 1000; // 30 seconds
        const countdownDuration = 30 * 1000; // 30 seconds countdown
        let lastActivityTime = Date.now();
        let countdownInterval = null;
        let countdownStartTime = null;
        let isRedirecting = false;

        function resetTimer() {
            lastActivityTime = Date.now();
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            $('#inactivity-timer').hide().removeClass('visible').removeClass('warning');
            checkInactivity();
        }

        function checkInactivity() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            const elapsed = Date.now() - lastActivityTime;
            const timeUntilThreshold = timerThreshold - elapsed;
            const timeUntilTimeout = inactivityTimeout - elapsed;

            if (elapsed >= inactivityTimeout) {
                clearCartAndRedirect();
            } else if (elapsed >= timerThreshold && !countdownInterval) {
                startCountdown();
            } else {
                setTimeout(checkInactivity, Math.min(timeUntilThreshold, timeUntilTimeout));
            }
        }

        function startCountdown() {
            countdownStartTime = Date.now();
            $('#inactivity-timer').show().addClass('visible');
            const $countdown = $('#timer-countdown');
            const lang = document.getElementById('language-dropdown')?.value || document.body.dataset.lang || 'de';
            const secondsText = getTranslation('seconds', lang);

            countdownInterval = setInterval(function() {
                const elapsedSinceStart = Date.now() - countdownStartTime;
                const remainingMs = countdownDuration - elapsedSinceStart;
                const remainingSeconds = Math.max(0, Math.floor(remainingMs / 1000));
                $countdown.text(remainingSeconds.toString().padStart(2, '0') + ' ' + secondsText);
                if (remainingSeconds <= 10) {
                    $('#inactivity-timer').addClass('warning');
                } else {
                    $('#inactivity-timer').removeClass('warning');
                }
                if (remainingSeconds <= 0) {
                    clearInterval(countdownInterval);
                    clearCartAndRedirect();
                }
            }, 1000);
        }

        function clearCartAndRedirect() {
            if (isRedirecting) return;
            isRedirecting = true;
            $('#order-confirmation').hide();
            $('#notify-staff').prop('disabled', false);
            $.ajax({
                url: BASE_PATH + 'clear_cart.php',
                type: 'POST',
                dataType: 'json'
            })
            .done(function(response) {
                localStorage.clear();
                window.location.href = BASE_PATH + 'index.php';
            })
            .fail(function(xhr, status, error) {
                console.error('Clear cart error:', status, error, 'Response:', xhr.responseText);
                localStorage.clear();
                window.location.href = BASE_PATH + 'index.php';
            });
        }

        $('#back-to-home').off('click').on('click', clearCartAndRedirect);

        $('#notify-staff').off('click').on('click', function() {
            openTableModal();
        });

        $('#close-table-modal').off('click').on('click', function() {
            closeTableModal();
        });

        $('#submit-table').off('click').on('click', function() {
            const tableNumber = $('#table-number').val();
            if (!tableNumber) {
                showToast('table_select_error', true);
                return;
            }
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'submit_order', table_number: tableNumber },
                success: function(response) {
                    if (response?.status === 'success') {
                        $('#order-confirmation').show();
                        $('#notify-staff').prop('disabled', true);
                        closeTableModal();
                        showToast('order_submit_success');
                        localStorage.setItem('lastOrderTime', Date.now().toString());
                    } else {
                        const lang = document.getElementById('language-dropdown')?.value || 'de';
                        showToast('order_submit_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                    showToast('order_submit_error', true);
                }
            });
        });

        $(document).on('mousemove keydown click', resetTimer);
        resetTimer();
    }

    // Online orders page
    if (page === 'online_orders') {
        let currentFilter = 'daily';
        let currentOrderBy = 'desc';
        let currentPage = 1;
        let currentBranch = '';
        let lastOrderTime = localStorage.getItem('lastOrderTime') || Date.now();

        loadBranches();
        loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch);

        $('#branch-filter').off('change').on('change', function() {
            currentBranch = $(this).val();
            currentPage = 1;
            loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch);
        });

        $('#date-filter').off('change').on('change', function() {
            currentFilter = $(this).val();
            currentPage = 1;
            loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch);
        });

        $('#order-filter').off('change').on('change', function() {
            currentOrderBy = $(this).val();
            currentPage = 1;
            loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch);
        });

        $('.tab-btn').off('click').on('click', function() {
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').removeClass('active');
            $('#' + $(this).data('tab') + '-orders').addClass('active');
        });

        $(document).off('change', '.order-action').on('change', '.order-action', function() {
            const orderId = $(this).data('order-id');
            const action = $(this).val();
            if (action === 'complete') {
                $.ajax({
                    url: BASE_PATH + 'config/api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'complete_order', order_id: orderId },
                    success: function(response) {
                        if (response?.status === 'success') {
                            showToast('order_completed');
                            loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch);
                        } else {
                            const lang = document.getElementById('language-dropdown')?.value || 'de';
                            showToast('order_complete_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                        showToast('order_complete_error', true);
                    }
                });
            } else if (action === 'delete') {
                $.ajax({
                    url: BASE_PATH + 'config/api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'delete_order', order_id: orderId },
                    success: function(response) {
                        if (response?.status === 'success') {
                            showToast('order_deleted');
                            loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch);
                        } else {
                            const lang = document.getElementById('language-dropdown')?.value || 'de';
                            showToast('order_delete_error', true, getTranslation('server_error_details', lang) + (response?.message || ''));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                        showToast('order_delete_error', true);
                    }
                });
            }
            $(this).val('');
        });

        // Polling for new orders every 10 seconds
        setInterval(() => {
            const newOrderTime = localStorage.getItem('lastOrderTime');
            if (newOrderTime && newOrderTime > lastOrderTime) {
                lastOrderTime = newOrderTime;
                loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch, true);
            } else {
                loadOrders(currentFilter, currentOrderBy, currentPage, currentBranch, true);
            }
        }, 10000);
    }

    // Artikelliste pages
    if (['yana_menu', 'warmekueche_menu', 'sushi_menu', 'sushi_vegetarisch'].includes(page)) {
        attachArtikellisteListeners();
    }

    // Initial cart update
    updateLocalCart(sessionCart);
    setInterval(updateCartCount, 1000);
});