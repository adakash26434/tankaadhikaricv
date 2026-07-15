<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/file_upload.php';

// ── SESSION TIMEOUT (in seconds) ────────────────────────────────────────────────
//   1800 = 30 min  |  3600 = 1 hour  |  7200 = 2 hours (default)
if (!defined('SESSION_TIMEOUT_SECS')) {
    define('SESSION_TIMEOUT_SECS', 7200);
}

// ── SESSION BOOTSTRAP ──────────────────────────────────────────────────────────
// Minimal session start. No custom cookie params — PHP's defaults are the most
// compatible across cPanel, Cloudflare, Apache, FastCGI, Nginx.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireAdmin(): void {
    if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if (time() - $lastActivity > SESSION_TIMEOUT_SECS) {
            $_SESSION['_timed_out'] = true;
            session_unset();
            session_destroy();
            if (session_status() === PHP_SESSION_ACTIVE) { session_write_close(); }
            header('Location: login.php');
            exit;
        }
        $_SESSION['last_activity'] = time();
        session_write_close();
        return;
    }
    header('Location: login.php');
    exit;
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
