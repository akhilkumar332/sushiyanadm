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
        <h1>Menüs</h1>
        <?php
        include '../config/config.php';

        // Verbindung erstellen
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Verbindung überprüfen
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL-Abfrage zum Abrufen der Menüs
        $sql = "SELECT * FROM `menues` WHERE vegetarisch = 1 ORDER BY `menues`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        
        include '../config/artikelliste.php';

        echo '<h1>Makis <span class="anzahl">- je 6 Stück</span></h1>';

        $sql = "SELECT * FROM `makis` WHERE vegetarisch = 1 ORDER BY `makis`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';

        echo '<h1>Inside Out Rolls <span class="anzahl">- je 8 Stück</span></h1>';

        $sql = "SELECT * FROM `insideoutrolls` WHERE vegetarisch = 1 ORDER BY `insideoutrolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';
        
        echo '<h1>Mini Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>';

        $sql = "SELECT * FROM `miniyanarolls` WHERE vegetarisch = 1 ORDER BY `miniyanarolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';

        echo '<h1>Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>';

        $sql = "SELECT * FROM `yanarolls` WHERE vegetarisch = 1 ORDER BY `yanarolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';

        echo '<h1>Nigiris <span class="anzahl">- 1 Stück</span></h1>';

        $sql = "SELECT * FROM `nigiris` WHERE vegetarisch = 1 ORDER BY `nigiris`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';

        echo '<h1>Special Rolls <span class="anzahl">- je 8 Stück</span></h1>';

        $sql = "SELECT * FROM `specialRolls` WHERE vegetarisch = 1 ORDER BY `specialRolls`.`artikelnummer` ASC";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';

        echo '<h1>Temaki <span class="anzahl">- 1 Stück</span></h1>';

        $sql = "SELECT * FROM `temaki` WHERE vegetarisch = 1 ORDER BY `temaki`.`artikelnummer` ASC";
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
