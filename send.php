<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

$success = get_flash('success');
$error = get_flash('error');
$feedbackSuccess = get_flash('feedback_success');
$feedbackError = get_flash('feedback_error');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send a Confession | Valentine Confession Wall</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="send-page">
    <div class="floating-hearts" aria-hidden="true"></div>
    <a class="top-admin-btn" href="login.php">Admin Login</a>

    <main class="page-wrap">
        <header class="page-header">
            <h1>Write Your Confession</h1>
            <p>Write your message for the person you want to tell.</p>
            <p>This is a confession wall for feelings, honesty, and release.</p>
            <a class="back-link" href="index.php">Back to Confession Wall</a>
        </header>

        <section class="notice-card">
            <h2>Instruction</h2>
            <p>Write your message for the person you care about.</p>
            <p>We will not read this as a reply. This space is for your feelings.</p>
            <p class="notice-note">Reminder: Avoid hate, attacks, or sharing private personal information.</p>
        </section>

        <?php if ($success): ?>
            <section class="alert success success-pop" id="successMessage">
                <div class="envelope-animation" aria-hidden="true"></div>
                <p><?= e($success) ?></p>
            </section>
        <?php endif; ?>

        <?php if ($error): ?>
            <section class="alert error">
                <p><?= e($error) ?></p>
            </section>
        <?php endif; ?>

        <section class="form-card">
            <form action="submit.php" method="POST" id="confessionForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <label for="nickname">Optional Nickname</label>
                <input
                    type="text"
                    id="nickname"
                    name="nickname"
                    maxlength="80"
                    placeholder="Your secret nickname (optional)"
                    autocomplete="off"
                >

                <label for="message">Message</label>
                <textarea
                    id="message"
                    name="message"
                    maxlength="<?= MESSAGE_MAX_LENGTH ?>"
                    required
                    placeholder="Write your confession here..."
                ></textarea>

                <div class="meter-wrap" aria-hidden="true">
                    <div class="meter-label">Love Meter</div>
                    <div class="meter-track">
                        <div class="meter-fill" id="loveMeter"></div>
                    </div>
                </div>

                <div class="form-meta">
                    <span id="charCount">0 / <?= MESSAGE_MAX_LENGTH ?></span>
                    <span>Confession wall submission</span>
                </div>

                <button type="submit" class="btn btn-primary">Release My Feelings</button>
            </form>

            <div class="music-box">
                <button type="button" class="btn btn-soft" id="musicToggle">Play Romantic Music</button>
                <audio id="bgMusic" preload="none" loop>
                    <source src="folder-audio/romantic.mp3" type="audio/mpeg">
                </audio>
                <small>Add your MP3 at <code>folder-audio/romantic.mp3</code></small>
            </div>
        </section>

        <section class="form-card feedback-card" id="feedback">
            <h2>App Feedback for All Users</h2>
            <p>Tell us if this app needs improvement or if you want new features.</p>

            <?php if ($feedbackSuccess): ?>
                <section class="alert success">
                    <p><?= e($feedbackSuccess) ?></p>
                </section>
            <?php endif; ?>

            <?php if ($feedbackError): ?>
                <section class="alert error">
                    <p><?= e($feedbackError) ?></p>
                </section>
            <?php endif; ?>

            <form action="submit-feedback.php" method="POST" id="feedbackForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <label for="feedback_nickname">Optional Nickname</label>
                <input
                    type="text"
                    id="feedback_nickname"
                    name="feedback_nickname"
                    maxlength="80"
                    placeholder="Your name or alias (optional)"
                    autocomplete="off"
                >

                <label for="feedback_type">Feedback Type</label>
                <select id="feedback_type" name="feedback_type" required>
                    <option value="general">General feedback</option>
                    <option value="improvement">Need improvement</option>
                    <option value="feature_request">Need feature update</option>
                </select>

                <label for="feedback_comment">Comment</label>
                <textarea
                    id="feedback_comment"
                    name="feedback_comment"
                    maxlength="<?= FEEDBACK_MAX_LENGTH ?>"
                    required
                    placeholder="Write what we should improve or add..."
                ></textarea>

                <div class="form-meta">
                    <span id="feedbackCharCount">0 / <?= FEEDBACK_MAX_LENGTH ?></span>
                    <span>Visible to admin for updates</span>
                </div>

                <button type="submit" class="btn btn-primary">Send Feedback</button>
            </form>
        </section>
    </main>

    <script src="assets/js/script.js" defer></script>
</body>
</html>
