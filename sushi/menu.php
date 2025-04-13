<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prioritize query parameter 'lang' over session, then default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');
$_SESSION['language'] = $current_lang; // Sync session with current language

// Define allowed categories and their display titles with translation keys
$categories = [
    'insideoutrolls' => ['title' => 'Inside Out Rolls', 'key' => 'inside_out_rolls'],
    'makis' => ['title' => 'Makis', 'key' => 'makis'],
    'menues' => ['title' => 'Menüs', 'key' => 'menus'],
    'miniyanarolls' => ['title' => 'Mini Yana Rolls', 'key' => 'mini_yana_rolls'],
    'nigiris' => ['title' => 'Nigiris', 'key' => 'nigiris'],
    'sashimi' => ['title' => 'Sashimi', 'key' => 'sashimi'],
    'specialrolls' => ['title' => 'Special Rolls', 'key' => 'special_rolls'],
    'temaki' => ['title' => 'Temaki', 'key' => 'temaki'],
    'yanarolls' => ['title' => 'Yana Rolls', 'key' => 'yana_rolls'],
];

// Get the category from the URL parameter
$category = isset($_GET['category']) ? strtolower($_GET['category']) : '';

// Validate the category
if (!array_key_exists($category, $categories)) {
    header('Location: ' . URL_HOME . '?lang=' . htmlspecialchars($current_lang));
    exit;
}

// Set the table and translate the title
$table = $category;
$title_key = $categories[$category]['key'];
$title = translateText([$categories[$category]['title']], 'de', $current_lang, $conn)[0];
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title data-translate="sushi_page_title">Digitale Speisekarte - <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'style-menu.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
    <style>
        .addon-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
            overflow-y: auto;
        }

        .addon-modal[style*="display: flex"] {
            opacity: 1;
        }

        .addon-modal .modal-content {
            background-color: #333333; /* Dark background */
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 420px;
            position: relative;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: scale(1);
            transition: transform 0.3s ease-in-out;
            max-height: 90vh;
            overflow-y: auto;
        }

        .addon-modal[style*="display: flex"] .modal-content {
            transform: scale(1);
        }

        .addon-modal .modal-content h2 {
            margin: 0 0 16px;
            font-size: 1.6em;
            font-weight: 600;
            color: #ffffff; /* White text for dark background */
            line-height: 1.3;
        }

        .addon-modal .modal-content .close {
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 1.8em;
            cursor: pointer;
            color: #cccccc; /* Light gray for visibility */
            transition: color 0.2s ease;
            padding: 10px;
        }

        .addon-modal .modal-content .close:hover {
            color: #ffffff; /* White on hover */
        }

        .addon-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .addon-form label {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1em;
            font-weight: 500;
            color: #ffffff; /* White text for dark background */
            cursor: pointer;
            padding: 12px;
            border-radius: 6px;
            transition: background-color 0.2s ease;
            min-height: 48px;
        }

        .addon-form label:hover {
            background-color: #444444; /* Slightly lighter dark for hover */
        }

        .addon-radio {
            width: 24px;
            height: 24px;
            accent-color: #6A2477;
            margin: 0;
            cursor: pointer;
            min-width: 24px;
        }

        .addon-submit {
            background-color: #6A2477;
            color: #ffffff; /* White text, unchanged */
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 500;
            margin-top: 16px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            min-height: 48px;
        }

        .addon-submit:hover {
            background-color: #551c60;
            transform: translateY(-1px);
        }

        .addon-submit:active {
            transform: translateY(0);
        }

        /* Mobile adjustments */
        @media (max-width: 480px) {
            .addon-modal .modal-content {
                padding: 16px;
                max-width: 95%;
                max-height: 85vh;
            }

            .addon-modal .modal-content h2 {
                font-size: 1.4em;
                margin-bottom: 12px;
                color: #ffffff; /* White text */
            }

            .addon-form {
                gap: 10px;
            }

            .addon-form label {
                font-size: 1em;
                padding: 10px;
                min-height: 44px;
                color: #ffffff; /* White text */
            }

            .addon-radio {
                width: 22px;
                height: 22px;
            }

            .addon-submit {
                font-size: 1em;
                padding: 10px;
                min-height: 44px;
            }

            .addon-modal .modal-content .close {
                font-size: 1.6em;
                padding: 12px;
                color: #cccccc; /* Light gray */
            }

            .addon-modal .modal-content .close:hover {
                color: #ffffff; /* White on hover */
            }
        }

        /* Very small screens */
        @media (max-width: 360px) {
            .addon-modal .modal-content {
                padding: 12px;
                max-width: 98%;
            }

            .addon-modal .modal-content h2 {
                font-size: 1.2em;
                margin-bottom: 10px;
                color: #ffffff; /* White text */
            }

            .addon-form label {
                font-size: 0.95em;
                padding: 8px;
                gap: 10px;
                color: #ffffff; /* White text */
            }

            .addon-radio {
                width: 20px;
                height: 20px;
            }

            .addon-submit {
                font-size: 0.95em;
                padding: 8px;
                min-height: 40px;
            }

            .addon-modal .modal-content .close {
                font-size: 1.5em;
                top: 8px;
                right: 12px;
                color: #cccccc; /* Light gray */
            }

            .addon-modal .modal-content .close:hover {
                color: #ffffff; /* White on hover */
            }
        }
    </style>
</head>
<body class="artikelliste" data-page="sushi_menu" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-table="<?php echo htmlspecialchars($table); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <?php 
    $page_title = 'Speisekarte';
    $data_translate = 'page_title';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/page-title.php'; 
    ?>
    <div class="timer" id="inactivity-timer" aria-live="polite" data-translate="inactivity_timer">
        Inaktivitätstimer: <span id="timer-countdown"></span>
    </div>
    <div class="content">
        <h1 data-translate="<?php echo htmlspecialchars($title_key); ?>"><?php echo htmlspecialchars($title); ?></h1>
        <?php
        $sql = "SELECT * FROM " . mysqli_real_escape_string($conn, $table) . " ORDER BY artikelnummer ASC";
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