<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Ensure session is started and cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if this is an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && isset($_POST['filiale'])) {
    $table = $_POST['table'];
    $filiale = $_POST['filiale'];
    $sql = "SELECT * FROM " . mysqli_real_escape_string($conn, $table) . " ORDER BY artikelnummer ASC";
    $result = $conn->query($sql);
} else {
    // Check if required variables are defined (passed from parent script)
    if (!isset($result) || !isset($table) || !isset($filiale)) {
        die('Error: Required variables ($result, $table, $filiale) are not defined.');
    }
}
?>

<div class="grid-container">
    <?php
    if ($result instanceof mysqli_result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $isAvailable = isset($row[$filiale]) && (int)$row[$filiale] === 1;
            if ($isAvailable) {
                $item_key = htmlspecialchars($table . ':' . (int)$row["id"]);
                $quantity_in_cart = isset($_SESSION['cart'][$item_key]) ? (int)$_SESSION['cart'][$item_key] : 0;
                ?>
                <div class="grid-item">
                    <div class="dish-image-container">
                        <img src="<?php echo htmlspecialchars(ASSETS_IMAGES . substr((string)$row['image'], 7)); ?>" 
                             onerror="this.src='<?php echo ASSETS_IMAGES; ?>/deafult.jpg';" 
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
                        <div class="cart-display <?php echo $quantity_in_cart > 0 ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="product-count" data-item-key="<?php echo $item_key; ?>">
                                <?php echo $quantity_in_cart; ?>
                            </span>
                        </div>
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
                        <div class="quantity-controls">
                            <button type="button" class="btn-decrement" data-item-key="<?php echo $item_key; ?>">
                                <span>-</span>
                                <span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                            <button class="price-button" aria-label="Preis: <?php echo htmlspecialchars((string)$row["preis"]); ?>">
                                <?php echo htmlspecialchars((string)$row["preis"]); ?>
                            </button>
                            <button type="button" class="btn-increment" data-item-key="<?php echo $item_key; ?>">
                                <span>+</span>
                                <span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
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