<?php
require_once __DIR__ . '/../config.php';

// ── SESSION TIMEOUT CONFIGURATION ──────────────────────────────────────────────
//   1800  = 30 minutes   |   3600 = 1 hour   |   7200 = 2 hours (default)
if (!defined('SESSION_TIMEOUT_SECS')) {
    define('SESSION_TIMEOUT_SECS', 7200);
}

// ── BOOTSTRAP SESSION ──────────────────────────────────────────────────────────
// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    // Detect HTTPS reliably — works behind Cloudflare, CDN, reverse proxies
    $isHttps = (
        isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
        || isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on'
        || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443
        || !empty($_SERVER['HTTP_CF_VISITOR']) // Cloudflare
    );

    @session_set_cookie_params([
        'lifetime' => 0,    // 0 = session cookie (expires when browser closes)
        'path'     => '/',
        'secure'   => $isHttps ? true : false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    @session_start();

    // Regenerate session ID every 5 minutes to prevent fixation
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
