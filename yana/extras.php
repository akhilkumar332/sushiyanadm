<?php
session_start();
include '../config/config.php';

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
                console.log('Cart restored:', data);
                updateCartCount();
            })
            .catch(error => console.error('Error restoring cart:', error));
        }
    </script>";
}

// Use $filiale from config.php (e.g., "neukoelln")
global $filiale;
$table = 'extras'; // Corresponding table name

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bowls items
$sql = "SELECT * FROM extras ORDER BY artikelnummer ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Extras</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../skripte/skripte.js"></script>
</head>
<body class="artikelliste">
    <header>
        <a href="../"><img src="../bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
        <h1>Extras</h1>
        <?php include '../config/artikelliste.php'; ?>
    </div>

    <div class="floating-bar">
        <a href="../cart.php" class="cart-icon">
            <span>Cart (<span id="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>)</span>
        </a>
    </div>

    <?php include_once '../config/footer.php'; ?>

    <?php $conn->close(); ?>
</body>
</html>