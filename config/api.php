<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Check if cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// API Endpoints
header('Content-Type: application/json');
ob_start();

try {
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        if ($_GET['action'] === 'get_cart') {
            ob_end_clean();
            echo json_encode(['status' => 'success', 'cart' => $_SESSION['cart']]);
            exit;
        } elseif ($_GET['action'] === 'get_branches') {
            $query = "SELECT DISTINCT branch FROM orders WHERE branch IS NOT NULL AND branch != '' ORDER BY branch ASC";
            $result = $conn->query($query);
            if (!$result) throw new Exception("Query failed: " . $conn->error);
            $branches = [];
            while ($row = $result->fetch_assoc()) {
                $branches[] = $row['branch'];
            }
            $result->free();
            ob_end_clean();
            echo json_encode(['status' => 'success', 'branches' => $branches]);
            exit;
        } elseif ($_GET['action'] === 'get_active_orders' || $_GET['action'] === 'get_completed_orders') {
            $filter = $_GET['filter'] ?? 'daily';
            $order_by = $_GET['order_by'] ?? 'desc';
            $page = max(1, (int)($_GET['page'] ?? 1));
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
            $branch = $_GET['branch'] ?? null;

            $status = $_GET['action'] === 'get_active_orders' ? 'active' : 'completed';
            $date_field = $status === 'active' ? 'created_at' : 'updated_at';
            $query = "SELECT id, order_details, status, branch, created_at, updated_at, table_number FROM orders WHERE status = ?";
            $count_query = "SELECT COUNT(*) as total FROM orders WHERE status = ?";
            $params = [$status];
            $types = "s";

            if ($branch) {
                $query .= " AND branch = ?";
                $count_query .= " AND branch = ?";
                $params[] = $branch;
                $types .= "s";
            }

            switch ($filter) {
                case 'daily':
                    $query .= " AND DATE($date_field) = CURDATE()";
                    $count_query .= " AND DATE($date_field) = CURDATE()";
                    break;
                case 'weekly':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'monthly':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
                case 'quarterly':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                    break;
                case 'semi-annually':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                    break;
                case 'annually':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
            }

            $query .= " ORDER BY $date_field " . ($order_by === 'asc' ? 'ASC' : 'DESC') . " LIMIT ?, ?";
            $params[] = $offset;
            $params[] = $per_page;
            $types .= "ii";

            $stmt = $conn->prepare($query);
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $result = $stmt->get_result();
            $orders = [];

            $tables = ['bowls', 'chopsuey', 'desserts', 'erdnussgericht', 'extras', 'extrasWarm', 'fingerfood',
                       'gemuese', 'getraenke', 'gyoza', 'insideoutrolls', 'makis', 'mangochutney', 'menues',
                       'miniyanarolls', 'nigiris', 'nudeln', 'redcurry', 'reis', 'salate', 'sashimi',
                       'sommerrollen', 'specialrolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
                       'yanarolls', 'yellowcurry'];

            while ($row = $result->fetch_assoc()) {
                $items = json_decode($row['order_details'], true);
                if (!is_array($items)) continue;
                $detailed_items = [];
                $total = 0;
                foreach ($items as $item_key => $quantity) {
                    if (strpos($item_key, ':') === false) continue;
                    list($table, $item_id) = explode(':', $item_key);
                    if (!in_array($table, $tables)) continue;
                    $item_stmt = $conn->prepare("SELECT artikelnummer, artikelname, preis FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                    if (!$item_stmt) continue;
                    $item_stmt->bind_param("i", $item_id);
                    $item_stmt->execute();
                    $item_result = $item_stmt->get_result();
                    $item = $item_result->fetch_assoc();
                    if ($item) {
                        $price = floatval($item['preis']);
                        $subtotal = $price * $quantity;
                        $total += $subtotal;
                        $detailed_items[] = [
                            'artikelnummer' => $item['artikelnummer'],
                            'artikelname' => $item['artikelname'],
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $subtotal
                        ];
                    }
                    $item_stmt->close();
                }
                $row['items'] = $detailed_items;
                $row['total'] = $total;
                $orders[] = $row;
            }
            $stmt->close();

            $count_stmt = $conn->prepare($count_query);
            if (!$count_stmt) throw new Exception("Prepare failed: " . $conn->error);
            $count_stmt->bind_param(substr($types, 0, -2), ...array_slice($params, 0, -2));
            if (!$count_stmt->execute()) throw new Exception("Execute failed: " . $count_stmt->error);
            $count_result = $count_stmt->get_result();
            $total_orders = $count_result->fetch_assoc()['total'];
            $count_stmt->close();

            ob_end_clean();
            echo json_encode([
                'status' => 'success',
                'orders' => $orders,
                'total' => $total_orders,
                'page' => $page,
                'per_page' => $per_page
            ]);
            exit;
        } elseif ($_GET['action'] === 'get_branch_impressum') {
            $branch = $_GET['branch'] ?? $_SESSION['branch'] ?? 'neukoelln';
            $branches_data = [
                'neukoelln' => [
                    'name' => 'Sushi Yana Neukölln',
                    'address' => "Flughafenstraße 76\n12049 Berlin",
                    'email' => 'neukoelln@sushi-yana.de',
                    'manager' => 'Hussein Hamid',
                    'tax_number' => '16/329/04249'
                ],
                'charlottenburg' => [
                    'name' => 'Sushi Yana Charlottenburg',
                    'address' => "Lietzenburger Straße 29\n10789 Berlin",
                    'email' => 'Charlottenburg@sushi-yana.de',
                    'manager' => 'Giorgos Mavridis',
                    'tax_number' => '24/437/02213'
                ],
                'friedrichshain' => [
                    'name' => 'Sushi Yana Friedrichshain',
                    'address' => "Karl-Marx-Allee 140\n10243 Berlin",
                    'email' => 'sushiyana.friedrichshain@web.de',
                    'manager' => 'Aydin Irendeli',
                    'tax_number' => '14/359/02065'
                ],
                'lichtenrade' => [
                    'name' => 'Sushi Yana Lichtenrade',
                    'address' => "Bahnhoffstraße 29\n12305 Berlin",
                    'email' => 'lichtenrade@sushi-yana.de',
                    'manager' => 'Pablo Gonzales',
                    'tax_number' => '2'
                ],
                'mitte' => [
                    'name' => 'Sushi Yana Mall of Berlin GmbH',
                    'address' => "Leipziger Platz 12\n10117 Berlin",
                    'email' => 'buero@sushi-yana.de',
                    'manager' => 'Hussein Hamid',
                    'court' => 'Amtsgericht Charlottenburg',
                    'register' => 'HRB 235940 B',
                    'tax_number' => '1130/553/51695'
                ],
                'moabit' => [
                    'name' => 'Sushi Yana Moabit',
                    'address' => "Gotzkowskystraße 26\n10555 Berlin",
                    'email' => 'moabit@sushi-yana.de',
                    'manager' => 'Hani El-Jamal',
                    'tax_number' => '034/275/02211'
                ],
                'potsdam' => [
                    'name' => 'Sushi Yana Potsdam',
                    'address' => "Kastanienallee 17\n14471 Potsdam",
                    'email' => 'potsdam@sushi-yana.de',
                    'manager' => 'Despoina Pappa',
                    'tax_number' => '046/255/11807'
                ],
                'rudow' => [
                    'name' => 'Sushi Yana GmbH',
                    'address' => "Flughafenstraße 76\n12049 Berlin",
                    'email' => 'buero@sushi-yana.de',
                    'manager' => 'Wesam El-Saadi',
                    'court' => 'Amtsgericht Charlottenburg',
                    'register' => 'HRB 233774 B',
                    'tax_number' => '29/553/32890',
                    'vat' => 'DE347204498'
                ],
                'spandau' => [
                    'name' => 'Sushi Yana Spandau',
                    'address' => "Pichelsdorferstraße 120\n13595 Berlin",
                    'email' => 'spandau@sushi-yana.de',
                    'manager' => 'Ibrahim Hamade',
                    'tax_number' => '19/929/00826'
                ],
                'tegel' => [
                    'name' => 'Sushi Yana Tegel',
                    'address' => "Medebacher Weg 12\n13507 Berlin",
                    'email' => 'tegel@sushi-yana.de',
                    'manager' => 'Nagy Varga',
                    'tax_number' => 'xx/xxx/xxxxx'
                ],
                'weissensee' => [
                    'name' => 'Sushi Yana Weißensee',
                    'address' => "Liebermannstraße 95\n13088 Berlin",
                    'email' => 'buero@sushi-yana.de',
                    'manager' => 'Tolga Cildasi',
                    'tax_number' => 'xx/xxx/xxxxx'
                ],
                'zehlendorf' => [
                    'name' => 'Sushi Yana Zehlendorf',
                    'address' => "Berlinerstraße 67\n14169 Berlin",
                    'email' => 'steglitz@sushi-yana.de',
                    'manager' => 'Mohamed Berjaoui',
                    'tax_number' => 'xx/xxx/xxx'
                ],
                'FFO' => [
                    'name' => 'Sushi Yana Frankfurt Oder',
                    'address' => "Dresdener Platz 9\n15232 Frankfurt (Oder)-Güldendorf",
                    'email' => 'sushi-yana-ff@outlook.de',
                    'manager' => 'Fadi Khachab',
                    'tax_number' => 'xx/xxx/xxx'
                ]
            ];
            $branch_info = $branches_data[$branch] ?? $branches_data['neukoelln'];
            ob_end_clean();
            echo json_encode(['status' => 'success', 'branch_info' => $branch_info]);
            exit;
        }
        throw new Exception('Invalid GET action');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'add';

        if ($action === 'get_translated_page') {
            $page = $_POST['page'] ?? null;
            $lang = $_POST['lang'] ?? $_SESSION['language'] ?? 'de';
            if ($page === 'datenschutz') {
                $texts_to_translate = [
                    'dtitle' => 'Datenschutzerklärung',
                    'section1' => '1. Datenschutz auf einen Blick',
                    'section1_sub1' => 'Allgemeine Hinweise',
                    'section1_sub1_p1' => 'Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten passiert, wenn Sie diese Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können. Ausführliche Informationen zum Thema Datenschutz entnehmen Sie unserer unter diesem Text aufgeführten Datenschutzerklärung.',
                    'section1_sub2' => 'Datenerfassung auf dieser Website',
                    'section1_sub2_h4_1' => 'Wer ist verantwortlich für die Datenerfassung auf dieser Website?',
                    'section1_sub2_p1' => 'Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Dessen Kontaktdaten können Sie dem Abschnitt „Hinweis zur Verantwortlichen Stelle“ in dieser Datenschutzerklärung entnehmen.',
                    'section1_sub2_h4_2' => 'Wie erfassen wir Ihre Daten?',
                    'section1_sub2_p2' => 'Ihre Daten werden zum einen dadurch erhoben, dass Sie uns diese mitteilen. Hierbei kann es sich z. B. um Daten handeln, die Sie in ein Kontaktformular eingeben.',
                    'section1_sub2_p3' => 'Andere Daten werden automatisch oder nach Ihrer Einwilligung beim Besuch der Website durch unsere IT-Systeme erfasst. Das sind vor allem technische Daten (z. B. Internetbrowser, Betriebssystem oder Uhrzeit des Seitenaufrufs). Die Erfassung dieser Daten erfolgt automatisch, sobald Sie diese Website betreten.',
                    'section1_sub2_h4_3' => 'Wofür nutzen wir Ihre Daten?',
                    'section1_sub2_p4' => 'Ein Teil der Daten wird erhoben, um eine fehlerfreie Bereitstellung der Website zu gewährleisten. Andere Daten können zur Analyse Ihres Nutzerverhaltens verwendet werden.',
                    'section1_sub2_h4_4' => 'Welche Rechte haben Sie bezüglich Ihrer Daten?',
                    'section1_sub2_p5' => 'Sie haben jederzeit das Recht, unentgeltlich Auskunft über Herkunft, Empfänger und Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten. Sie haben außerdem ein Recht, die Berichtigung oder Löschung dieser Daten zu verlangen. Wenn Sie eine Einwilligung zur Datenverarbeitung erteilt haben, können Sie diese Einwilligung jederzeit für die Zukunft widerrufen. Außerdem haben Sie das Recht, unter bestimmten Umständen die Einschränkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen. Des Weiteren steht Ihnen ein Beschwerderecht bei der zuständigen Aufsichtsbehörde zu.',
                    'section1_sub2_p6' => 'Hierzu sowie zu weiteren Fragen zum Thema Datenschutz können Sie sich jederzeit an uns wenden.',
                    'section2' => '2. Hosting',
                    'section2_p1' => 'Wir hosten die Inhalte unserer Website bei folgendem Anbieter:',
                    'section2_sub1' => 'Externes Hosting',
                    'section2_sub1_p1' => 'Diese Website wird extern gehostet. Die personenbezogenen Daten, die auf dieser Website erfasst werden, werden auf den Servern des Hosters / der Hoster gespeichert. Hierbei kann es sich v. a. um IP-Adressen, Kontaktanfragen, Meta- und Kommunikationsdaten, Vertragsdaten, Kontaktdaten, Namen, Websitezugriffe und sonstige Daten, die über eine Website generiert werden, handeln.',
                    'section2_sub1_p2' => 'Das externe Hosting erfolgt zum Zwecke der Vertragserfüllung gegenüber unseren potenziellen und bestehenden Kunden (Art. 6 Abs. 1 lit. b DSGVO) und im Interesse einer sicheren, schnellen und effizienten Bereitstellung unseres Online-Angebots durch einen professionellen Anbieter (Art. 6 Abs. 1 lit. f DSGVO). Sofern eine entsprechende Einwilligung abgefragt wurde, erfolgt die Verarbeitung ausschließlich auf Grundlage von Art. 6 Abs. 1 lit. a DSGVO und § 25 Abs. 1 TDDDG, soweit die Einwilligung die Speicherung von Cookies oder den Zugriff auf Informationen im Endgerät des Nutzers (z. B. Device-Fingerprinting) im Sinne des TDDDG umfasst. Die Einwilligung ist jederzeit widerrufbar.',
                    'section2_sub1_p3' => 'Unser(e) Hoster wird bzw. werden Ihre Daten nur insoweit verarbeiten, wie dies zur Erfüllung seiner Leistungspflichten erforderlich ist und unsere Weisungen in Bezug auf diese Daten befolgen.',
                    'section2_sub1_p4' => 'Wir setzen folgende(n) Hoster ein:',
                    'section2_sub1_p5' => "manitu GmbH\nWelvertstraße 2\n66606 St. Wendel",
                    'section2_sub1_h4' => 'Auftragsverarbeitung',
                    'section2_sub1_p6' => 'Wir haben einen Vertrag über Auftragsverarbeitung (AVV) zur Nutzung des oben genannten Dienstes geschlossen. Hierbei handelt es sich um einen datenschutzrechtlich vorgeschriebenen Vertrag, der gewährleistet, dass dieser die personenbezogenen Daten unserer Websitebesucher nur nach unseren Weisungen und unter Einhaltung der DSGVO verarbeitet.',
                    'section3' => '3. Allgemeine Hinweise und Pflichtinformationen',
                    'section3_sub1' => 'Datenschutz',
                    'section3_sub1_p1' => 'Die Betreiber dieser Seiten nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend den gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerklärung.',
                    'section3_sub1_p2' => 'Wenn Sie diese Website benutzen, werden verschiedene personenbezogenen Daten erhoben. Personenbezogene Daten sind Daten, mit denen Sie persönlich identifiziert werden können. Die vorliegende Datenschutzerklärung erläutert, welche Daten wir erheben und wofür wir sie nutzen. Sie erläutert auch, wie und zu welchem Zweck das geschieht.',
                    'section3_sub1_p3' => 'Wir weisen darauf hin, dass die Datenübertragung im Internet (z. B. bei der Kommunikation per E-Mail) Sicherheitslücken aufweisen kann. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich.',
                    'section3_sub2' => 'Hinweis zur verantwortlichen Stelle',
                    'section3_sub2_p1' => 'Die verantwortliche Stelle für die Datenverarbeitung auf dieser Website ist:',
                    'section3_sub2_p2' => "manitu GmbH\nWelvertstraße 2\n66606 St. Wendel\nDeutschland",
                    'section3_sub2_p3' => "Telefon: +49 6851998080\nE-Mail: datenschutz@manitu.de",
                    'section3_sub2_p4' => 'Verantwortliche Stelle ist die natürliche oder juristische Person, die allein oder gemeinsam mit anderen über die Zwecke und Mittel der Verarbeitung von personenbezogenen Daten (z. B. Namen, E-Mail-Adressen o. Ä.) entscheidet.',
                    'section3_sub3' => 'Speicherdauer',
                    'section3_sub3_p1' => 'Soweit innerhalb dieser Datenschutzerklärung keine speziellere Speicherdauer genannt wurde, verbleiben Ihre personenbezogenen Daten bei uns, bis der Zweck für die Datenverarbeitung entfällt. Wenn Sie ein berechtigtes Löschersuchen geltend machen oder eine Einwilligung zur Datenverarbeitung widerrufen, werden Ihre Daten gelöscht, sofern wir keine anderen rechtlich zulässigen Gründe für die Speicherung Ihrer personenbezogenen Daten haben (z. B. steuer- oder handelsrechtliche Aufbewahrungsfristen); im letztgenannten Fall erfolgt die Löschung nach Fortfall dieser Gründe.',
                    'section3_sub4' => 'Allgemeine Hinweise zu den Rechtsgrundlagen der Datenverarbeitung auf dieser Website',
                    'section3_sub4_p1' => 'Sofern Sie in die Datenverarbeitung eingewilligt haben, verarbeiten wir Ihre personenbezogenen Daten auf Grundlage von Art. 6 Abs. 1 lit. a DSGVO bzw. Art. 9 Abs. 2 lit. a DSGVO, sofern besondere Datenkategorien nach Art. 9 Abs. 1 DSGVO verarbeitet werden. Im Falle einer ausdrücklichen Einwilligung in die Übertragung personenbezogener Daten in Drittstaaten erfolgt die Datenverarbeitung außerdem auf Grundlage von Art. 49 Abs. 1 lit. a DSGVO. Sofern Sie in die Speicherung von Cookies oder in den Zugriff auf Informationen in Ihr Endgerät (z. B. via Device-Fingerprinting) eingewilligt haben, erfolgt die Datenverarbeitung zusätzlich auf Grundlage von § 25 Abs. 1 TDDDG. Die Einwilligung ist jederzeit widerrufbar. Sind Ihre Daten zur Vertragserfüllung oder zur Durchführung vorvertraglicher Maßnahmen erforderlich, verarbeiten wir Ihre Daten auf Grundlage des Art. 6 Abs. 1 lit. b DSGVO. Des Weiteren verarbeiten wir Ihre Daten, sofern diese zur Erfüllung einer rechtlichen Verpflichtung erforderlich sind auf Grundlage von Art. 6 Abs. 1 lit. c DSGVO. Die Datenverarbeitung kann ferner auf Grundlage unseres berechtigten Interesses nach Art. 6 Abs. 1 lit. f DSGVO erfolgen. Über die jeweils im Einzelfall einschlägigen Rechtsgrundlagen wird in den folgenden Absätzen dieser Datenschutzerklärung informiert.',
                    'section3_sub5' => 'Empfänger von personenbezogenen Daten',
                    'section3_sub5_p1' => 'Im Rahmen unserer Geschäftstätigkeit arbeiten wir mit verschiedenen externen Stellen zusammen. Dabei ist teilweise auch eine Übermittlung von personenbezogenen Daten an diese externen Stellen erforderlich. Wir geben personenbezogene Daten nur dann an externe Stellen weiter, wenn dies im Rahmen einer Vertragserfüllung erforderlich ist, wenn wir gesetzlich hierzu verpflichtet sind (z. B. Weitergabe von Daten an Steuerbehörden), wenn wir ein berechtigtes Interesse nach Art. 6 Abs. 1 lit. f DSGVO an der Weitergabe haben oder wenn eine sonstige Rechtsgrundlage die Datenweitergabe erlaubt. Beim Einsatz von Auftragsverarbeitern geben wir personenbezogene Daten unserer Kunden nur auf Grundlage eines gültigen Vertrags über Auftragsverarbeitung weiter. Im Falle einer gemeinsamen Verarbeitung wird ein Vertrag über gemeinsame Verarbeitung geschlossen.',
                    'section3_sub6' => 'Widerruf Ihrer Einwilligung zur Datenverarbeitung',
                    'section3_sub6_p1' => 'Viele Datenverarbeitungsvorgänge sind nur mit Ihrer ausdrücklichen Einwilligung möglich. Sie können eine bereits erteilte Einwilligung jederzeit widerrufen. Die Rechtmäßigkeit der bis zum Widerruf erfolgten Datenverarbeitung bleibt vom Widerruf unberührt.',
                    'section3_sub7' => 'Widerspruchsrecht gegen die Datenerhebung in besonderen Fällen sowie gegen Direktwerbung (Art. 21 DSGVO)',
                    'section3_sub7_p1' => 'WENN DIE DATENVERARBEITUNG AUF GRUNDLAGE VON ART. 6 ABS. 1 LIT. E ODER F DSGVO ERFOLGT, HABEN SIE JEDERZEIT DAS RECHT, AUS GRÜNDEN, DIE SICH AUS IHRER BESONDEREN SITUATION ERGEBEN, GEGEN DIE VERARBEITUNG IHRER PERSONENBEZOGENEN DATEN WIDERSPRUCH EINZULEGEN; DIES GILT AUCH FÜR EIN AUF DIESE BESTIMMUNGEN GESTÜTZTES PROFILING. DIE JEWEILIGE RECHTSGRUNDLAGE, AUF DENEN EINE VERARBEITUNG BERUHT, ENTNEHMEN SIE DIESER DATENSCHUTZERKLÄRUNG. WENN SIE WIDERSPRUCH EINLEGEN, WERDEN WIR IHRE BETROFFENEN PERSONENBEZOGENEN DATEN NICHT MEHR VERARBEITEN, ES SEI DENN, WIR KÖNNEN ZWINGENDE SCHUTZWÜRDIGE GRÜNDE FÜR DIE VERARBEITUNG NACHWEISEN, DIE IHRE INTERESSEN, RECHTE UND FREIHEITEN ÜBERWIEGEN ODER DIE VERARBEITUNG DIENT DER GELTENDMACHUNG, AUSÜBUNG ODER VERTEIDIGUNG VON RECHTSANSPRÜCHEN (WIDERSPRUCH NACH ART. 21 ABS. 1 DSGVO).',
                    'section3_sub7_p2' => 'WERDEN IHRE PERSONENBEZOGENEN DATEN VERARBEITED, UM DIREKTWERBUNG ZU BETREIBEN, SO HABEN SIE DAS RECHT, JEDERZEIT WIDERSPRUCH GEGEN DIE VERARBEITUNG SIE BETREFFENDER PERSONENBEZOGENER DATEN ZUM ZWECKE DERARTIGER WERBUNG EINZULEGEN; DIES GILT AUCH FÜR DAS PROFILING, SOWEIT ES MIT SOLCHER DIREKTWERBUNG IN VERBINDUNG STEHT. WENN SIE WIDERSPRECHEN, WERDEN IHRE PERSONENBEZOGENEN DATEN ANSCHLIESSEND NICHT MEHR ZUM ZWECKE DER DIREKTWERBUNG VERWENDET (WIDERSPRUCH NACH ART. 21 ABS. 2 DSGVO).',
                    'section3_sub8' => 'Beschwerderecht bei der zuständigen Aufsichtsbehörde',
                    'section3_sub8_p1' => 'Im Falle von Verstößen gegen die DSGVO steht den Betroffenen ein Beschwerderecht bei einer Aufsichtsbehörde, insbesondere in dem Mitgliedstaat ihres gewöhnlichen Aufenthalts, ihres Arbeitsplatzes oder des Orts des mutmaßlichen Verstoßes zu. Das Beschwerderecht besteht unbeschadet anderweitiger verwaltungsrechtlicher oder gerichtlicher Rechtsbehelfe.',
                    'section3_sub9' => 'Recht auf Datenübertragbarkeit',
                    'section3_sub9_p1' => 'Sie haben das Recht, Daten, die wir auf Grundlage Ihrer Einwilligung oder in Erfüllung eines Vertrags automatisiert verarbeiten, an sich oder an einen Dritten in einem gängigen, maschinenlesbaren Format aushändigen zu lassen. Sofern Sie die direkte Übertragung der Daten an einen anderen Verantwortlichen verlangen, erfolgt dies nur, soweit es technisch machbar ist.',
                    'section3_sub10' => 'Auskunft, Berichtigung und Löschung',
                    'section3_sub10_p1' => 'Sie haben im Rahmen der geltenden gesetzlichen Bestimmungen jederzeit das Recht auf unentgeltliche Auskunft über Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empfänger und den Zweck der Datenverarbeitung und ggf. ein Recht auf Berichtigung oder Löschung dieser Daten. Hierzu sowie zu weiteren Fragen zum Thema personenbezogene Daten können Sie sich jederzeit an uns wenden.',
                    'section3_sub11' => 'Recht auf Einschränkung der Verarbeitung',
                    'section3_sub11_p1' => 'Sie haben das Recht, die Einschränkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen. Hierzu können Sie sich jederzeit an uns wenden. Das Recht auf Einschränkung der Verarbeitung besteht in folgenden Fällen:',
                    'section3_sub11_li1' => 'Wenn Sie die Richtigkeit Ihrer bei uns gespeicherten personenbezogenen Daten bestreiten, benötigen wir in der Regel Zeit, um dies zu überprüfen. Für die Dauer der Prüfung haben Sie das Recht, die Einschränkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen.',
                    'section3_sub11_li2' => 'Wenn die Verarbeitung Ihrer personenbezogenen Daten unrechtmäßig geschah/geschieht, können Sie statt der Löschung die Einschränkung der Datenverarbeitung verlangen.',
                    'section3_sub11_li3' => 'Wenn wir Ihre personenbezogenen Daten nicht mehr benötigen, Sie sie jedoch zur Ausübung, Verteidigung oder Geltendmachung von Rechtsansprüchen benötigen, haben Sie das Recht, statt der Löschung die Einschränkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen.',
                    'section3_sub11_li4' => 'Wenn Sie einen Widerspruch nach Art. 21 Abs. 1 DSGVO eingelegt haben, muss eine Abwägung zwischen Ihren und unseren Interessen vorgenommen werden. Solange noch nicht feststeht, wessen Interessen überwiegen, haben Sie das Recht, die Einschränkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen.',
                    'section3_sub11_p2' => 'Wenn Sie die Verarbeitung Ihrer personenbezogenen Daten eingeschränkt haben, dürfen diese Daten – von ihrer Speicherung abgesehen – nur mit Ihrer Einwilligung oder zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen oder zum Schutz der Rechte einer anderen natürlichen oder juristischen Person oder aus Gründen eines wichtigen öffentlichen Interesses der Europäischen Union oder eines Mitgliedstaats verarbeitet werden.',
                ];

                $translated_texts = [];
                foreach ($texts_to_translate as $key => $text) {
                    $translated_texts[$key] = translateText($text, 'de', $lang, $conn)[0];
                }

                header('Content-Type: text/html');
                ob_start();
                ?>
                <h1 data-translate="dtitle"><?php echo htmlspecialchars($translated_texts['dtitle']); ?></h1>
                <h2 data-translate="section1"><?php echo htmlspecialchars($translated_texts['section1']); ?></h2>
                <h3 data-translate="section1_sub1"><?php echo htmlspecialchars($translated_texts['section1_sub1']); ?></h3>
                <p data-translate="section1_sub1_p1"><?php echo htmlspecialchars($translated_texts['section1_sub1_p1']); ?></p>
                <h3 data-translate="section1_sub2"><?php echo htmlspecialchars($translated_texts['section1_sub2']); ?></h3>
                <h4 data-translate="section1_sub2_h4_1"><?php echo htmlspecialchars($translated_texts['section1_sub2_h4_1']); ?></h4>
                <p data-translate="section1_sub2_p1"><?php echo htmlspecialchars($translated_texts['section1_sub2_p1']); ?></p>
                <h4 data-translate="section1_sub2_h4_2"><?php echo htmlspecialchars($translated_texts['section1_sub2_h4_2']); ?></h4>
                <p data-translate="section1_sub2_p2"><?php echo htmlspecialchars($translated_texts['section1_sub2_p2']); ?></p>
                <p data-translate="section1_sub2_p3"><?php echo htmlspecialchars($translated_texts['section1_sub2_p3']); ?></p>
                <h4 data-translate="section1_sub2_h4_3"><?php echo htmlspecialchars($translated_texts['section1_sub2_h4_3']); ?></h4>
                <p data-translate="section1_sub2_p4"><?php echo htmlspecialchars($translated_texts['section1_sub2_p4']); ?></p>
                <h4 data-translate="section1_sub2_h4_4"><?php echo htmlspecialchars($translated_texts['section1_sub2_h4_4']); ?></h4>
                <p data-translate="section1_sub2_p5"><?php echo htmlspecialchars($translated_texts['section1_sub2_p5']); ?></p>
                <p data-translate="section1_sub2_p6"><?php echo htmlspecialchars($translated_texts['section1_sub2_p6']); ?></p>
                <h2 data-translate="section2"><?php echo htmlspecialchars($translated_texts['section2']); ?></h2>
                <p data-translate="section2_p1"><?php echo htmlspecialchars($translated_texts['section2_p1']); ?></p>
                <h3 data-translate="section2_sub1"><?php echo htmlspecialchars($translated_texts['section2_sub1']); ?></h3>
                <p data-translate="section2_sub1_p1"><?php echo htmlspecialchars($translated_texts['section2_sub1_p1']); ?></p>
                <p data-translate="section2_sub1_p2"><?php echo htmlspecialchars($translated_texts['section2_sub1_p2']); ?></p>
                <p data-translate="section2_sub1_p3"><?php echo htmlspecialchars($translated_texts['section2_sub1_p3']); ?></p>
                <p data-translate="section2_sub1_p4"><?php echo htmlspecialchars($translated_texts['section2_sub1_p4']); ?></p>
                <p class="address-block" data-translate="section2_sub1_p5"><?php echo htmlspecialchars($translated_texts['section2_sub1_p5']); ?></p>
                <h4 data-translate="section2_sub1_h4"><?php echo htmlspecialchars($translated_texts['section2_sub1_h4']); ?></h4>
                <p data-translate="section2_sub1_p6"><?php echo htmlspecialchars($translated_texts['section2_sub1_p6']); ?></p>
                <h2 data-translate="section3"><?php echo htmlspecialchars($translated_texts['section3']); ?></h2>
                <h3 data-translate="section3_sub1"><?php echo htmlspecialchars($translated_texts['section3_sub1']); ?></h3>
                <p data-translate="section3_sub1_p1"><?php echo htmlspecialchars($translated_texts['section3_sub1_p1']); ?></p>
                <p data-translate="section3_sub1_p2"><?php echo htmlspecialchars($translated_texts['section3_sub1_p2']); ?></p>
                <p data-translate="section3_sub1_p3"><?php echo htmlspecialchars($translated_texts['section3_sub1_p3']); ?></p>
                <h3 data-translate="section3_sub2"><?php echo htmlspecialchars($translated_texts['section3_sub2']); ?></h3>
                <p data-translate="section3_sub2_p1"><?php echo htmlspecialchars($translated_texts['section3_sub2_p1']); ?></p>
                <p class="address-block" data-translate="section3_sub2_p2"><?php echo htmlspecialchars($translated_texts['section3_sub2_p2']); ?></p>
                <p class="address-block" data-translate="section3_sub2_p3"><?php echo htmlspecialchars($translated_texts['section3_sub2_p3']); ?></p>
                <p data-translate="section3_sub2_p4"><?php echo htmlspecialchars($translated_texts['section3_sub2_p4']); ?></p>
                <h3 data-translate="section3_sub3"><?php echo htmlspecialchars($translated_texts['section3_sub3']); ?></h3>
                <p data-translate="section3_sub3_p1"><?php echo htmlspecialchars($translated_texts['section3_sub3_p1']); ?></p>
                <h3 data-translate="section3_sub4"><?php echo htmlspecialchars($translated_texts['section3_sub4']); ?></h3>
                <p data-translate="section3_sub4_p1"><?php echo htmlspecialchars($translated_texts['section3_sub4_p1']); ?></p>
                <h3 data-translate="section3_sub5"><?php echo htmlspecialchars($translated_texts['section3_sub5']); ?></h3>
                <p data-translate="section3_sub5_p1"><?php echo htmlspecialchars($translated_texts['section3_sub5_p1']); ?></p>
                <h3 data-translate="section3_sub6"><?php echo htmlspecialchars($translated_texts['section3_sub6']); ?></h3>
                <p data-translate="section3_sub6_p1"><?php echo htmlspecialchars($translated_texts['section3_sub6_p1']); ?></p>
                <h3 data-translate="section3_sub7"><?php echo htmlspecialchars($translated_texts['section3_sub7']); ?></h3>
                <p data-translate="section3_sub7_p1"><?php echo htmlspecialchars($translated_texts['section3_sub7_p1']); ?></p>
                <p data-translate="section3_sub7_p2"><?php echo htmlspecialchars($translated_texts['section3_sub7_p2']); ?></p>
                <h3 data-translate="section3_sub8"><?php echo htmlspecialchars($translated_texts['section3_sub8']); ?></h3>
                <p data-translate="section3_sub8_p1"><?php echo htmlspecialchars($translated_texts['section3_sub8_p1']); ?></p>
                <h3 data-translate="section3_sub9"><?php echo htmlspecialchars($translated_texts['section3_sub9']); ?></h3>
                <p data-translate="section3_sub9_p1"><?php echo htmlspecialchars($translated_texts['section3_sub9_p1']); ?></p>
                <h3 data-translate="section3_sub10"><?php echo htmlspecialchars($translated_texts['section3_sub10']); ?></h3>
                <p data-translate="section3_sub10_p1"><?php echo htmlspecialchars($translated_texts['section3_sub10_p1']); ?></p>
                <h3 data-translate="section3_sub11"><?php echo htmlspecialchars($translated_texts['section3_sub11']); ?></h3>
                <p data-translate="section3_sub11_p1"><?php echo htmlspecialchars($translated_texts['section3_sub11_p1']); ?></p>
                <ul>
                    <li data-translate="section3_sub11_li1"><?php echo htmlspecialchars($translated_texts['section3_sub11_li1']); ?></li>
                    <li data-translate="section3_sub11_li2"><?php echo htmlspecialchars($translated_texts['section3_sub11_li2']); ?></li>
                    <li data-translate="section3_sub11_li3"><?php echo htmlspecialchars($translated_texts['section3_sub11_li3']); ?></li>
                    <li data-translate="section3_sub11_li4"><?php echo htmlspecialchars($translated_texts['section3_sub11_li4']); ?></li>
                </ul>
                <p data-translate="section3_sub11_p2"><?php echo htmlspecialchars($translated_texts['section3_sub11_p2']); ?></p>
                <?php
                $html = ob_get_clean();
                ob_end_clean();
                echo $html;
                exit;
            } elseif ($page === 'impressum') {
                $branch = $_POST['branch'] ?? $_SESSION['branch'] ?? 'neukoelln';
                $branches_data = [
                    'neukoelln' => [
                        'name' => 'Sushi Yana Neukölln',
                        'address' => "Flughafenstraße 76\n12049 Berlin",
                        'email' => 'neukoelln@sushi-yana.de',
                        'manager' => 'Hussein Hamid',
                        'tax_number' => '16/329/04249'
                    ],
                    'charlottenburg' => [
                        'name' => 'Sushi Yana Charlottenburg',
                        'address' => "Lietzenburger Straße 29\n10789 Berlin",
                        'email' => 'Charlottenburg@sushi-yana.de',
                        'manager' => 'Giorgos Mavridis',
                        'tax_number' => '24/437/02213'
                    ],
                    'friedrichshain' => [
                        'name' => 'Sushi Yana Friedrichshain',
                        'address' => "Karl-Marx-Allee 140\n10243 Berlin",
                        'email' => 'sushiyana.friedrichshain@web.de',
                        'manager' => 'Aydin Irendeli',
                        'tax_number' => '14/359/02065'
                    ],
                    'lichtenrade' => [
                        'name' => 'Sushi Yana Lichtenrade',
                        'address' => "Bahnhoffstraße 29\n12305 Berlin",
                        'email' => 'lichtenrade@sushi-yana.de',
                        'manager' => 'Pablo Gonzales',
                        'tax_number' => '2'
                    ],
                    'mitte' => [
                        'name' => 'Sushi Yana Mall of Berlin GmbH',
                        'address' => "Leipziger Platz 12\n10117 Berlin",
                        'email' => 'buero@sushi-yana.de',
                        'manager' => 'Hussein Hamid',
                        'court' => 'Amtsgericht Charlottenburg',
                        'register' => 'HRB 235940 B',
                        'tax_number' => '1130/553/51695'
                    ],
                    'moabit' => [
                        'name' => 'Sushi Yana Moabit',
                        'address' => "Gotzkowskystraße 26\n10555 Berlin",
                        'email' => 'moabit@sushi-yana.de',
                        'manager' => 'Hani El-Jamal',
                        'tax_number' => '034/275/02211'
                    ],
                    'potsdam' => [
                        'name' => 'Sushi Yana Potsdam',
                        'address' => "Kastanienallee 17\n14471 Potsdam",
                        'email' => 'potsdam@sushi-yana.de',
                        'manager' => 'Despoina Pappa',
                        'tax_number' => '046/255/11807'
                    ],
                    'rudow' => [
                        'name' => 'Sushi Yana GmbH',
                        'address' => "Flughafenstraße 76\n12049 Berlin",
                        'email' => 'buero@sushi-yana.de',
                        'manager' => 'Wesam El-Saadi',
                        'court' => 'Amtsgericht Charlottenburg',
                        'register' => 'HRB 233774 B',
                        'tax_number' => '29/553/32890',
                        'vat' => 'DE347204498'
                    ],
                    'spandau' => [
                        'name' => 'Sushi Yana Spandau',
                        'address' => "Pichelsdorferstraße 120\n13595 Berlin",
                        'email' => 'spandau@sushi-yana.de',
                        'manager' => 'Ibrahim Hamade',
                        'tax_number' => '19/929/00826'
                    ],
                    'tegel' => [
                        'name' => 'Sushi Yana Tegel',
                        'address' => "Medebacher Weg 12\n13507 Berlin",
                        'email' => 'tegel@sushi-yana.de',
                        'manager' => 'Nagy Varga',
                        'tax_number' => 'xx/xxx/xxxxx'
                    ],
                    'weissensee' => [
                        'name' => 'Sushi Yana Weißensee',
                        'address' => "Liebermannstraße 95\n13088 Berlin",
                        'email' => 'buero@sushi-yana.de',
                        'manager' => 'Tolga Cildasi',
                        'tax_number' => 'xx/xxx/xxxxx'
                    ],
                    'zehlendorf' => [
                        'name' => 'Sushi Yana Zehlendorf',
                        'address' => "Berlinerstraße 67\n14169 Berlin",
                        'email' => 'steglitz@sushi-yana.de',
                        'manager' => 'Mohamed Berjaoui',
                        'tax_number' => 'xx/xxx/xxx'
                    ],
                    'FFO' => [
                        'name' => 'Sushi Yana Frankfurt Oder',
                        'address' => "Dresdener Platz 9\n15232 Frankfurt (Oder)-Güldendorf",
                        'email' => 'sushi-yana-ff@outlook.de',
                        'manager' => 'Fadi Khachab',
                        'tax_number' => 'xx/xxx/xxx'
                    ]
                ];
                $branch_info = $branches_data[$branch] ?? $branches_data['neukoelln'];

                $texts_to_translate = [
                    'title' => 'Impressum',
                    'responsible_text' => 'Verantwortlich für die Startseite und die weiteren Informationsseiten (ohne Onlineshops) dieser Webseite ist:',
                    'name_label' => $branch_info['name'],
                    'address_label' => $branch_info['address'],
                    'email_label' => $branch_info['email'],
                    'manager_label' => 'Geschäftsführer: ' . $branch_info['manager'],
                    'tax_label' => 'Steuernummer: ' . $branch_info['tax_number'],
                    'court_label' => isset($branch_info['court']) ? 'Zuständiges Gericht: ' . $branch_info['court'] : '',
                    'register_label' => isset($branch_info['register']) ? 'Handelsregister: ' . $branch_info['register'] : '',
                    'vat_label' => isset($branch_info['vat']) ? 'Ust.: ' . $branch_info['vat'] : '',
                    'franchise_note' => 'Jede Sushi Yana Filiale wird von einem selbstständig tätigen Gewerbetreibenden als Franchisenehmer bewirtschaftet. Dieser organisiert Produktion und Auslieferung seiner Produkte für seinen Betrieb in eigener Verantwortung. Wenn du Fragen oder Anliegen zu deiner Lieferung hast, wende dich bitte an den Verantwortlichen des jeweiligen Betriebes, den du in vorstehender Liste finden kannst.',
                    'contact_note' => 'Unsere Franchisezentrale erreichen Sie über die Mailadresse',
                    'contact_email' => 'buero@sushi-yana.de',
                    'contact_instruction' => 'Bitte nutzen Sie diese ausschließlich für allgemeine Anfragen.'
                ];

                $translated_texts = [];
                foreach ($texts_to_translate as $key => $text) {
                    if ($key === 'address_label') {
                        $lines = explode("\n", $text);
                        $translated_lines = translateText($lines, 'de', $lang, $conn);
                        $translated_texts[$key] = implode("\n", $translated_lines);
                    } else {
                        $translated_texts[$key] = translateText($text, 'de', $lang, $conn)[0];
                    }
                }

                header('Content-Type: text/html');
                ob_start();
                ?>
                <h1 data-translate="title"><?php echo htmlspecialchars($translated_texts['title']); ?></h1>
                <p data-translate="responsible_text"><strong><?php echo htmlspecialchars($translated_texts['responsible_text']); ?></strong></p>
                <span class="address-block" data-translate="name_label"><?php echo htmlspecialchars($translated_texts['name_label']); ?></span><br>
                <span class="address-block" data-translate="address_label"><?php echo htmlspecialchars($translated_texts['address_label']); ?></span><br>
                <span><a href="mailto:<?php echo htmlspecialchars($branch_info['email']); ?>" class="email-link" data-translate="email_label"><?php echo htmlspecialchars($translated_texts['email_label']); ?></a></span><br>
                <span data-translate="manager_label"><?php echo htmlspecialchars($translated_texts['manager_label']); ?></span><br>
                <span data-translate="tax_label"><?php echo htmlspecialchars($translated_texts['tax_label']); ?></span><br>
                <?php if (!empty($translated_texts['court_label'])): ?>
                    <span data-translate="court_label"><?php echo htmlspecialchars($translated_texts['court_label']); ?></span><br>
                <?php endif; ?>
                <?php if (!empty($translated_texts['register_label'])): ?>
                    <span data-translate="register_label"><?php echo htmlspecialchars($translated_texts['register_label']); ?></span><br>
                <?php endif; ?>
                <?php if (!empty($translated_texts['vat_Label'])): ?>
                    <span data-translate="vat_label"><?php echo htmlspecialchars($translated_texts['vat_label']); ?></span><br>
                <?php endif; ?>
                <p data-translate="franchise_note"><?php echo htmlspecialchars($translated_texts['franchise_note']); ?></p>
                <p data-translate="contact_note"><?php echo htmlspecialchars($translated_texts['contact_note']); ?> <a href="mailto:<?php echo htmlspecialchars($translated_texts['contact_email']); ?>" class="email-link" data-translate="contact_email"><?php echo htmlspecialchars($translated_texts['contact_email']); ?></a>. <?php echo htmlspecialchars($translated_texts['contact_instruction']); ?></p>
                <?php
                $html = ob_get_clean();
                ob_end_clean();
                echo $html;
                exit;
            }
            throw new Exception('Invalid page');
        } elseif ($action === 'submit_order') {
            $order_details = json_encode($_SESSION['cart']);
            $branch = $_SESSION['branch'] ?? null;
            $table_number = isset($_POST['table_number']) ? (int)$_POST['table_number'] : null;
            $stmt = $conn->prepare("INSERT INTO orders (order_details, status, branch, table_number, created_at) VALUES (?, 'active', ?, ?, NOW())");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            $stmt->bind_param("ssi", $order_details, $branch, $table_number);
            if ($stmt->execute()) {
                $_SESSION['cart'] = [];
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Order submitted']);
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            exit;
        } elseif ($action === 'complete_order' || $action === 'delete_order') {
            $order_id = (int)($_POST['order_id'] ?? 0);
            if ($order_id <= 0) throw new Exception('Invalid or missing order_id');

            if ($action === 'complete_order') {
                $stmt = $conn->prepare("UPDATE orders SET status = 'completed', updated_at = NOW() WHERE id = ? AND status = 'active'");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $order_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows === 0) throw new Exception('Order not found or already completed');
                    ob_end_clean();
                    echo json_encode(['status' => 'success', 'message' => 'Order completed']);
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
            } else {
                $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $order_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows === 0) throw new Exception('Order not found');
                    ob_end_clean();
                    echo json_encode(['status' => 'success', 'message' => 'Order deleted']);
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
            }
            $stmt->close();
            exit;
        } elseif ($action === 'set_branch') {
            $branch = $_POST['branch'] ?? null;
            $valid_branches = ['charlottenburg', 'friedrichshain', 'lichtenrade', 'mitte', 'moabit',
                               'neukoelln', 'potsdam', 'rudow', 'spandau', 'tegel', 'weissensee',
                               'zehlendorf', 'FFO'];
            if ($branch && in_array($branch, $valid_branches)) {
                $_SESSION['branch'] = $branch;
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Branch updated to ' . $branch]);
            } else {
                throw new Exception('Invalid branch');
            }
            exit;
        } elseif ($action === 'set_language') {
            $language = $_POST['language'] ?? null;
            $valid_languages = ['de', 'en', 'fr', 'pl', 'it', 'ru', 'tr', 'es', 'ar'];
            if ($language && in_array($language, $valid_languages)) {
                $_SESSION['language'] = $language;
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Language set to ' . $language]);
            } else {
                throw new Exception('Invalid language');
            }
            exit;
        }

        $item_id = $_POST['item_id'] ?? null;
        $table = $_POST['table'] ?? null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
        $item_key = ($item_id && $table) ? "$table:$item_id" : ($_POST['item_key'] ?? null);

        switch ($action) {
            case 'add':
                if (!$item_id || !$table) throw new Exception('Missing item_id or table');
                $_SESSION['cart'][$item_key] = ($_SESSION['cart'][$item_key] ?? 0) + 1;
                $message = 'Item added to cart';
                break;

            case 'update':
                if (!$item_key || $quantity === null) throw new Exception('Missing item_key or quantity');
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$item_key]);
                    $message = 'Item removed from cart';
                } else {
                    $_SESSION['cart'][$item_key] = $quantity;
                    $message = 'Cart updated';
                }
                break;

            case 'remove':
                if (!$item_key) throw new Exception('Missing item_key');
                unset($_SESSION['cart'][$item_key]);
                $message = 'Item removed from cart';
                break;

            default:
                throw new Exception('Invalid action');
        }

        session_write_close();
        ob_end_clean();
        echo json_encode(['status' => 'success', 'message' => $message, 'cart' => $_SESSION['cart']]);
        exit;
    }

    throw new Exception('Invalid request method');
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

exit;
?>