<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: " . URL_HOME);
    exit;
}

$tables = [
    'bowls', 'chopsuey', 'desserts', 'erdnussgericht', 'extras', 'extrasWarm', 'fingerfood',
    'gemuese', 'getraenke', 'gyoza', 'insideoutrolls', 'makis', 'mangochutney', 'menues',
    'miniyanarolls', 'nigiris', 'nudeln', 'redcurry', 'reis', 'salate', 'sashimi',
    'sommerrollen', 'specialrolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
    'yanarolls', 'yellowcurry'
];

// Get current timestamp in German format
date_default_timezone_set('Europe/Berlin');
$timestamp = date('d.m.Y H:i:s');
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Rechnung</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/skripte/skripte.js"></script>
</head>
<body class="navigation" data-page="final_order" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="bill-wrapper">
            <div class="bill-header">
                <h1>Ihre Rechnung - Bestellbestätigung</h1>
                <p><?php echo $timestamp; ?></p>
            </div>
            <div class="bill-details">
                <table class="bill-table">
                    <thead>
                        <tr>
                            <th>Nummer</th>
                            <th>Artikel</th>
                            <th class="quantity">Menge</th>
                            <th class="price">Einzelpreis</th>
                            <th class="subtotal">Gesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item_key => $quantity) {
                            if (strpos($item_key, ':') === false) {
                                error_log("Invalid cart item key: $item_key");
                                continue;
                            }
                            list($table, $item_id) = explode(':', $item_key);
                            if (!in_array($table, $tables)) continue;
                            $stmt = $conn->prepare("SELECT artikelnummer, artikelname, preis FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                            $stmt->bind_param("i", $item_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $item = $result->fetch_assoc();
                            $price = floatval($item['preis']);
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['artikelnummer']); ?></td>
                                <td><?php echo htmlspecialchars($item['artikelname']); ?></td>
                                <td class="quantity"><?php echo $quantity; ?></td>
                                <td class="price"><?php echo number_format($price, 2); ?> €</td>
                                <td class="subtotal"><?php echo number_format($subtotal, 2); ?> €</td>
                            </tr>
                        <?php $stmt->close(); } ?>
                    </tbody>
                </table>
                <div class="bill-total">
                    <span>Gesamtbetrag</span>
                    <span><?php echo number_format($total, 2); ?> €</span>
                </div>
                <div class="timer" id="inactivity-timer" aria-live="polite">
                    Inaktivitätstimer: <span id="timer-countdown"></span>
                </div>
            </div>
            <div class="bill-buttons">
                <button class="btn" id="back-to-home">Löschen & Zurückgeben</button>
            </div>
        </div>
    </main>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
</body>
</html>