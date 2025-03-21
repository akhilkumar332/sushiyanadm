<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style-menu.css">
    <title>Menu Items</title>
</head>
<body class="artikelliste">
<?php
// No session_start() here; handled by suppen.php
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
            
            echo '<form method="POST" action="" style="display:inline;">';
            echo '<input type="hidden" name="item_key" value="' . $table . ':' . $row["id"] . '">';
            echo '<input type="hidden" name="quantity" value="1">';
            echo '<button type="submit" name="add_to_cart" class="cart-button" onclick="event.stopPropagation();">
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

if (isset($_POST['add_to_cart'])) {
    $item_key = $_POST['item_key'];
    $quantity = (int)$_POST['quantity'];
    if (isset($_SESSION['cart'][$item_key])) {
        $_SESSION['cart'][$item_key] += $quantity;
    } else {
        $_SESSION['cart'][$item_key] = $quantity;
    }
    echo "<script>updateLocalCart(); alert('Item added to cart!');</script>";
}
?>

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
    const cart = <?php echo json_encode($_SESSION['cart']); ?>;
    localStorage.setItem('cart', JSON.stringify(cart));
    if (document.getElementById('cart-count')) {
        document.getElementById('cart-count').innerText = Object.values(cart).reduce((a, b) => a + b, 0);
    }
}

window.onclick = function(event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none";
        }
    }
}

window.onload = updateLocalCart;
</script>
</body>
</html>