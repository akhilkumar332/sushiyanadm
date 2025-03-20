<?php
        if ($result->num_rows > 0) {
         // Datensätze ausgeben
            while($row = $result->fetch_assoc()) {
                if ($row[$filiale] == 1) {
                    echo '<div class="dish" onclick="toggleDescription(\'desc' . $row["id"] . '\')">';
                    echo '<h3>';

                    echo '<img src="../bilder/icons/dropdown.png" alt="Dropdown" class="dropdown">';

                    echo '<span class="dishname">' . htmlspecialchars($row["artikelnummer"]) . " " . htmlspecialchars($row["artikelname"]) . '</span>';
                

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
?>