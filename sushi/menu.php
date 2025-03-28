<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo "<script>
        if (localStorage.getItem('cart')) {
            fetch('" . URL_RESTORE_CART . "', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart=' + encodeURIComponent(localStorage.getItem('cart'))
            })
            .then(response => response.text())
            .then(text => JSON.parse(text.replace(/%+$/, '').trim()))
            .then(data => updateCartCount())
            .catch(error => console.error('Error restoring cart:', error));
        }
    </script>";
}

// Define allowed categories and their display titles
$categories = [
    'insideoutrolls' => 'Inside Out Rolls',
    'makis' => 'Makis',
    'menues' => 'Menüs',
    'miniyanarolls' => 'Mini Yana Rolls',
    'nigiris' => 'Nigiris',
    'sashimi' => 'Sashimi',
    'specialrolls' => 'Special Rolls',
    'temaki' => 'Temaki',
    'yanarolls' => 'Yana Rolls',
];

// Get the category from the URL parameter
$category = isset($_GET['category']) ? strtolower($_GET['category']) : '';

// Validate the category
if (!array_key_exists($category, $categories)) {
    header('Location: ' . URL_HOME);
    exit;
}

// Set the table and title
$table = $category;
$title = $categories[$category];

// Database connection (assumed to be in config.php as $conn)
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_PATH; ?>skripte/skripte.js"></script>
</head>
<body class="artikelliste">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <?php
        $sql = "SELECT * FROM " . mysqli_real_escape_string($conn, $table) . " ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>
    </div>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>

    <?php $conn->close(); ?>
</body>
</html>