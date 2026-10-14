<?php
require_once __DIR__ . '/../config/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
verify_csrf();
$name = clean_string($_POST['name'] ?? '', 120);
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone = clean_string($_POST['phone'] ?? '', 40);
$therapistId = filter_var($_POST['therapist_id'] ?? '', FILTER_VALIDATE_INT) ?: null;
$date = clean_string($_POST['preferred_date'] ?? '', 10) ?: null;
$requestedType = $_POST['session_type'] ?? 'online';
$sessionType = in_array($requestedType, ['online', 'in-person'], true) ? $requestedType : 'online';
$message = clean_string($_POST['message'] ?? '', 2000);
if (!$name || !$email) json_response(['ok' => false, 'message' => 'Please provide your name and a valid email.'], 422);
if ($date && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) $date = null;
try {
    if ($therapistId) {
        $check = db()->prepare('SELECT id FROM therapists WHERE id = ? AND is_active = 1');
        $check->execute([$therapistId]);
        if (!$check->fetch()) $therapistId = null;
    }
    $stmt = db()->prepare('INSERT INTO appointments (name, email, phone, therapist_id, preferred_date, session_type, message) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone ?: null, $therapistId, $date, $sessionType, $message ?: null]);
    json_response(['ok' => true, 'message' => 'Your request is in. Our team will contact you to confirm a time.']);
} catch (Throwable $e) {
    json_response(['ok' => false, 'message' => 'We could not submit your request right now. Please try again.'], 500);
}
