<?php
require_once __DIR__ . '/../config.php';
session_start();

define('SESSION_TIMEOUT_SECS', 1800); // 30 minutes idle auto-logout

function requireAdmin(): void {
    // Session idle timeout check
    if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if (time() - $lastActivity > SESSION_TIMEOUT_SECS) {
            session_unset();
            session_destroy();
            header('Location: login.php?timeout=1');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
    if (empty($_SESSION[ADMIN_SESSION_KEY])) {
        header('Location: login.php');
        exit;
    }
}

function isAdmin(): bool {
    return !empty($_SESSION[ADMIN_SESSION_KEY]);
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrf(): void {
    $token  = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';
    if (!$stored || !hash_equals($stored, $token)) {
        http_response_code(403);
        die('<p style="font-family:monospace;padding:20px;color:#f87171;background:#0f1420;min-height:100vh">⚠️ Invalid security token. Please go back and try again.</p>');
    }
}
