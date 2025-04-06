<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// List of branches
$branches = [
    'charlottenburg', 'friedrichshain', 'lichtenrade', 'mitte', 'moabit', 
    'neukoelln', 'potsdam', 'rudow', 'spandau', 'tegel', 'weissensee', 
    'zehlendorf', 'FFO'
];

// List of languages
$languages = [
    'de' => 'Deutsch',
    'en' => 'English',
    'fr' => 'Français',
    'pl' => 'Polski',
    'it' => 'Italiano',
    'ru' => 'Русский',
    'tr' => 'Türkçe',
    'es' => 'Español',
    'ar' => 'العربية'
];

// Get current language from session
$current_lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'de';
?>

<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="<?php echo URL_IMPRESSUM; ?>" data-translate="impressum">Impressum</a>
            <span class="footer-separator">|</span>
            <a href="<?php echo URL_DATENSCHUTZ; ?>" data-translate="datenschutz">Datenschutz</a>
        </div>
        <div class="branch-selector">
            <label for="branch-dropdown" class="branch-label" data-translate="select_branch">Filiale wählen:</label>
            <select id="branch-dropdown" name="branch">
                <?php foreach ($branches as $branch): ?>
                    <option value="<?php echo htmlspecialchars($branch); ?>" 
                            <?php echo $filiale === $branch ? 'selected' : ''; ?>>
                        <?php echo ucfirst(htmlspecialchars($branch)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="language-selector">
            <label for="language-dropdown" class="language-label" data-translate="select_language">Sprache wählen:</label>
            <select id="language-dropdown" name="language">
                <?php foreach ($languages as $code => $name): ?>
                    <option value="<?php echo htmlspecialchars($code); ?>" 
                            <?php echo $current_lang === $code ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="footer-divider"></div>
        <p class="footer-text" data-translate="footer_copyright">Sushi Yana © All Rights Reserved. 2025</p>
    </div>
</footer>