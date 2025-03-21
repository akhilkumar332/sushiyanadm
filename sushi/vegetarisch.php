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
                updateVegetarischCartCount();
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
    <script>
        // Function to fetch the latest cart data from the server
        function fetchCartData() {
            return fetch(window.location.href + '?action=get_cart', {
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
                return data;
            })
            .catch(error => {
                return JSON.parse(localStorage.getItem('cart') || '{}');
            });
        }

        // Update localStorage with the latest cart data
        function updateVegetarischLocalCart() {
            fetchCartData().then(cart => {
                updateVegetarischCartCount();
            });
        }

        // Update the cart count in the DOM
        function updateVegetarischCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            let total = 0;
            // Handle flat cart structure (e.g., {"menues:1004": 1})
            for (let itemKey in cart) {
                if (cart.hasOwnProperty(itemKey)) {
                    const quantity = parseInt(cart[itemKey]) || 0;
                    total += quantity;
                }
            }
            // Target the cart-count element in the main document body
            const cartCountElement = Array.from(document.querySelectorAll('#cart-count'))
                .find(el => el.closest('body.artikelliste') === document.body);
            if (cartCountElement) {
                cartCountElement.textContent = total.toString();
            }
        }
    </script>
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
        // Attach event listeners to cart buttons
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

                    // Extract itemId and table from the form's item_key input
                    const itemKeyInput = form.querySelector('input[name="item_key"]');
                    if (!itemKeyInput) {
                        return;
                    }

                    const [table, itemId] = itemKeyInput.value.split(':');
                    if (!itemId || !table) {
                        return;
                    }

                    // Add item to cart via fetch to the same page (vegetarisch.php)
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `item_id=${encodeURIComponent(itemId)}&table=${encodeURIComponent(table)}`
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            updateVegetarischLocalCart();
                        } catch (error) {
                            updateVegetarischLocalCart();
                        }
                    })
                    .catch(error => {});
                });
            });

            // Initial cart update
            updateVegetarischLocalCart();
            setInterval(updateVegetarischCartCount, 1000);
        });
    </script>
</body>
</html>