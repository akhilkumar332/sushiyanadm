<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!session_id()) {
    session_start();
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$current_lang = isset($_SESSION['language']) ? $_SESSION['language'] : (isset($_POST['lang']) ? $_POST['lang'] : 'de');
$filiale = isset($filiale) ? $filiale : (isset($_POST['filiale']) ? $_POST['filiale'] : DB_FILIALE);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table'])) {
    $table = $_POST['table'];
    $sql = "SELECT * FROM " . mysqli_real_escape_string($conn, $table) . " ORDER BY artikelnummer ASC";
    $result = $conn->query($sql);
} elseif (isset($result) && isset($table)) { // Only for direct includes
    // No action needed, variables are already set by the including script
} else {
    // For invalid AJAX or unexpected calls, exit gracefully
    exit;
}
?>

<div class="grid-container">
    <?php
    if ($result instanceof mysqli_result && $result->num_rows > 0) {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $items_to_translate = [];
        $item_data = [];
        foreach ($rows as $row) {
            $isAvailable = isset($row[$filiale]) && (int)$row[$filiale] === 1;
            if ($isAvailable) {
                $items_to_translate[] = $row['artikelname'];
                $items_to_translate[] = $row['beschreibung'];
                $items_to_translate[] = $row['allergene_zusatz'] ?: '';
                $item_data[] = $row;
            }
        }
        $translated_texts = translateText($items_to_translate, 'de', $current_lang, $conn);

        if (count($translated_texts) !== count($items_to_translate)) {
            die('Error: Translation count mismatch.');
        }

        $text_index = 0;
        foreach ($item_data as $row) {
            $artikelname = $translated_texts[$text_index++];
            $beschreibung_unused = $translated_texts[$text_index++]; // Not used in grid
            $allergene_unused = $translated_texts[$text_index++]; // Not used in grid
            $item_key = htmlspecialchars($table . ':' . (int)$row["id"]);
            $quantity_in_cart = isset($_SESSION['cart'][$item_key]['quantity']) ? (int)$_SESSION['cart'][$item_key]['quantity'] : 0;
            ?>
            <div class="grid-item">
                <div class="dish-image-container">
                    <img src="<?php echo htmlspecialchars(ASSETS_IMAGES . substr((string)$row['image'], 7)); ?>" 
                         onerror="this.src='<?php echo ASSETS_IMAGES; ?>deafult.jpg';" 
                         alt="<?php echo htmlspecialchars($artikelname); ?>" 
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
                    <h3 class="dishname"><?php echo htmlspecialchars($row["artikelnummer"] . " " . $artikelname); ?></h3>
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
                        <button type="button" class="btn-increment" data-item-key="<?php echo $item_key; ?>" data-product-name="<?php echo htmlspecialchars($artikelname); ?>">
                            <span>+</span>
                            <span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="no-items" data-translate="no_items_found">Keine Artikel gefunden.</div>
        <?php
    }
    ?>
</div>

<?php
if ($result instanceof mysqli_result) {
    $result->data_seek(0);
    $modal_texts = [];
    $modal_data = [];
    while ($row = $result->fetch_assoc()) {
        $isAvailable = isset($row[$filiale]) && (int)$row[$filiale] === 1;
        if ($isAvailable) {
            $modal_texts[] = $row['beschreibung'];
            $modal_texts[] = $row['allergene_zusatz'] ?: '';
            $modal_data[] = $row;
        }
    }
    $translated_modals = translateText($modal_texts, 'de', $current_lang, $conn);

    if (count($translated_modals) !== count($modal_texts)) {
        die('Error: Modal translation count mismatch.');
    }

    $modal_index = 0;
    foreach ($modal_data as $row) {
        $beschreibung = $translated_modals[$modal_index++];
        $allergene_zusatz = $translated_modals[$modal_index++];
        ?>
        <div id="myModal<?php echo (int)$row["id"]; ?>" class="modal">
            <div class="modal-content">
                <span class="close" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Modal schließen">×</span>
                <h2 data-translate="allergens_additives">Allergene und Zusatzstoffe</h2>
                <div><?php echo $allergene_zusatz; ?></div>
            </div>
        </div>
        <div id="ingredientsModal<?php echo (int)$row["id"]; ?>" class="modal">
            <div class="modal-content">
                <span class="close" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Modal schließen">×</span>
                <h2 data-translate="ingredients">Zutaten</h2>
                <div><?php echo $beschreibung; ?></div>
            </div>
        </div>
        <?php
        // Add add-on modal for insideoutrolls
        if ($table === 'insideoutrolls') {
            $addons = getAddonsForCategory('insideoutrolls', $conn);
            $translated_addon_names = translateText(array_column($addons, 'name'), 'de', $current_lang, $conn);
            ?>
            <div id="addonModal<?php echo (int)$row["id"]; ?>" class="modal addon-modal">
                <div class="modal-content">
                    <span class="close addon-close" data-id="<?php echo (int)$row["id"]; ?>" aria-label="Modal schließen">×</span>
                    <h2>Add-Ons: <?php echo htmlspecialchars($row['artikelname']); ?></h2>
                    <form class="addon-form" data-item-key="<?php echo htmlspecialchars($table . ':' . (int)$row["id"]); ?>">
                        <p data-translate="select_one_addon">Bitte wählen Sie ein Add-On aus (maximal 1):</p>
                        <?php
                        foreach ($addons as $index => $addon) {
                            ?>
                            <label>
                                <input type="radio" name="addon" value="<?php echo (int)$addon['id']; ?>" class="addon-radio">
                                <?php echo htmlspecialchars($translated_addon_names[$index]); ?> (<?php echo number_format($addon['price'], 2, ',', '.'); ?> €)
                            </label><br>
                            <?php
                        }
                        ?>
                        <button type="submit" class="btn addon-submit" data-translate="add_to_cart">Zum Warenkorb hinzufügen</button>
                    </form>
                </div>
            </div>
            <?php
        }
    }
}
?>