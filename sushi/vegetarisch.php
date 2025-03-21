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
        // Fetch vegetarian items from menues
        $table = 'menues';
        $sql = "SELECT * FROM `menues` WHERE vegetarisch = 1 ORDER BY `menues`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Makis <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'makis';
        $sql = "SELECT * FROM `makis` WHERE vegetarisch = 1 ORDER BY `makis`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Inside Out Rolls <span class="anzahl">- je 8 Stück</span></h1>
        <?php
        $table = 'insideoutrolls';
        $sql = "SELECT * FROM `insideoutrolls` WHERE vegetarisch = 1 ORDER BY `insideoutrolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Mini Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'miniyanarolls';
        $sql = "SELECT * FROM `miniyanarolls` WHERE vegetarisch = 1 ORDER BY `miniyanarolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'yanarolls';
        $sql = "SELECT * FROM `yanarolls` WHERE vegetarisch = 1 ORDER BY `yanarolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Nigiris <span class="anzahl">- 1 Stück</span></h1>
        <?php
        $table = 'nigiris';
        $sql = "SELECT * FROM `nigiris` WHERE vegetarisch = 1 ORDER BY `nigiris`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Special Rolls <span class="anzahl">- je 8 Stück</span></h1>
        <?php
        $table = 'specialrolls';
        $sql = "SELECT * FROM `specialrolls` WHERE vegetarisch = 1 ORDER BY `specialrolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <h1>Temaki <span class="anzahl">- 1 Stück</span></h1>
        <?php
        $table = 'temaki';
        $sql = "SELECT * FROM `temaki` WHERE vegetarisch = 1 ORDER BY `temaki`.`artikelnummer` ASC";
        $result = $conn->query($sql);
        include '../config/artikelliste.php';
        ?>

        <?php
        // Close the database connection
        $conn->close();
        ?>
    </div>

    <?php include_once '../config/floating_bar.php'; ?>
    <?php include_once '../config/footer.php'; ?>
</body>
</html>