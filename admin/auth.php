<?php
require_once __DIR__ . '/../config.php';

// ── SESSION TIMEOUT CONFIGURATION ──────────────────────────────────────────────
if (!defined('SESSION_TIMEOUT_SECS')) {
    define('SESSION_TIMEOUT_SECS', 7200); // 2 hours idle timeout
}

// ── BOOTSTRAP SESSION ──────────────────────────────────────────────────────────
// Use PHP's default session handling — no custom cookie params.
// PHP's defaults (PHPSESSID cookie, / path, etc.) are the most compatible
// across all hosting environments including cPanel, Cloudflare, Apache, FastCGI.
if (session_status() === PHP_SESSION_NONE) {
    // Start session using PHP's default cookie name and settings
    // This is the most portable approach — let PHP/hosting handle the details
    @session_start();
}

// Periodically regenerate session ID for security (skip on login page)
$script = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($script, '/admin/login') !== 0 && session_status() === PHP_SESSION_ACTIVE) {
    if (empty($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 300) {
        @session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

function requireAdmin(): void {
    if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if (time() - $lastActivity > SESSION_TIMEOUT_SECS) {
            $_SESSION['_timed_out'] = true;
            @session_unset();
            @session_destroy();
            header('Location: login.php');
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
    if (empty($_SESSION[ADMIN_SESSION_KEY])) {
        header('Location: login.php');
        exit;
    }
    @session_write_close();
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
