<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentine Confession Wall</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-page">
    <div class="floating-hearts" aria-hidden="true"></div>
    <a class="top-admin-btn" href="login.php">Admin Login</a>

    <main class="center-wrap">
        <section class="hero-copy">
            <h1>Valentine Confession Wall</h1>
            <p>Do you have something to confess?</p>
            <p>Do you have words you have been holding in for a long time?</p>
            <p>Share what you truly feel for your crush, secret love, ex, or someone you can no longer talk to.</p>
            <div class="hero-lines">
                <p>This is not for the admin.</p>
                <p>This is not for us.</p>
                <p>This is for the person you want to tell.</p>
            </div>
        </section>

        <section class="card-scene" aria-label="Valentine Card">
            <div class="valentine-card" id="valentineCard">
                <div class="card-face card-front">
                    <span class="card-stamp">Confess</span>
                    <h2>Open your heart and let it out</h2>
                    <button type="button" class="btn btn-primary" id="openCardBtn">Open Confession Wall</button>
                </div>
                <div class="card-face card-back">
                    <h3>Sweet, painful, honest, anonymous</h3>
                    <p>No judgment. Just truth from your heart.</p>
                    <a class="btn btn-primary" href="send.php">Leave a Message</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-note">
        <p>Write it. Send it. Release it.</p>
    </footer>

    <script src="assets/js/script.js" defer></script>
</body>
</html>
