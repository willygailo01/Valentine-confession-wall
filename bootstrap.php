<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$cookieParams = session_get_cookie_params();
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => $cookieParams['path'],
    'domain' => $cookieParams['domain'],
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function db(): mysqli
{
    static $mysqli = null;

    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    } catch (mysqli_sql_exception $exception) {
        if ((int) $exception->getCode() !== 1049) {
            throw $exception;
        }

        // Auto-create the database if it doesn't exist yet (useful for first local run).
        if (!preg_match('/^[A-Za-z0-9_]+$/', DB_NAME)) {
            throw $exception;
        }

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
        $mysqli->set_charset('utf8mb4');
        $mysqli->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $mysqli->select_db(DB_NAME);
    }

    $mysqli->set_charset('utf8mb4');
    ensure_messages_table($mysqli);
    ensure_feedback_table($mysqli);

    return $mysqli;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function str_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }

    return strlen($value);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_or_abort(?string $token): void
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
        http_response_code(400);
        exit('Invalid CSRF token.');
    }
}

function cleanup_old_messages(mysqli $conn): void
{
    $days = AUTO_DELETE_DAYS;
    $query = 'DELETE FROM messages WHERE created_at < (NOW() - INTERVAL ? DAY)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $days);
    $stmt->execute();
    $stmt->close();
}

function cleanup_old_feedback(mysqli $conn): void
{
    $days = AUTO_DELETE_DAYS;
    $query = 'DELETE FROM feedback_comments WHERE created_at < (NOW() - INTERVAL ? DAY)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $days);
    $stmt->execute();
    $stmt->close();
}

function ensure_messages_table(mysqli $conn): void
{
    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nickname VARCHAR(80) NULL,
    message VARCHAR(500) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_messages_created_at (created_at),
    KEY idx_messages_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

    $conn->query($sql);
}

function ensure_feedback_table(mysqli $conn): void
{
    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS feedback_comments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nickname VARCHAR(80) NULL,
    feedback_type VARCHAR(40) NOT NULL DEFAULT 'general',
    comment VARCHAR(700) NOT NULL,
    is_reviewed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_feedback_created_at (created_at),
    KEY idx_feedback_is_reviewed (is_reviewed),
    KEY idx_feedback_type (feedback_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

    $conn->query($sql);
}

function set_flash(string $key, string $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function get_flash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return is_string($value) ? $value : null;
}
