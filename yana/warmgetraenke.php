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
        <h1>Warme Getränke</h1>
        <?php
        include '../config/config.php';

        // Verbindung erstellen
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Verbindung überprüfen
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


        // SQL-Abfrage zum Abrufen der Menüs
        $sql = "SELECT * FROM `warmgetraenke` ORDER BY `warmgetraenke`.`artikelnummer` ASC;";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Datensätze ausgeben
            while($row = $result->fetch_assoc()) {
                if ($row[$filiale] == 1) {
                    echo '<div class="dish" onclick="toggleDescription(\'desc' . $row["id"] . '\')">';
                    echo '<h3>';

                    echo '<span class="dishname">'  . " " . htmlspecialchars($row["artikelname"]) . '</span>';
                    
                    // Mini-Logos basierend auf den Spalten pikant, vegetarisch und vegan
                    if ($row["pikant"] == 1) {
                        echo '<img src="../bilder/icons/pikant.png" alt="Pikant" class="mini-logo">';
                    }
                    if ($row["vegetarisch"] == 1) {
                        echo '<img src="../bilder/icons/vegetarisch.png" alt="Vegetarisch" class="mini-logo">';
                    }
                    if ($row["vegan"] == 1) {
                        echo '<img src="../bilder/icons/vegan.png" alt="Vegan" class="mini-logo">';
                    }

                    // Info-Logo für Allergenzusätze
                    echo '<img id="img' . $row["id"] . '" src="../bilder/icons/info.png" alt="Info" class="info-logo" onclick="openModal(' . $row["id"] . ')">';
                    echo '<span class="price">' . htmlspecialchars($row["preis"]) . '</span>';
                    echo '</h3>';

                    // Beschreibung mit Allergenzusätzen
                    echo '<p id="desc' . $row["id"] . '" class="description" style="display:none;">' . $row["beschreibung"] .'</p>';
                    echo '</div>';

                    echo '<div id="myModal' . $row["id"] . '" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal(' . $row["id"] . ')">&times;</span>
                            <h2>Allergene und Zusatzstoffe</h2>
                            <div>
                                ' . $row["allergene_zusatz"] . '
                            </div>
                        </div>
                    </div>';
                }
            

            }
        } else {
            echo "Keine Artikel gefunden.";
        }

        // Verbindung schließen
        $conn->close();
        ?>
    </div>


    <footer>
        <a href="../index.html">Zurück zur Startseite</a> - <a href="../impressum.html">Impressum</a> - <a href="../datenschutz.html">Datenschutz</a>
    </footer>
</body>
</html>
