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
    const total = Object.values(cart).reduce((a, b) => a + b, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) cartCountElement.textContent = total;
    document.querySelectorAll('.product-count').forEach(span => {
        const itemKey = span.dataset.itemKey;
        const quantity = cart[itemKey] || 0;
        span.textContent = quantity;
        span.classList.toggle('active', quantity > 0);
    });
}

// Toast notification
function showToast(message, isError = false) {
    if (typeof Toastify === 'undefined') {
        console.error('Toastify not loaded');
        return;
    }
    Toastify({
        text: `<i class="${isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'}"></i> ${message}`,
        duration: 3000,
        gravity: "top",
        position: "right",
        escapeMarkup: false,
        style: { background: isError ? "#dc3545" : "#6A2477" }
    }).showToast();
}

// Page-specific logic
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const page = body.dataset.page;
    const BASE_PATH = body.dataset.basePath;
    const sessionCart = JSON.parse(body.dataset.sessionCart || '{}');

    // Restore cart from localStorage if empty session
    if (Object.keys(sessionCart).length === 0 && localStorage.getItem('cart')) {
        fetch(BASE_PATH + 'restore_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'cart=' + encodeURIComponent(localStorage.getItem('cart'))
        })
        .then(response => response.text())
        .then(text => JSON.parse(text.replace(/%+$/, '').trim()))
        .then(() => updateCartCount())
        .catch(() => {});
    }

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
                error: function() {
                    $menuGrid.html('<p>Fehler beim Laden des Menüs. <button id="retry-menu">Erneut versuchen</button></p>');
                    $menuGrid.removeClass('loading').attr('aria-busy', 'false');
                    $loadingSpinner.hide();
                    $('#retry-menu').on('click', function() {
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
                            error: function() {
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
        function updateCart(itemKey, quantity, element) {
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                data: { action: 'update', item_key: itemKey, quantity: quantity },
                success: function(response) {
                    if (response.status === 'success') {
                        const $item = element.closest('.cart-item');
                        const price = parseFloat($item.find('.cart-item-details p').text().replace(' €', '').replace(',', '.'));
                        const newSubtotal = price * quantity;
                        if (quantity <= 0) {
                            $item.remove();
                            updateTotal();
                            if ($('.cart-item').length === 0) {
                                $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                                $('.cart-buttons').remove();
                            }
                            showToast('Artikel entfernt');
                        } else {
                            $item.find('.quantity').text(newSubtotal.toFixed(2).replace('.', ',') + ' €');
                            $item.find('.quantity-input').val(quantity);
                            updateTotal();
                            showToast('Menge aktualisiert');
                        }
                        updateLocalCart(response.cart);
                    } else {
                        console.error('Update failed:', response);
                        showToast('Fehler beim Aktualisieren: ' + (response.message || 'Unbekannter Fehler'), true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    showToast('Fehler beim Aktualisieren', true);
                }
            });
        }

        function removeCartItem(itemKey, element) {
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                data: { action: 'remove', item_key: itemKey },
                success: function(response) {
                    if (response.status === 'success') {
                        element.closest('.cart-item').remove();
                        updateTotal();
                        if ($('.cart-item').length === 0) {
                            $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                            $('.cart-buttons').remove();
                        }
                        showToast('Artikel entfernt');
                        updateLocalCart(response.cart);
                    } else {
                        console.error('Remove failed:', response);
                        showToast('Fehler beim Entfernen: ' + (response.message || 'Unbekannter Fehler'), true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    showToast('Fehler beim Entfernen', true);
                }
            });
        }

        function updateTotal() {
            let total = 0;
            $('.cart-item').each(function() {
                const subtotal = parseFloat($(this).find('.quantity').text().replace(' €', '').replace(',', '.'));
                total += subtotal;
            });
            $('.total-amount').text(total.toFixed(2).replace('.', ',') + ' €');
        }

        $('.btn-increment').on('click', function() {
            const itemKey = $(this).data('item-key');
            const $input = $(this).siblings('.quantity-input');
            let quantity = parseInt($input.val()) + 1;
            updateCart(itemKey, quantity, $(this));
        });

        $('.btn-decrement').on('click', function() {
            const itemKey = $(this).data('item-key');
            const $input = $(this).siblings('.quantity-input');
            let quantity = parseInt($input.val()) - 1;
            if (quantity < 0) quantity = 0;
            updateCart(itemKey, quantity, $(this));
        });

        $('.quantity-input').on('change', function() {
            const itemKey = $(this).data('item-key');
            let quantity = parseInt($(this).val());
            if (isNaN(quantity) || quantity < 0) quantity = 0;
            updateCart(itemKey, quantity, $(this));
        });

        $('.btn-remove').on('click', function(e) {
            e.preventDefault();
            const itemKey = $(this).data('item-key');
            removeCartItem(itemKey, $(this));
        });

        // Initial total calculation
        updateTotal();
    }

    // Final order page
    if (page === 'final_order') {
        const inactivityTimeout = 2 * 60 * 1000; // 2 minutes
        const timerThreshold = 1 * 60 * 1000; // 1 minute
        const countdownDuration = 60 * 1000; // 1 minute countdown
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

            countdownInterval = setInterval(function() {
                const elapsedSinceStart = Date.now() - countdownStartTime;
                const remainingMs = countdownDuration - elapsedSinceStart;
                const remainingSeconds = Math.max(0, Math.floor(remainingMs / 1000));
                $countdown.text(remainingSeconds.toString().padStart(2, '0') + ' Sekunden');
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
            $.ajax({
                url: BASE_PATH + 'clear_cart.php',
                type: 'POST',
                dataType: 'json'
            })
            .done(function(response) {
                localStorage.clear();
                window.location.href = BASE_PATH + 'index.php';
            })
            .fail(function() {
                localStorage.clear();
                window.location.href = BASE_PATH + 'index.php';
            });
        }

        $('#back-to-home').on('click', clearCartAndRedirect);
        $(document).on('mousemove keydown click', resetTimer);
        resetTimer();
    }

    // Artikelliste pages (yana/menu.php, warmekueche/menu.php, sushi/menu.php, sushi/vegetarisch.php)
    if (['yana_menu', 'warmekueche_menu', 'sushi_menu', 'sushi_vegetarisch'].includes(page)) {
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

        document.querySelectorAll('.inline-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const itemKey = form.querySelector('input[name="item_key"]').value;
                const [table, itemId] = itemKey.split(':');
                fetch(BASE_PATH + 'config/api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `item_id=${encodeURIComponent(itemId)}&table=${encodeURIComponent(table)}&action=add`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateLocalCart(data.cart);
                        showToast('Artikel hinzugefügt!');
                    } else {
                        console.error('Add failed:', data);
                        showToast('Fehler beim Hinzufügen: ' + (data.message || 'Unbekannter Fehler'), true);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showToast('Fehler beim Hinzufügen', true);
                });
            });
        });
    }

    // Initial cart update
    updateLocalCart(sessionCart);
    setInterval(updateCartCount, 1000);
});