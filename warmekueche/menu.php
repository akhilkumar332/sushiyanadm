<?php
session_set_cookie_params(['path' => '/']);
session_start();
include '../config/config.php';

// Handle cart fetching request (similar to get_cart.php)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_cart') {
    header('Content-Type: application/json');
    echo json_encode($_SESSION['cart'] ?? []);
    exit;
}

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

// Define allowed categories and their display titles
$categories = [
    'chopsuey' => 'Chop Suey',
    'erdnussgericht' => 'Erdnussgericht',
    'extras' => 'Extras',
    'gemuese' => 'Gemüse',
    'mangochutney' => 'Mango Chutney',
    'nudeln' => 'Nudeln',
    'redcurry' => 'Rotes Curry',
    'reis' => 'Reis',
    'suesssauersauce' => 'Süß Sauer Sauce',
    'yellowcurry' => 'Gelbes Curry',
];

// Get the category from the URL parameter
$category = isset($_GET['category']) ? strtolower($_GET['category']) : '';

// Validate the category
if (!array_key_exists($category, $categories)) {
    // Redirect to a default category or show an error
    header('Location: ../index.php');
    exit;
}

// Set the table and title
$table = $category;
$title = $categories[$category];

// Use $filiale from config.php (e.g., "neukoelln")
global $filiale;

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch items for the given category
$sql = "SELECT * FROM " . $table . " ORDER BY artikelnummer ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../skripte/skripte.js"></script>
    <script>
        // Function to fetch the latest cart data from the server
        function fetchCartData() {
            return fetch(window.location.href + (window.location.search ? '&' : '?') + 'action=get_cart', {
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
        function updateMenuLocalCart() {
            fetchCartData().then(cart => {
                updateCartCount();
            });
        }

        // Update the cart count in the DOM
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            let total = 0;
            for (let itemKey in cart) {
                if (cart.hasOwnProperty(itemKey)) {
                    const quantity = parseInt(cart[itemKey]) || 0;
                    total += quantity;
                }
            }
            const cartCountElement = Array.from(document.querySelectorAll('#cart-count'))
                .find(el => el.closest('body.artikelliste') === document.body);
            if (cartCountElement) {
                cartCountElement.textContent = total.toString();
            }
        }

        // Run initial cart update on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateMenuLocalCart();
            setInterval(updateCartCount, 1000);
        });
    </script>
</head>
<body class="artikelliste">
    <header>
        <a href="../"><img src="../bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <?php include '../config/artikelliste.php'; ?>
    </div>

    <?php include_once '../config/floating_bar.php'; ?>
    <?php include_once '../config/footer.php'; ?>

    <?php $conn->close(); ?>
</body>
</html>