<footer>
    <div class="footer-content">
        <div class="footer-links">
            <a href="/impressum.php">Impressum</a>
            <span class="footer-separator">|</span>
            <a href="/datenschutz.php">Datenschutz</a>
        </div>
        <div class="footer-divider"></div>
        <p class="footer-text">Sushi Yana © All Rights Reserved. 2025</p>
    </div>
</footer>

<style>
    footer {
        background: linear-gradient(135deg, #2a1a2f 0%, #1a0f1d 100%); /* Darker gradient to match site theme */
        color: #fff;
        text-align: center;
        padding: 40px 20px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        position: relative;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.5);
        box-sizing: border-box;
        border-top: 2px solid #6A2477; /* Match the purple border used elsewhere */
    }

    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(106, 36, 119, 0.15) 0%, rgba(106, 36, 119, 0) 70%);
        animation: glow 8s ease-in-out infinite alternate;
        z-index: 0;
    }

    .footer-content {
        position: relative;
        z-index: 1;
        max-width: 100%;
        box-sizing: border-box;
        opacity: 0; /* Initial state for fade-in animation */
        animation: fadeInFooter 1s ease forwards; /* Fade-in animation */
    }

    .footer-divider {
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #6A2477, transparent); /* Gradient divider */
        margin: 15px auto;
        opacity: 0.8;
    }

    .footer-text {
        font-size: 16px;
        font-weight: 300;
        margin: 15px 0;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
    }

    .footer-links {
        margin: 15px 0;
        white-space: nowrap;
        box-sizing: border-box;
        display: flex; /* Use flexbox for better control over spacing */
        justify-content: center;
        align-items: center;
        gap: 15px; /* Equal spacing between items */
    }

    .footer-links a {
        color: #D4AF37; /* Gold accent */
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: color 0.3s ease, transform 0.3s ease, text-shadow 0.3s ease; /* Added text-shadow transition */
        position: relative;
        display: inline-block;
    }

    .footer-links a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        bottom: -2px;
        left: 0;
        background-color: #fff;
        transition: width 0.3s ease;
        z-index: 1; /* Ensure the underline is above other elements */
    }

    .footer-links a:hover::after {
        width: 100%;
    }

    .footer-links a:hover {
        color: #fff;
        transform: translateY(-2px);
        text-shadow: 0 0 8px rgba(212, 175, 55, 0.5); /* Glow effect on hover */
    }

    .footer-separator {
        color: #fff;
        font-size: 15px;
        font-weight: 500;
    }

    /* Fade-in animation for footer content */
    @keyframes fadeInFooter {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Refined glow effect for background */
    @keyframes glow {
        0% {
            transform: scale(1);
            opacity: 0.5;
        }
        100% {
            transform: scale(1.03);
            opacity: 0.8; /* Softer opacity for a subtler effect */
        }
    }

    @media (max-width: 768px) {
        footer {
            padding: 30px 15px;
        }

        .footer-text {
            font-size: 14px;
        }

        .footer-links {
            white-space: normal; /* Allow wrapping on small screens */
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .footer-links a {
            font-size: 14px;
        }

        .footer-separator {
            font-size: 14px;
        }
    }
</style>