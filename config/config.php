<?php
// Copy these values from your hosting control panel. Never publish real credentials.
define('DB_HOST', getenv('SOLUS_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('SOLUS_DB_NAME') ?: 'solus');
define('DB_USER', getenv('SOLUS_DB_USER') ?: 'root');
define('DB_PASS', getenv('SOLUS_DB_PASS') ?: '');
define('APP_NAME', 'Solus');
define('APP_URL', getenv('SOLUS_APP_URL') ?: 'http://localhost/solus');
date_default_timezone_set('UTC');

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function start_app_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('solus_session');
        session_start();
    }
}

function csrf_token(): string {
    start_app_session();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    start_app_session();
    $token = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        json_response(['ok' => false, 'message' => 'Your session expired. Please refresh and try again.'], 419);
    }
}

function json_response(array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function clean_string(string $value, int $max = 5000): string {
    return trim(mb_substr($value, 0, $max));
}

function admin_required(): void {
    start_app_session();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
