<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Ensure session is started and cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if required variables are defined (passed from parent script)
if (!isset($result) || !isset($table) || !isset($filiale)) {
    die('Error: Required variables ($result, $table, $filiale) are not defined.');
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>style-menu.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <title>Menu Items</title>
</head>
<body class="artikelliste">
    <div class="grid-container">
        <?php
        if ($result instanceof mysqli_result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Type-safe check for $row[$filiale]
                $isAvailable = isset($row[$filiale]) && (int)$row[$filiale] === 1;
                if ($isAvailable) {
                    $item_key = htmlspecialchars($table . ':' . (int)$row["id"]);
                    $quantity_in_cart = isset($_SESSION['cart'][$item_key]) ? (int)$_SESSION['cart'][$item_key] : 0;
                    ?>
                    <div class="grid-item">
                        <div class="dish-image-container">
                            <img src="<?php echo htmlspecialchars(ASSETS_IMAGES . substr((string)$row['image'], 1)); ?>" 
                                 onerror="this.src='https://placehold.co/150';" 
                                 alt="<?php echo htmlspecialchars((string)$row["artikelname"]); ?>" 
                                 class="dish-image">
                        </div>
                        <div class="action-buttons">
                            <button class="info-button" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Allergene und Zusatzstoffe anzeigen">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button class="ingredients-button" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Zutaten anzeigen">
                                <i class="fas fa-utensils"></i>
                            </button>
                            <form action="<?php echo URL_SUSHI_VEGETARISCH; ?>" class="inline-form">
                                <input type="hidden" name="item_key" value="<?php echo $item_key; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="cart-button-list" aria-label="Zum Warenkorb hinzufügen">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="product-count <?php echo $quantity_in_cart > 0 ? 'active' : ''; ?>" data-item-key="<?php echo $item_key; ?>">
                                        <?php echo $quantity_in_cart; ?>
                                    </span>
                                </button>
                            </form>
                        </div>
                        <div class="dish-details">
                            <h3 class="dishname"><?php echo htmlspecialchars((string)$row["artikelnummer"] . " " . (string)$row["artikelname"]); ?></h3>
                            <div class="mini-logo-container">
                                <?php
                                if (isset($row["pikant"]) && (int)$row["pikant"] === 1) {
                                    echo '<img src="' . ASSETS_IMAGES . 'icons/pikant.png" alt="Pikant" class="mini-logo">';
                                }
                                if (isset($row["vegetarisch"]) && (int)$row["vegetarisch"] === 1) {
                                    echo '<img src="' . ASSETS_IMAGES . 'icons/vegetarisch.png" alt="Vegetarisch" class="mini-logo">';
                                }
                                if (isset($row["vegan"]) && (int)$row["vegan"] === 1) {
                                    echo '<img src="' . ASSETS_IMAGES . 'icons/vegan.png" alt="Vegan" class="mini-logo">';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="price-button-container">
                            <button class="price-button" aria-label="Preis: <?php echo htmlspecialchars((string)$row["preis"]); ?>">
                                <?php echo htmlspecialchars((string)$row["preis"]); ?>
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
    <?php
    if ($result instanceof mysqli_result) {
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $isAvailable = isset($row[$filiale]) && (int)$row[$filiale] === 1;
            if ($isAvailable) {
                ?>
                <div id="myModal<?php echo (int)$row["id"]; ?>" class="modal">
                    <div class="modal-content">
                        <span class="close" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Modal schließen">×</span>
                        <h2>Allergene und Zusatzstoffe</h2>
                        <div><?php echo $row["allergene_zusatz"]; ?></div>
                    </div>
                </div>
                <div id="ingredientsModal<?php echo (int)$row["id"]; ?>" class="modal">
                    <div class="modal-content">
                        <span class="close" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Modal schließen">×</span>
                        <h2>Zutaten</h2>
                        <div><?php echo $row["beschreibung"]; ?></div>
                    </div>
                </div>
                <?php
            }
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>