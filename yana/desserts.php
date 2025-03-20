<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digitale Speisekarte</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../skripte/skripte.js"></script>
</head>
<body class="artikelliste">
    <header>
        <a href="../"><img src="../bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
        <h1>Dessert´s</h1>
        <?php
        include '../config/config.php';

        // Verbindung erstellen
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Verbindung überprüfen
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


        // SQL-Abfrage zum Abrufen der Menüs
        $sql = "SELECT * FROM `desserts` ORDER BY `desserts`.`artikelnummer` ASC;";
        $result = $conn->query($sql);

        
        include '../config/artikelliste.php';

        // Verbindung schließen
        $conn->close();
        ?>
    </div>


    <footer>
        <a href="../index.html">Zurück zur Startseite</a> - <a href="../impressum.html">Impressum</a> - <a href="../datenschutz.html">Datenschutz</a>
    </footer>
</body>
</html>
