<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

require_admin();

$messages = [];
$feedbackItems = [];
$success = get_flash('success');
$error = null;
$unreadCount = 0;
$unreviewedFeedbackCount = 0;

function feedback_type_label(string $type): string
{
    if ($type === 'improvement') {
        return 'Need improvement';
    }

    if ($type === 'feature_request') {
        return 'Need feature update';
    }

    return 'General feedback';
}

function feedback_type_class(string $type): string
{
    if ($type === 'improvement') {
        return 'improvement';
    }

    if ($type === 'feature_request') {
        return 'feature-request';
    }

    return 'general';
}

try {
    $conn = db();
    cleanup_old_messages($conn);
    cleanup_old_feedback($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf_or_abort($_POST['csrf_token'] ?? null);

        $action = (string) ($_POST['action'] ?? '');
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id && $id > 0) {
            if ($action === 'mark_read') {
                $stmt = $conn->prepare('UPDATE messages SET is_read = 1 WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Message marked as read.');
            }

            if ($action === 'delete') {
                $stmt = $conn->prepare('DELETE FROM messages WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Message deleted.');
            }

            if ($action === 'mark_feedback_reviewed') {
                $stmt = $conn->prepare('UPDATE feedback_comments SET is_reviewed = 1 WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Feedback marked as reviewed.');
            }

            if ($action === 'delete_feedback') {
                $stmt = $conn->prepare('DELETE FROM feedback_comments WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Feedback deleted.');
            }
        }

        redirect('dashboard.php');
    }

    $result = $conn->query('SELECT id, nickname, message, is_read, created_at FROM messages ORDER BY created_at DESC');
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();

    foreach ($messages as $messageRow) {
        if ((int) $messageRow['is_read'] === 0) {
            $unreadCount++;
        }
    }

    $feedbackResult = $conn->query('SELECT id, nickname, feedback_type, comment, is_reviewed, created_at FROM feedback_comments ORDER BY created_at DESC');
    $feedbackItems = $feedbackResult->fetch_all(MYSQLI_ASSOC);
    $feedbackResult->close();

    foreach ($feedbackItems as $feedbackRow) {
        if ((int) $feedbackRow['is_reviewed'] === 0) {
            $unreviewedFeedbackCount++;
        }
    }
} catch (Throwable $exception) {
    $error = 'Database is not ready. Check MySQL/XAMPP status and config.php settings.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Valentine Confession Wall</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <div class="floating-hearts" aria-hidden="true"></div>

    <main class="page-wrap dashboard-wrap">
        <header class="dashboard-header">
            <h1>Valentine Inbox</h1>
            <p>
                Messages: <?= count($messages) ?> (Unread: <?= $unreadCount ?>)
                | Feedback: <?= count($feedbackItems) ?> (Pending: <?= $unreviewedFeedbackCount ?>)
            </p>
            <div class="dashboard-actions">
                <a class="btn btn-soft" href="index.php">Public View</a>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </header>

        <?php if ($success): ?>
            <div class="alert success"><p><?= e($success) ?></p></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><p><?= e($error) ?></p></div>
        <?php endif; ?>

        <section class="dashboard-section">
            <h2>Confessions</h2>
            <?php if (empty($messages)): ?>
                <section class="empty-state">
                    <h3>No confessions yet</h3>
                    <p>When someone sends a message, it will show up here.</p>
                </section>
            <?php else: ?>
                <section class="messages-grid">
                    <?php foreach ($messages as $row): ?>
                        <article class="message-card <?= ((int) $row['is_read'] === 1) ? 'read' : 'unread' ?>">
                            <header>
                                <h3><?= $row['nickname'] !== null && $row['nickname'] !== '' ? e($row['nickname']) : 'Anonymous' ?></h3>
                                <time datetime="<?= e((string) $row['created_at']) ?>"><?= e((string) $row['created_at']) ?></time>
                            </header>

                            <p><?= nl2br(e((string) $row['message'])) ?></p>

                            <footer>
                                <form method="POST" action="dashboard.php">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="action" value="mark_read">
                                    <button type="submit" class="btn btn-soft" <?= ((int) $row['is_read'] === 1) ? 'disabled' : '' ?>>
                                        Mark as read
                                    </button>
                                </form>

                                <form method="POST" action="dashboard.php" onsubmit="return confirm('Delete this message?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </section>

        <section class="dashboard-section">
            <h2>User Feedback</h2>
            <?php if (empty($feedbackItems)): ?>
                <section class="empty-state">
                    <h3>No feedback yet</h3>
                    <p>Feedback from users about improvements and features will appear here.</p>
                </section>
            <?php else: ?>
                <section class="messages-grid">
                    <?php foreach ($feedbackItems as $row): ?>
                        <article class="message-card <?= ((int) $row['is_reviewed'] === 1) ? 'read' : 'unread' ?>">
                            <header>
                                <h3><?= $row['nickname'] !== null && $row['nickname'] !== '' ? e($row['nickname']) : 'User feedback' ?></h3>
                                <span class="type-badge <?= e(feedback_type_class((string) $row['feedback_type'])) ?>">
                                    <?= e(feedback_type_label((string) $row['feedback_type'])) ?>
                                </span>
                                <time datetime="<?= e((string) $row['created_at']) ?>"><?= e((string) $row['created_at']) ?></time>
                            </header>

                            <p><?= nl2br(e((string) $row['comment'])) ?></p>

                            <footer>
                                <form method="POST" action="dashboard.php">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="action" value="mark_feedback_reviewed">
                                    <button type="submit" class="btn btn-soft" <?= ((int) $row['is_reviewed'] === 1) ? 'disabled' : '' ?>>
                                        Mark reviewed
                                    </button>
                                </form>

                                <form method="POST" action="dashboard.php" onsubmit="return confirm('Delete this feedback?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="action" value="delete_feedback">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </section>
    </main>

    <script src="assets/js/script.js" defer></script>
</body>
</html>
