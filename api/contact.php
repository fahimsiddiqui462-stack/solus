<?php
require_once __DIR__ . '/../config/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
verify_csrf();
$name = clean_string($_POST['name'] ?? '', 120);
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$message = clean_string($_POST['message'] ?? '', 3000);
if (!$name || !$email || !$message) json_response(['ok' => false, 'message' => 'Please provide your name, a valid email, and a message.'], 422);
try {
    $stmt = db()->prepare('INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $message]);
    json_response(['ok' => true, 'message' => 'Thanks—your message has been received. We typically reply within 12 hours.']);
} catch (Throwable $e) {
    json_response(['ok' => false, 'message' => 'We could not send your message right now. Please try again.'], 500);
}
