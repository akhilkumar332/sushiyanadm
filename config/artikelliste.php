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
    </style>
</head>
<body class="artikelliste">
<?php
// No session_start() here; handled by the including page (e.g., menu.php)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

echo '<div class="grid-container">';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row[$filiale] == 1) {
            echo '<div class="grid-item">';
            
            // Use absolute path from root, add onerror for fallback
            $image = '/' . $row['image'];
            echo '<img src="' . htmlspecialchars($image) . '" 
                       onerror="this.onerror=null; this.src=\'https://placehold.co/150\';" 
                       alt="' . htmlspecialchars($row["artikelname"]) . '" 
                       class="dish-image">';
            
            echo '<button class="info-button" onclick="openModal(' . $row["id"] . '); event.stopPropagation();">
                    <i class="fas fa-info-circle"></i>
                  </button>';
            
            echo '<button class="ingredients-button" onclick="openIngredientsModal(' . $row["id"] . '); event.stopPropagation();">
                    <i class="fas fa-utensils"></i>
                  </button>';
            
            echo '<div class="dish-details">';
            echo '<h3 class="dishname">' . htmlspecialchars($row["artikelnummer"]) . " " . htmlspecialchars($row["artikelname"]) . '</h3>';
            
            echo '<div class="mini-logo-container">';
            if ($row["pikant"] == 1) {
                echo '<img src="../bilder/icons/pikant.png" alt="Pikant" class="mini-logo">';
            }
            if ($row["vegetarisch"] == 1) {
                echo '<img src="../bilder/icons/vegetarisch.png" alt="Vegetarisch" class="mini-logo">';
            }
            if ($row["vegan"] == 1) {
                echo '<img src="../bilder/icons/vegan.png" alt="Vegan" class="mini-logo">';
            }
            echo '</div>';
            
            echo '</div>';

            echo '<button class="price-button">' . htmlspecialchars($row["preis"]) . '</button>';
            
            echo '<form action="" style="display:inline;">';
            echo '<input type="hidden" name="item_key" value="' . $table . ':' . $row["id"] . '">';
            echo '<input type="hidden" name="quantity" value="1">';
            echo '<button type="submit" name="add_to_cart" class="cart-button-list" onclick="event.stopPropagation();">
                    <i class="fas fa-shopping-cart"></i>
                  </button>';
            echo '</form>';
            
            echo '</div>';

            echo '<div id="myModal' . $row["id"] . '" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal(' . $row["id"] . ')">×</span>
                    <h2>Allergene und Zusatzstoffe</h2>
                    <div>' . $row["allergene_zusatz"] . '</div>
                </div>
              </div>';

            echo '<div id="ingredientsModal' . $row["id"] . '" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeIngredientsModal(' . $row["id"] . ')">×</span>
                    <h2>Zutaten</h2>
                    <div>' . $row["beschreibung"] . '</div>
                </div>
              </div>';
        }
    }
} else {
    echo '<div class="no-items">Keine Artikel gefunden.</div>';
}

echo '</div>';
?>

<!-- Add Toastify JS -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
function openModal(id) {
    document.getElementById('myModal' + id).style.display = "block";
}

function closeModal(id) {
    document.getElementById('myModal' + id).style.display = "none";
}

function openIngredientsModal(id) {
    document.getElementById('ingredientsModal' + id).style.display = "block";
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
        }
    }
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = total.toString();
    }
}

// Show Toastify notification
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

document.addEventListener('DOMContentLoaded', function() {
    // Intercept form submissions for cart buttons
    const cartForms = document.querySelectorAll('form[action=""]');
    cartForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const button = form.querySelector('.cart-button-list');
            if (!button) {
                return;
            }

            // Extract item_key and quantity from the form
            const itemKeyInput = form.querySelector('input[name="item_key"]');
            const quantityInput = form.querySelector('input[name="quantity"]');
            if (!itemKeyInput || !quantityInput) {
                return;
            }

            const itemKey = itemKeyInput.value;
            const quantity = parseInt(quantityInput.value) || 1;
            const [table, itemId] = itemKey.split(':');
            if (!itemId || !table) {
                return;
            }

            // Add item to cart via AJAX to vegetarisch.php
            fetch('/sushi/vegetarisch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${encodeURIComponent(itemId)}&table=${encodeURIComponent(table)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateLocalCart();
                    showToast('Item added to cart!');
                } else {
                    showToast('Failed to add item to cart.', true);
                }
            })
            .catch(error => {
                showToast('Error adding item to cart.', true);
            });
        });
    });

    // Initial cart update
    updateLocalCart();
});
</script>
</body>
</html>