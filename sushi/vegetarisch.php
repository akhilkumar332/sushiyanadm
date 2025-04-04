<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Vegetarisch</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>style-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo ASSETS_SCRIPTS; ?>skripte.js"></script>
</head>
<body class="artikelliste" data-page="sushi_vegetarisch" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div class="content">
        <h1>Menüs</h1>
        <?php
        $table = 'menues';
        $sql = "SELECT *, 'menues' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Makis <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'makis';
        $sql = "SELECT *, 'makis' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Inside Out Rolls <span class="anzahl">- je 8 Stück</span></h1>
        <?php
        $table = 'insideoutrolls';
        $sql = "SELECT *, 'insideoutrolls' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Mini Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'miniyanarolls';
        $sql = "SELECT *, 'miniyanarolls' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        $table = 'yanarolls';
        $sql = "SELECT *, 'yanarolls' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Nigiris <span class="anzahl">- 1 Stück</span></h1>
        <?php
        $table = 'nigiris';
        $sql = "SELECT *, 'nigiris' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Special Rolls <span class="anzahl">- je 8 Stück</span></h1>
        <?php
        $table = 'specialrolls';
        $sql = "SELECT *, 'specialrolls' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>

        <h1>Temaki <span class="anzahl">- 1 Stück</span></h1>
        <?php
        $table = 'temaki';
        $sql = "SELECT *, 'temaki' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>
    </div>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner"></div>
    <?php $conn->close(); ?>
</body>
</html>