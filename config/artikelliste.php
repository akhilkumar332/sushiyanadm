<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style-menu.css"> <!-- Link to your existing CSS -->
    <title>Menu Items</title>
</head>
<body class="artikelliste">

<?php
// Start the grid container
echo '<div class="grid-container">';

if ($result->num_rows > 0) {
    // Output records
    while ($row = $result->fetch_assoc()) {
        if ($row[$filiale] == 1) {
            // Create a grid item for each dish
            echo '<div class="grid-item">';
            
            // Sample online image (replace with actual image path later)
            echo '<img src="https://placehold.co/150" alt="' . htmlspecialchars($row["artikelname"]) . '" class="dish-image">';
            
            // Info button for allergen information
            echo '<button class="info-button" onclick="openModal(' . $row["id"] . '); event.stopPropagation();">
                    <i class="fas fa-info-circle"></i>
                  </button>';
            
            // Ingredients button
            echo '<button class="ingredients-button" onclick="openIngredientsModal(' . $row["id"] . '); event.stopPropagation();">
                    <i class="fas fa-utensils"></i>
                  </button>';
            
            // Dish name and mini logos
            echo '<div class="dish-details">';
            echo '<h3 class="dishname">' . htmlspecialchars($row["artikelnummer"]) . " " . htmlspecialchars($row["artikelname"]) . '</h3>';
            
            // Mini-Logos container
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
            echo '</div>'; // End of mini-logo-container
            
            echo '</div>'; // End of dish-details

            // Price as a button
            echo '<button class="price-button">' . htmlspecialchars($row["preis"]) . '</button>';
            // Cart icon below the info button
            echo '<button class="cart-button" onclick="addToCart(' . $row["id"] . '); event.stopPropagation();">
                    <i class="fas fa-shopping-cart"></i>
                  </button>';
            echo '</div>'; // End of grid-item

            // Modal for allergen information
            echo '<div id="myModal' . $row["id"] . '" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal(' . $row["id"] . ')">&times;</span>
                    <h2>Allergene und Zusatzstoffe</h2>
                    <div>
                        ' . $row["allergene_zusatz"] . '
                    </div>
                </div>
              </div>';

            // Modal for ingredients
            echo '<div id="ingredientsModal' . $row["id"] . '" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeIngredientsModal(' . $row["id"] . ')">&times;</span>
                    <h2>Zutaten</h2>
                    <div>
                        ' . $row["beschreibung"] . '
                    </div>
                </div>
              </div>';
        }
    }
} else {
    echo '<div class="no-items">Keine Artikel gefunden.</div>';
}

// End the grid container
echo '</div>';
?>

<script>
function openModal(id) {
    document.getElementById('myModal' + id).style.display = "block";
}

function closeModal(id) {
    document.getElementById('myModal' + id ).style.display = "none";
}

function openIngredientsModal(id) {
    document.getElementById('ingredientsModal' + id).style.display = "block";
}

function closeIngredientsModal(id) {
    document.getElementById('ingredientsModal' + id).style.display = "none";
}

function addToCart(id) {
    // Functionality to add item to cart
    alert("Item " + id + " added to cart!");
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>