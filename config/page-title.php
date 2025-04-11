<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// List of branches
$branches = [
    'brandenburg', 'charlottenburg', 'friedrichshain', 'lichtenrade', 'mitte', 'moabit', 
    'neukoelln', 'potsdam', 'rudow', 'schoneweide', 'spandau', 'tegel', 'weissensee', 
    'zehlendorf', 'frankfurt(oder)'
];

// List of languages
$languages = [
    'de' => '🇩🇪 Deutsch',
    'en' => '🇬🇧 English',
    'fr' => '🇫🇷 Français',
    'pl' => '🇵🇱 Polski',
    'it' => '🇮🇹 Italiano',
    'ru' => '🇷🇺 Русский',
    'tr' => '🇹🇷 Türkçe',
    'es' => '🇪🇸 Español',
    'ar' => '🇦🇪 العربية',
];

// Get current language from session
$current_lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'de';

// Determine page title based on the current page (passed as a variable when including this file)
$page_title = isset($page_title) ? $page_title : 'Default Title';
$data_translate = isset($data_translate) ? $data_translate : 'default';

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// List of pages where the title should NOT be displayed
$excluded_pages = [
    'online-orders.php',
    'datenschutz.php',
    'impressum.php',
    'cart.php',
    'final_order.php'
];
?>

<!-- Container for Branch and Language Selectors -->
<div class="selector-container">
    <!-- Branch Selector -->
    <div class="branch-selector">
        <label for="branch-dropdown" class="branch-label" data-translate="select_branch">Filiale:</label>
        <select id="branch-dropdown" name="branch">
            <?php foreach ($branches as $branch): ?>
                <option value="<?php echo htmlspecialchars($branch); ?>" 
                        <?php echo isset($filiale) && $filiale === $branch ? 'selected' : ''; ?>>
                    <?php echo ucfirst(htmlspecialchars($branch)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Language Selector -->
    <div class="language-selector">
        <label for="language-dropdown" class="language-label" data-translate="select_language">Sprache:</label>
        <select id="language-dropdown" name="language">
            <?php foreach ($languages as $code => $name): ?>
                <option value="<?php echo htmlspecialchars($code); ?>" 
                        <?php echo $current_lang === $code ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Page Title (Conditionally Displayed) -->
<?php if (!in_array($current_page, $excluded_pages)): ?>
    <div>
        <h1 class="page-title" data-translate="<?php echo htmlspecialchars($data_translate); ?>">
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
    </div>
<?php endif; ?>

<!-- CSS for Inline and Mobile-Responsive Layout -->
<style>
    .selector-container {
        display: flex;
        justify-content: space-between; /* Distributes space between selectors */
        align-items: center; /* Vertically aligns items */
        gap: 15px; /* Space between selectors */
        flex-wrap: nowrap; /* Prevents wrapping to new line */
        padding: 10px; /* Adds some padding around the container */
        max-width: 100%; /* Ensures it fits within the screen */
    }

    .branch-selector, .language-selector {
        display: flex;
        align-items: center;
        gap: 8px; /* Space between label and dropdown */
        flex: 1; /* Allows both selectors to grow/shrink equally */
        min-width: 0; /* Prevents overflow on small screens */
    }

    .branch-label, .language-label {
        white-space: nowrap; /* Prevents label text from wrapping */
    }

    select {
        width: 100%; /* Ensures dropdown takes available space */
        max-width: 200px; /* Limits width on larger screens */
        padding: 5px; /* Adds padding for better touch targets */
    }

    /* Media Query for Mobile Devices */
    @media (max-width: 768px) {
        .selector-container {
            gap: 10px; /* Reduces gap on smaller screens */
            padding: 5px; /* Reduces padding */
        }

        .branch-selector, .language-selector {
            gap: 5px; /* Reduces gap between label and dropdown */
        }

        select {
            max-width: 150px; /* Reduces dropdown width on mobile */
            font-size: 14px; /* Slightly smaller font for better fit */
        }

        .branch-label, .language-label {
            font-size: 14px; /* Smaller label text for mobile */
        }
    }

    /* Extra Small Screens (e.g., very narrow phones) */
    @media (max-width: 480px) {
        select {
            max-width: 120px; /* Further reduces dropdown width */
            font-size: 12px; /* Smaller font */
        }

        .branch-label, .language-label {
            font-size: 12px; /* Smaller label text */
        }
    }
</style>