<?php
require_once __DIR__ . '/../config/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
verify_csrf();
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
if (!$email) json_response(['ok' => false, 'message' => 'Please enter a valid email address.'], 422);
try {
    $stmt = db()->prepare('INSERT INTO newsletter_subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE is_active = 1');
    $stmt->execute([$email]);
    json_response(['ok' => true, 'message' => 'You are on the list. Look out for thoughtful notes from Solus.']);
} catch (Throwable $e) {
    json_response(['ok' => false, 'message' => 'We could not add you right now. Please try again.'], 500);
}
