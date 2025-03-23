<?php
// No session_start() here; handled by the including page (e.g., menu.php)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style-menu.css">
    <!-- Add Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <title>Menu Items</title>
    <style>
        /* Toastify styling (similar to cart.php) */
        .toastify {
            background: #6A2477 !important;
            color: #fff !important;
            border-radius: 8px !important;
            padding: 12px 20px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
            font-family: 'Arial', sans-serif !important;
            font-size: 16px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            max-width: 300px !important;
        }
        .toastify i {
            font-size: 18px !important;
        }

        /* Styling for the cart button to position the product count */
        .cart-button-list {
            position: relative;
        }

        /* Styling for the product count on the cart icon */
        .product-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #6A2477;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            display: none;
            line-height: 1;
        }

        @media (max-width: 768px) {
            .product-count {
                font-size: 8px;
                padding: 0px 3px;
                top: -4px;
                right: -4px;
                border-radius: 6px;
            }
        }
    </style>
</head>
<body class="artikelliste">
    <div class="grid-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row[$filiale] == 1) {
                    $item_key = $table . ':' . $row["id"];
                    $quantity_in_cart = isset($_SESSION['cart'][$item_key]) ? (int)$_SESSION['cart'][$item_key] : 0;
                    ?>
                    <div class="grid-item">
                        <!-- Image container for better control over image styling -->
                        <div class="dish-image-container">
                            <img src="<?php echo htmlspecialchars('/' . $row['image']); ?>" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/150';" 
                                 alt="<?php echo htmlspecialchars($row["artikelname"]); ?>" 
                                 class="dish-image">
                        </div>
                        
                        <!-- Action buttons container -->
                        <div class="action-buttons">
                            <button class="info-button" onclick="openModal(<?php echo $row["id"]; ?>); event.stopPropagation();" aria-label="Allergene und Zusatzstoffe anzeigen">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            
                            <button class="ingredients-button" onclick="openIngredientsModal(<?php echo $row["id"]; ?>); event.stopPropagation();" aria-label="Zutaten anzeigen">
                                <i class="fas fa-utensils"></i>
                            </button>
                            
                            <form action="" style="display:inline;">
                                <input type="hidden" name="item_key" value="<?php echo $table . ':' . $row["id"]; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="cart-button-list" onclick="event.stopPropagation();" aria-label="Zum Warenkorb hinzufügen">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="product-count" data-item-key="<?php echo htmlspecialchars($item_key); ?>" 
                                          style="<?php echo ($quantity_in_cart > 0 ? 'display: inline-block;' : 'display: none;'); ?>">
                                        <?php echo $quantity_in_cart; ?>
                                    </span>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Dish details -->
                        <div class="dish-details">
                            <h3 class="dishname"><?php echo htmlspecialchars($row["artikelnummer"]) . " " . htmlspecialchars($row["artikelname"]); ?></h3>
                            
                            <div class="mini-logo-container">
                                <?php
                                if ($row["pikant"] == 1) {
                                    echo '<img src="../bilder/icons/pikant.png" alt="Pikant" class="mini-logo">';
                                }
                                if ($row["vegetarisch"] == 1) {
                                    echo '<img src="../bilder/icons/vegetarisch.png" alt="Vegetarisch" class="mini-logo">';
                                }
                                if ($row["vegan"] == 1) {
                                    echo '<img src="../bilder/icons/vegan.png" alt="Vegan" class="mini-logo">';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Price button container (moved outside dish-details) -->
                        <div class="price-button-container">
                            <button class="price-button" aria-label="Preis: <?php echo htmlspecialchars($row["preis"]); ?>">
                                <?php echo htmlspecialchars($row["preis"]); ?>
                            </button>
                        </div>
                    </div>
                    <?php
                }
            }
        } else {
            echo '<div class="no-items">Keine Artikel gefunden.</div>';
        }
        ?>
    </div>

    <!-- Modals (moved outside grid-container) -->
    <?php
    // Reset the result pointer to generate modals
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        if ($row[$filiale] == 1) {
            ?>
            <div id="myModal<?php echo $row["id"]; ?>" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal(<?php echo $row["id"]; ?>)" aria-label="Modal schließen">×</span>
                    <h2>Allergene und Zusatzstoffe</h2>
                    <div><?php echo $row["allergene_zusatz"]; ?></div>
                </div>
            </div>

            <div id="ingredientsModal<?php echo $row["id"]; ?>" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeIngredientsModal(<?php echo $row["id"]; ?>)" aria-label="Modal schließen">×</span>
                    <h2>Zutaten</h2>
                    <div><?php echo $row["beschreibung"]; ?></div>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <!-- Add Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
    function openModal(id) {
        document.getElementById('myModal' + id).style.display = "flex";
    }

    function closeModal(id) {
        document.getElementById('myModal' + id).style.display = "none";
    }

    function openIngredientsModal(id) {
        document.getElementById('ingredientsModal' + id).style.display = "flex";
    }

    function closeIngredientsModal(id) {
        document.getElementById('ingredientsModal' + id).style.display = "none";
    }

    function updateLocalCart() {
        const currentUrl = window.location.pathname;
        let cartFetchUrl;
        if (currentUrl.includes('/sushi/vegetarisch.php')) {
            cartFetchUrl = '/sushi/vegetarisch.php?action=get_cart';
        } else if (currentUrl.includes('/warmekueche/menu.php')) {
            cartFetchUrl = window.location.href + (window.location.search ? '&' : '?') + 'action=get_cart';
        } else if (currentUrl.includes('/sushi/menu.php')) {
            cartFetchUrl = window.location.href + (window.location.search ? '&' : '?') + 'action=get_cart';
        } else {
            cartFetchUrl = '/sushi/vegetarisch.php?action=get_cart'; // Fallback
        }

        fetch(cartFetchUrl, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            localStorage.setItem('cart', JSON.stringify(data));
            updateCartCount();
        })
        .catch(error => {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            updateCartCount();
        });
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart') || '{}');
        let total = 0;

        for (let itemKey in cart) {
            if (cart.hasOwnProperty(itemKey)) {
                const quantity = parseInt(cart[itemKey]) || 0;
                total += quantity;

                const productCountElement = document.querySelector(`.product-count[data-item-key="${itemKey}"]`);
                if (productCountElement) {
                    productCountElement.textContent = quantity.toString();
                    productCountElement.style.display = quantity > 0 ? 'inline-block' : 'none';
                }
            }
        }

        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = total.toString();
        }
    }

    function showToast(message, isError = false) {
        Toastify({
            text: `<i class="${isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'}"></i> ${message}`,
            duration: 3000,
            gravity: "top",
            position: "right",
            escapeMarkup: false,
            style: {
                background: isError ? "#dc3545" : "#6A2477",
            }
        }).showToast();
    }

    if (!window.artikellisteEventListenerAttached) {
        document.addEventListener('DOMContentLoaded', function() {
            const cartForms = document.querySelectorAll('form[action=""]');
            cartForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const button = form.querySelector('.cart-button-list');
                    if (!button) return;

                    const itemKeyInput = form.querySelector('input[name="item_key"]');
                    const quantityInput = form.querySelector('input[name="quantity"]');
                    if (!itemKeyInput || !quantityInput) return;

                    const itemKey = itemKeyInput.value;
                    const quantity = parseInt(quantityInput.value) || 1;
                    const [table, itemId] = itemKey.split(':');
                    if (!itemId || !table) return;

                    fetch('/sushi/vegetarisch.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `item_id=${encodeURIComponent(itemId)}&table=${encodeURIComponent(table)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            updateLocalCart();
                            showToast('Artikel hinzugefügt!');
                        } else {
                            showToast('Der Artikel konnte nicht in den Warenkorb gelegt werden.', true);
                        }
                    })
                    .catch(error => {
                        showToast('Error adding item to cart.', true);
                    });
                });
            });

            updateLocalCart();
        });

        window.artikellisteEventListenerAttached = true;
    }
    </script>
</body>
</html>