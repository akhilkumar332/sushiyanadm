<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// List of branches
$branches = [
    'charlottenburg', 'friedrichshain', 'lichtenrade', 'mitte', 'moabit', 
    'neukoelln', 'potsdam', 'rudow', 'spandau', 'tegel', 'weissensee', 
    'zehlendorf', 'FFO'
];
?>
<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="<?php echo URL_IMPRESSUM; ?>">Impressum</a>
            <span class="footer-separator">|</span>
            <a href="<?php echo URL_DATENSCHUTZ; ?>">Datenschutz</a>
        </div>
        <div class="branch-selector">
            <label for="branch-dropdown" class="branch-label">Filiale wählen:</label>
            <select id="branch-dropdown" name="branch">
                <?php foreach ($branches as $branch): ?>
                    <option value="<?php echo htmlspecialchars($branch); ?>" 
                            <?php echo $filiale === $branch ? 'selected' : ''; ?>>
                        <?php echo ucfirst(htmlspecialchars($branch)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="footer-divider"></div>
        <p class="footer-text">Sushi Yana © All Rights Reserved. 2025</p>
    </div>
</footer>