<?php
session_set_cookie_params(['path' => '/']);
session_start();
include '../config/config.php';

// Handle cart fetching request (replaces get_cart.php)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_cart') {
    header('Content-Type: application/json');
    echo json_encode($_SESSION['cart'] ?? []);
    exit;
}

// Handle cart addition request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id']) && isset($_POST['table'])) {
    // Ensure no HTML output before JSON
    ob_start();

    // Set the content type to JSON
    header('Content-Type: application/json');

    // Check if cart session exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Get the item ID and table from the POST request
    $item_id = $_POST['item_id'];
    $table = $_POST['table'];

    // Validate input
    if (!$item_id || !$table) {
        echo json_encode(['status' => 'error', 'message' => 'Missing item_id or table']);
        ob_end_flush();
        exit;
    }

    // Create the item key in the format table:item_id
    $item_key = "$table:$item_id";

    // Add item to cart using the flat structure
    $_SESSION['cart'][$item_key] = ($_SESSION['cart'][$item_key] ?? 0) + 1;

    // Explicitly save the session
    session_write_close();

    // Clear output buffer and send JSON response
    ob_end_clean();
    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
    exit;
}

// If not a cart addition request, proceed with rendering the page
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo "<script>
        if (localStorage.getItem('cart')) {
            fetch('../restore_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart=' + encodeURIComponent(localStorage.getItem('cart'))
            })
            .then(response => response.json())
            .then(data => {
                updateCartCount();
            })
            .catch(error => {});
        }
    </script>";
}

// Use $filiale from config.php (e.g., "neukoelln")
global $filiale;

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Vegetarisch</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../skripte/skripte.js"></script>
</head>
<body class="artikelliste">
    <header>
        <a href="../"><img src="../bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
        <h1>Menüs</h1>
        <?php
        $table = 'menues';
        $sql = "SELECT *, 'menues' AS source_table FROM `menues` WHERE vegetarisch = 1 ORDER BY `menues`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Makis <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'makis';
        $sql = "SELECT *, 'makis' AS source_table FROM `makis` WHERE vegetarisch = 1 ORDER BY `makis`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Inside Out Rolls <span class="anzahl">- je 8 Stück</span></h1>
        <?php
        $table = 'insideoutrolls';
        $sql = "SELECT *, 'insideoutrolls' AS source_table FROM `insideoutrolls` WHERE vegetarisch = 1 ORDER BY `insideoutrolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Mini Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'miniyanarolls';
        $sql = "SELECT *, 'miniyanarolls' AS source_table FROM `miniyanarolls` WHERE vegetarisch = 1 ORDER BY `miniyanarolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'yanarolls';
        $sql = "SELECT *, 'yanarolls' AS source_table FROM `yanarolls` WHERE vegetarisch = 1 ORDER BY `yanarolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Nigiris <span class="anzahl">- 1 Stück</span></h1>
        <?php
        $table = 'nigiris';
        $sql = "SELECT *, 'nigiris' AS source_table FROM `nigiris` WHERE vegetarisch = 1 ORDER BY `nigiris`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Special Rolls <span class="anzahl">- je 8 Stück</span></h1>
        <?php
        $table = 'specialrolls';
        $sql = "SELECT *, 'specialrolls' AS source_table FROM `specialrolls` WHERE vegetarisch = 1 ORDER BY `specialrolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Temaki <span class="anzahl">- 1 Stück</span></h1>
        <?php
        $table = 'temaki';
        $sql = "SELECT *, 'temaki' AS source_table FROM `temaki` WHERE vegetarisch = 1 ORDER BY `temaki`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>
    </div>

    <?php include_once '../config/floating_bar.php'; ?>
    <?php include_once '../config/footer.php'; ?>

    <?php $conn->close(); ?>

    <script>
        // Initial cart update (optional, since artikelliste.php already does this)
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(updateCartCount, 1000);
        });
    </script>
</body>
</html>