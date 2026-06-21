<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['response' => 'error', 'error' => 'Method not allowed']);
    exit;
}

session_start();
$now = time();
$windowSecs = 60;
$maxPerWindow = 3;
$reqs = $_SESSION['contact_requests'] ?? [];
$reqs = array_filter($reqs, fn($t) => $now - $t < $windowSecs);
if (count($reqs) >= $maxPerWindow) {
    http_response_code(429);
    echo json_encode(['response' => 'error', 'error' => 'Too many messages. Please wait a minute and try again.']);
    exit;
}

$body    = json_decode(file_get_contents('php://input'), true);
$name    = trim($body['name']    ?? '');
$email   = trim($body['email']   ?? '');
$subject = trim($body['subject'] ?? '');
$message = trim($body['message'] ?? '');
$hp      = trim($body['website'] ?? '');

if ($hp !== '') {
    echo json_encode(['response' => 'success']);
    exit;
}

if (!$name || !$email || !$message) {
    echo json_encode(['response' => 'error', 'error' => 'Please fill all required fields.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['response' => 'error', 'error' => 'Invalid email address.']);
    exit;
}
if (mb_strlen($name) > 100) {
    echo json_encode(['response' => 'error', 'error' => 'Name is too long (max 100 characters).']);
    exit;
}
if (mb_strlen($subject) > 300) {
    echo json_encode(['response' => 'error', 'error' => 'Subject is too long (max 300 characters).']);
    exit;
}
if (mb_strlen($message) > 3000) {
    echo json_encode(['response' => 'error', 'error' => 'Message is too long (max 3000 characters).']);
    exit;
}

dbExec("INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)", [$name, $email, $subject, $message]);

$reqs[] = $now;
$_SESSION['contact_requests'] = array_values($reqs);

echo json_encode(['response' => 'success']);
