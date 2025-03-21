<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo "<script>
        if (localStorage.getItem('cart')) {
            fetch('restore_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart=' + encodeURIComponent(localStorage.getItem('cart'))
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                const cleanedText = text.replace(/%+$/, '').trim();
                return JSON.parse(cleanedText);
            })
            .then(data => {
                console.log('Cart restored:', data);
                updateCartCount();
            })
            .catch(error => console.error('Error restoring cart:', error));
        }
    </script>";
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Impressum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #000;
        }
        header {
            width: 100%;
            background-color: #000;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .logo {
            max-width: 50%;
            height: auto;
        }
        .content {
            width: 90%;
            max-width: 600px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }
        .email-link {
            color: #0066cc;
            text-decoration: underline;
        }
        footer {
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background-color: #333;
            color: white;
            position: fixed;
            bottom: 0;
        }
         a {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
    <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
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
    <?php include_once './config/floating_bar.php'; ?>
    <?php include_once './config/footer.php'; ?>
</body>
</html>

