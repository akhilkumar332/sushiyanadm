<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        footer {
            background: linear-gradient(135deg, #6A2477 0%, #4A1A55 100%);
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            font-family: 'Arial', sans-serif;
            position: relative;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.3);
            box-sizing: border-box; /* Scoped to footer */
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, rgba(106, 36, 119, 0) 70%);
            animation: glow 10s infinite alternate;
            z-index: 0;
        }

        .footer-content {
            position: relative;
            z-index: 1;
            max-width: 100%;
            box-sizing: border-box; /* Scoped to footer-content */
        }

        .footer-text {
            font-size: 18px;
            font-weight: 300;
            margin: 0 0 10px 0;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            box-sizing: border-box; /* Scoped */
        }

        .footer-links {
            margin-top: 10px;
            white-space: nowrap;
            box-sizing: border-box; /* Scoped */
        }

        .footer-links a {
            color: #D4AF37; /* Gold accent */
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            margin: 0 15px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .footer-links a:hover {
            color: #fff;
            transform: translateY(-2px);
        }

        .footer-links a:not(:last-child)::after {
            content: '|';
            color: #fff;
            margin-left: 15px;
        }

        @keyframes glow {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.05); opacity: 1; }
        }

        @media (max-width: 768px) {
            footer {
                padding: 20px 10px;
            }

            .footer-text {
                font-size: 16px;
            }

            .footer-links {
                font-size: 0; /* Spacing hack */
            }

            .footer-links a {
                font-size: 14px;
                margin: 0 8px;
            }

            .footer-links a:not(:last-child)::after {
                margin-left: 8px;
            }
        }
    </style>
</head>
<body>
    <footer>
        <div class="footer-content">
            <p class="footer-text">Sushi Yana © All Rights Reserved. 2025</p>
            <div class="footer-links">
                <a href="/impressum.php">Impressum</a>
                <a href="/datenschutz.php">Datenschutz</a>
            </div>
        </div>
    </footer>
</body>
</html>