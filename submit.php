<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('send.php');
}

verify_csrf_or_abort($_POST['csrf_token'] ?? null);

$now = time();
$lastSubmit = (int) ($_SESSION['last_submit_time'] ?? 0);

if ($lastSubmit > 0 && ($now - $lastSubmit) < RATE_LIMIT_SECONDS) {
    $wait = RATE_LIMIT_SECONDS - ($now - $lastSubmit);
    set_flash('error', 'Please wait ' . $wait . ' second(s) before sending another message.');
    redirect('send.php');
}

$nickname = trim((string) ($_POST['nickname'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($nickname !== '' && str_length($nickname) > 80) {
    set_flash('error', 'Nickname is too long.');
    redirect('send.php');
}

if ($message === '') {
    set_flash('error', 'Message is required.');
    redirect('send.php');
}

if (str_length($message) > MESSAGE_MAX_LENGTH) {
    set_flash('error', 'Message cannot exceed ' . MESSAGE_MAX_LENGTH . ' characters.');
    redirect('send.php');
}

try {
    $conn = db();
    cleanup_old_messages($conn);

    $sql = 'INSERT INTO messages (nickname, message, is_read) VALUES (NULLIF(?, \'\'), ?, 0)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $nickname, $message);
    $stmt->execute();
    $stmt->close();

    $_SESSION['last_submit_time'] = $now;

    set_flash('success', 'Your confession was sent with love.');
} catch (Throwable $exception) {
    set_flash('error', 'Unable to send your confession right now. Please try again.');
}

redirect('send.php');
