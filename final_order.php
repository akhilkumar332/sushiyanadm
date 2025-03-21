<?php
session_start();
include 'config/config.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$tables = [
    'bowls', 'chopsuey', 'desserts', 'erdnussgericht', 'extras', 'extrasWarm', 'fingerfood',
    'gemuese', 'getraenke', 'gyoza', 'insideoutrolls', 'makis', 'mangochutney', 'menues',
    'miniyanarolls', 'nigiris', 'nudeln', 'redcurry', 'reis', 'salate', 'sashimi',
    'sommerrollen', 'specialrolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
    'yanarolls', 'yellowcurry'
];

// Get current timestamp in German format
date_default_timezone_set('Europe/Berlin'); // Adjust timezone as needed
$timestamp = date('d.m.Y H:i:s'); // e.g., "20.03.2025 14:30:45"
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Rechnung</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <style>
        .bill-wrapper { max-width: 800px; margin: 20px auto; padding: 0 15px; font-family: 'Arial', sans-serif; }
        .bill-header { text-align: center; padding: 20px; color: #fff !important; background: #6A2477; border-radius: 10px 10px 0 0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .bill-header h1 { margin: 0 0 10px 0; font-size: 28px; font-weight: 600; letter-spacing: 1px; color: #fff !important; }
        .bill-header p { margin: 0; font-size: 16px; color: #fff !important; font-weight: 300; }
        .bill-details { background-color: #fff; padding: 20px; border-radius: 0 0 10px 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .bill-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .bill-table th, .bill-table td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .bill-table th { background-color: #f9f9f9; font-weight: 600; color: #333; }
        .bill-table td { color: #666; }
        .bill-table .quantity { text-align: center; }
        .bill-table .price, .bill-table .subtotal { text-align: right; }
        .bill-total { display: flex; justify-content: space-between; padding: 15px; background-color: #f9f9f9; font-size: 18px; font-weight: 600; color: #333; border-top: 1px solid #eee; }
        .timer { text-align: center; margin-top: 20px; font-size: 16px; color: #6A2477; display: none; background: rgba(106, 36, 119, 0.1); padding: 10px; border-radius: 5px; transition: opacity 0.3s ease; }
        .timer.visible { opacity: 1; }
        #timer-countdown {
            display: inline-block;
            min-width: 100px; /* Fixed width to prevent shaking */
            text-align: center;
            font-family: monospace; /* Monospace font for consistent digit width */
        }
        .timer.warning {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        .bill-buttons { padding: 20px 0; text-align: center; }
        .bill-buttons .btn { display: inline-block; padding: 12px 25px; margin: 10px; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: 500; background-color: #6A2477; color: #fff !important; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); cursor: pointer; border: none; }
        .bill-buttons .btn:hover { background-color: #4A1A55; transform: translateY(-2px); }
        @media (max-width: 768px) {
            .bill-wrapper { margin: 10px; padding: 0; width: auto; max-width: 100%; }
            .bill-header { padding: 15px; border-radius: 10px 10px 0 0; }
            .bill-header h1 { font-size: 24px; }
            .bill-header p { font-size: 14px; }
            .bill-details { padding: 15px; }
            .bill-table th, .bill-table td { font-size: 14px; padding: 8px; }
            .bill-total { font-size: 16px; padding: 10px; }
            .timer { font-size: 14px; }
            #timer-countdown { min-width: 80px; }
            .bill-buttons { padding: 15px 0; }
            .bill-buttons .btn { display: block; width: 90%; max-width: 400px; margin: 10px auto; padding: 12px 20px; }
        }
    </style>
</head>
<body class="navigation">
    <header>
        <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="bill-wrapper">
            <div class="bill-header">
                <h1>Ihre Rechnung - Bestellbestätigung</h1>
                <p><?php echo $timestamp; ?></p>
            </div>
            <div class="bill-details">
                <table class="bill-table">
                    <thead>
                        <tr>
                            <th>Nummer</th>
                            <th>Artikel</th>
                            <th class="quantity">Menge</th>
                            <th class="price">Einzelpreis</th>
                            <th class="subtotal">Gesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item_key => $quantity) {
                            if (strpos($item_key, ':') === false) {
                                error_log("Invalid cart item key: $item_key");
                                continue;
                            }
                            list($table, $item_id) = explode(':', $item_key);
                            if (!in_array($table, $tables)) continue;
                            $stmt = $conn->prepare("SELECT artikelnummer, artikelname, preis FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                            $stmt->bind_param("i", $item_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $item = $result->fetch_assoc();
                            $price = floatval($item['preis']);
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['artikelnummer']); ?></td>
                                <td><?php echo htmlspecialchars($item['artikelname']); ?></td>
                                <td class="quantity"><?php echo $quantity; ?></td>
                                <td class="price"><?php echo number_format($price, 2); ?> €</td>
                                <td class="subtotal"><?php echo number_format($subtotal, 2); ?> €</td>
                            </tr>
                        <?php $stmt->close(); } ?>
                    </tbody>
                </table>
                <div class="bill-total">
                    <span>Gesamtbetrag</span>
                    <span><?php echo number_format($total, 2); ?> €</span>
                </div>
                <div class="timer" id="inactivity-timer" aria-live="polite">
                    Inaktivitätstimer: <span id="timer-countdown"></span>
                </div>
            </div>
            <div class="bill-buttons">
                <button class="btn" id="back-to-home">Löschen & Zurückgeben</button>
            </div>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            const inactivityTimeout = 2 * 60 * 1000; // 2 minutes (for testing; adjust to 5 minutes if needed)
            const timerThreshold = 1 * 60 * 1000; // 1 minute (for testing; adjust to 4 minutes if needed)
            const countdownDuration = 60 * 1000; // 1 minute countdown
            const gracePeriod = 1000; // 1 second grace period to prevent last-second resets
            let lastActivityTime = Date.now(); // Track the last user activity time
            let countdownInterval = null;
            let countdownStartTime = null; // Track when the countdown starts
            let isRedirecting = false; // Flag to prevent multiple redirects

            function resetTimer() {
                const elapsed = Date.now() - lastActivityTime;
                const timeUntilTimeout = inactivityTimeout - elapsed;

                // Prevent reset if we're within the grace period before timeout
                if (timeUntilTimeout <= gracePeriod) {
                    console.log('Grace period active, ignoring reset');
                    return;
                }

                lastActivityTime = Date.now(); // Update the last activity time
                // Clear any existing countdown
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }
                $('#inactivity-timer').hide().removeClass('visible').removeClass('warning');
                // Start checking for inactivity
                checkInactivity();
            }

            function checkInactivity() {
                // Clear any existing interval to prevent overlap
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }

                const elapsed = Date.now() - lastActivityTime;
                const timeUntilThreshold = timerThreshold - elapsed;
                const timeUntilTimeout = inactivityTimeout - elapsed;

                if (elapsed >= inactivityTimeout) {
                    console.log('Inactivity timeout reached, redirecting');
                    clearCartAndRedirect();
                    return;
                }

                if (elapsed >= timerThreshold && !countdownInterval) {
                    // Start the countdown if the threshold is reached and no countdown is running
                    startCountdown();
                } else {
                    // Schedule the next check
                    setTimeout(checkInactivity, Math.min(timeUntilThreshold, timeUntilTimeout));
                }
            }

            function startCountdown() {
                countdownStartTime = Date.now();
                $('#inactivity-timer').show().addClass('visible');
                const $countdown = $('#timer-countdown');

                countdownInterval = setInterval(function() {
                    const elapsedSinceStart = Date.now() - countdownStartTime;
                    const remainingMs = countdownDuration - elapsedSinceStart;
                    const remainingSeconds = Math.max(0, Math.floor(remainingMs / 1000));

                    $countdown.text(remainingSeconds.toString().padStart(2, '0') + ' Sekunden');

                    // Add warning class for the last 10 seconds
                    if (remainingSeconds <= 10) {
                        $('#inactivity-timer').addClass('warning');
                    } else {
                        $('#inactivity-timer').removeClass('warning');
                    }

                    if (remainingSeconds <= 0) {
                        console.log('Countdown reached 0, redirecting');
                        clearInterval(countdownInterval);
                        countdownInterval = null;
                        clearCartAndRedirect();
                    }
                }, 1000);
            }

            function clearCartAndRedirect() {
                if (isRedirecting) {
                    console.log('Redirect already in progress, skipping');
                    return;
                }
                isRedirecting = true;

                console.log('Calling clear_cart.php');
                $.ajax({
                    url: 'clear_cart.php',
                    type: 'POST',
                    dataType: 'json',
                })
                .done(function(response) {
                    console.log('clear_cart.php response:', response);
                    if (response.status === 'success') {
                        localStorage.clear();
                        window.location.href = 'index.php';
                    } else {
                        console.error('Clear cart failed:', response.message);
                        localStorage.clear();
                        window.location.href = 'index.php';
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Clear cart AJAX error:', status, error, xhr.responseText);
                    localStorage.clear();
                    window.location.href = 'index.php';
                });
            }

            $('#back-to-home').on('click', function() {
                console.log('Back to home clicked');
                clearCartAndRedirect();
            });

            // Listen for user activity
            $(document).on('mousemove keydown click', resetTimer);

            // Start the timer on page load
            resetTimer();
        });
    </script>
    <?php include_once './config/floating_bar.php'; ?>
    <?php include_once './config/footer.php'; ?>
</body>
</html>