<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('send.php#feedback');
}

verify_csrf_or_abort($_POST['csrf_token'] ?? null);

$now = time();
$lastSubmit = (int) ($_SESSION['last_feedback_submit_time'] ?? 0);

if ($lastSubmit > 0 && ($now - $lastSubmit) < FEEDBACK_RATE_LIMIT_SECONDS) {
    $wait = FEEDBACK_RATE_LIMIT_SECONDS - ($now - $lastSubmit);
    set_flash('feedback_error', 'Please wait ' . $wait . ' second(s) before sending another feedback.');
    redirect('send.php#feedback');
}

$nickname = trim((string) ($_POST['feedback_nickname'] ?? ''));
$feedbackType = trim((string) ($_POST['feedback_type'] ?? 'general'));
$comment = trim((string) ($_POST['feedback_comment'] ?? ''));
$allowedTypes = ['general', 'improvement', 'feature_request'];

if ($nickname !== '' && str_length($nickname) > 80) {
    set_flash('feedback_error', 'Nickname is too long.');
    redirect('send.php#feedback');
}

if (!in_array($feedbackType, $allowedTypes, true)) {
    set_flash('feedback_error', 'Feedback type is invalid.');
    redirect('send.php#feedback');
}

if ($comment === '') {
    set_flash('feedback_error', 'Feedback comment is required.');
    redirect('send.php#feedback');
}

if (str_length($comment) > FEEDBACK_MAX_LENGTH) {
    set_flash('feedback_error', 'Feedback comment cannot exceed ' . FEEDBACK_MAX_LENGTH . ' characters.');
    redirect('send.php#feedback');
}

try {
    $conn = db();
    cleanup_old_feedback($conn);

    $sql = 'INSERT INTO feedback_comments (nickname, feedback_type, comment, is_reviewed) VALUES (NULLIF(?, \'\'), ?, ?, 0)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $nickname, $feedbackType, $comment);
    $stmt->execute();
    $stmt->close();

    $_SESSION['last_feedback_submit_time'] = $now;

    set_flash('feedback_success', 'Thank you for your feedback. We will use this for future updates.');
} catch (Throwable $exception) {
    set_flash('feedback_error', 'Unable to send feedback right now. Please try again.');
}

redirect('send.php#feedback');
