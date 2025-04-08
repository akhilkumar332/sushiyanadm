<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Ensure cart is initialized and synchronized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get current language from session or query parameter, default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');
$_SESSION['language'] = $current_lang;

// Function to translate text with database caching
function getTranslatedText($text, $sourceLang, $targetLang, $conn) {
    $translations = translateText($text, $sourceLang, $targetLang, $conn);
    return is_array($translations) ? $translations[0] : $translations;
}

// Define texts to translate
$texts_to_translate = [
    'title' => 'Datenschutzerklärung',
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

// Translate all texts
$translated_texts = [];
foreach ($texts_to_translate as $key => $text) {
    $translated_texts[$key] = getTranslatedText($text, 'de', $current_lang, $conn);
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>" dir="<?php echo $current_lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title><?php echo htmlspecialchars($translated_texts['title']); ?></title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
    <style>
        .legal-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            text-align: <?php echo $current_lang === 'ar' ? 'right' : 'left'; ?>;
        }
        .legal-content h1 { font-size: 2rem; margin-bottom: 1.5rem; }
        .legal-content h2 { font-size: 1.5rem; margin-top: 2rem; margin-bottom: 1rem; }
        .legal-content h3 { font-size: 1.25rem; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .legal-content h4 { font-size: 1.1rem; margin-top: 1.25rem; margin-bottom: 0.5rem; }
        .legal-content p { margin-bottom: 1rem; line-height: 1.6; }
        .legal-content ul { margin: 1rem 0 1rem <?php echo $current_lang === 'ar' ? '0' : '2rem'; ?>; padding: 0; list-style: disc; }
        .legal-content li { margin-bottom: 0.75rem; line-height: 1.6; }
        .address-block { white-space: pre-line; }
    </style>
</head>
<body class="navigation" data-page="datenschutz" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="content legal-content">
            <h1 data-translate="title"><?php echo htmlspecialchars($translated_texts['title']); ?></h1>
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
        </div>
    </main>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner"></div>
</body>
</html>