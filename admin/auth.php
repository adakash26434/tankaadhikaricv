<?php
require_once __DIR__ . '/../config.php';

// ── SESSION CONFIGURATION ──────────────────────────────────────────────────────
// Timeout in seconds: 7200 = 2 hours (logged in users stay logged in 2 hours)
// Adjust this value to change auto-logout time:
//   1800  = 30 minutes (default, more secure)
//   3600  = 1 hour
//   7200  = 2 hours
//  14400  = 4 hours
//  86400  = 24 hours (least secure)
if (!defined('SESSION_TIMEOUT_SECS')) {
    define('SESSION_TIMEOUT_SECS', 7200); // Default: 2 hours
}

// ── BOOTSTRAP SESSION ──────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    // Secure cookie settings for production
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT_SECS,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('TANKAAD_SESSION');
    session_start();

    // Regenerate session ID periodically to prevent fixation attacks
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 300) {
        // Regenerate every 5 minutes
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

function requireAdmin(): void {
    if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if (time() - $lastActivity > SESSION_TIMEOUT_SECS) {
            $_SESSION['_timed_out'] = true; // Persist timeout flag in session
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
    if (empty($_SESSION[ADMIN_SESSION_KEY])) {
        header('Location: login.php');
        exit;
    }
    // Close session early to prevent write locks blocking other requests
    session_write_close();
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
