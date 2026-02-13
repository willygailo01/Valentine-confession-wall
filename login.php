<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

if (is_admin_logged_in()) {
    redirect('dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_abort($_POST['csrf_token'] ?? null);

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (hash_equals(ADMIN_USERNAME, $username) && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = ADMIN_USERNAME;

        redirect('dashboard.php');
    }

    usleep(250000);
    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Valentine Confession Wall</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="floating-hearts" aria-hidden="true"></div>

    <main class="page-wrap compact">
        <section class="form-card admin-login-card">
            <h1>Private Admin Access</h1>
            <p>Only the owner can read incoming confessions.</p>

            <?php if ($error): ?>
                <div class="alert error"><p><?= e($error) ?></p></div>
            <?php endif; ?>

            <form action="login.php" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn btn-primary">Log In</button>
            </form>
        </section>
    </main>

    <script src="assets/js/script.js" defer></script>
</body>
</html>
