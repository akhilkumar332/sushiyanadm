<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
date_default_timezone_set('Europe/Berlin');

$page_title = 'Bestellungen';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="navigation" data-page="online_orders" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart='<?php echo json_encode($_SESSION['cart'] ?? []); ?>'>
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="orders-wrapper">
            <div class="orders-header">
                <h1>Online-Bestellungen</h1>
            </div>
            <div class="orders-filters">
                <div class="branch-filter">
                    <label for="branch-filter">Filiale:</label>
                    <select id="branch-filter">
                        <option value="">Alle Filialen</option>
                        <!-- Branches populated dynamically via JavaScript -->
                    </select>
                </div>
                <div class="date-filter">
                    <label for="date-filter">Zeitraum:</label>
                    <select id="date-filter">
                        <option value="daily" selected>Täglich</option>
                        <option value="weekly">Wöchentlich</option>
                        <option value="monthly">Monatlich</option>
                        <option value="quarterly">Quartal</option>
                        <option value="semi-annually">Halbjährlich</option>
                        <option value="annually">Jährlich</option>
                    </select>
                </div>
                <div class="order-filter">
                    <label for="order-filter">Sortieren:</label>
                    <select id="order-filter">
                        <option value="desc" selected>Absteigend</option>
                        <option value="asc">Aufsteigend</option>
                    </select>
                </div>
            </div>
            <div class="orders-tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="active">Aktive Bestellungen</button>
                    <button class="tab-btn" data-tab="completed">Abgeschlossene Bestellungen</button>
                </div>
                <div class="tab-content active" id="active-orders">
                    <div class="orders-list" id="active-orders-list"></div>
                    <div class="pagination" id="active-pagination"></div>
                </div>
                <div class="tab-content" id="completed-orders">
                    <div class="orders-list" id="completed-orders-list"></div>
                    <div class="pagination" id="completed-pagination"></div>
                </div>
            </div>
        </div>
    </main>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner" class="loading-spinner"></div>
</body>
</html>