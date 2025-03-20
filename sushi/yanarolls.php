<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sushi</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../skripte/skripte.js"></script>
</head>
<body class="artikelliste">
    <header>
        <a href="../"><img src="../bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="content">
        <h1>Yana Rolls <span class="anzahl">- je 6 Stück</span></h1>
        <?php
        include '../config/config.php';

        // Verbindung erstellen
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Verbindung überprüfen
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL-Abfrage zum Abrufen der Menüs
        $sql = "SELECT * FROM `yanarolls` ORDER BY `yanarolls`.`artikelnummer` ASC;";
        $result = $conn->query($sql);

        include '../config/artikelliste.php';

        // Verbindung schließen
        $conn->close();
        ?>
    </div>


    <footer>
        <a href="../index.html">Zurück zur Startseite</a> - <a href="../impressum.html">Impressum</a> - <a href="../datenschutz.html">Datenschutz</a>
    </footer>

    <script>
        function toggleDescription(id) {
            var description = document.getElementById(id);
            if (description.style.display === "none" || description.style.display === "") {
                description.style.display = "block";
            } else {
                description.style.display = "none";
            }
        }

        function openModal(articleId) {
            var modal = document.getElementById('myModal' + articleId);
            modal.style.display = "block";
        }

        function closeModal(articleId) {
            var modal = document.getElementById('myModal' + articleId);
            modal.style.display = "none";
        }

        // Schließen des Modalfensters, wenn der Benutzer außerhalb des Modalfensters klickt
        window.onclick = function(event) {
            var modals = document.getElementsByClassName('modal');
            for (var i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
