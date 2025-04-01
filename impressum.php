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
            .catch(error => {});
        }
    </script>";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Impressum</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="navigation">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="content legal-content">
            <h1>Impressum</h1>
            <p><strong>Verantwortlich für die Startseite und die weiteren Informationsseiten (ohne Onlineshops) dieser Webseite ist:</strong></p>
            <p>Sushi Yana Neukölln<br>
            Flughafenstraße 76<br>
            12049 Berlin<br>
            <a href="mailto:neukoelln@sushi-yana.de" class="email-link">neukoelln@sushi-yana.de</a></p>
            <p>Geschäftsführer: Hussein Hamid <br>
            Steuernummer: 16/329/04249<br>
            <p>Jede Sushi Yana Filiale wird von einem selbstständig tätigen Gewerbetreibenden als Franchisenehmer bewirtschaftet. Dieser organisiert Produktion und Auslieferung seiner Produkte für seinen Betrieb in eigener Verantwortung. Wenn du Fragen oder Anliegen zu deiner Lieferung hast, wende dich bitte an den Verantwortlichen des jeweiligen Betriebes, den du in vorstehender Liste finden kannst.</p>
            <p>Unsere Franchisezentrale erreichen Sie über die Mailadresse <a href="mailto:buero@sushi-yana.de" class="email-link">buero@sushi-yana.de</a>. Bitte nutzen Sie diese ausschließlich für allgemeine Anfragen.</p>
        </div>
    </main>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
</body>
</html>