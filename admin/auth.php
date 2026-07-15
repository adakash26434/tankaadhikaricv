<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/file_upload.php';

// ── SESSION TIMEOUT (in seconds) ────────────────────────────────────────────────
//   1800 = 30 min  |  3600 = 1 hour  |  7200 = 2 hours (default)
if (!defined('SESSION_TIMEOUT_SECS')) {
    define('SESSION_TIMEOUT_SECS', 7200);
}

// ── SESSION BOOTSTRAP ──────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── CSRF TOKEN ────────────────────────────────────────────────────────────────
function csrfToken(): string {
    // Generate fresh token if none exists in this session
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        // CRITICAL: ensure token is written to storage BEFORE any output
        session_write_close();
        // Re-open session for continued use (e.g. verifyCsrf on POST)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrf(): void {
    // Ensure session is open for reading
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token  = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';
    // Regenerate token after verification (one-time use)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    if (!hash_equals($stored, $token)) {
        http_response_code(403);
        die('<p style="font-family:monospace;padding:20px;color:#f87171;background:#0f1420;min-height:100vh">⚠️ Invalid security token. Please go back and try again.</p>');
    }
}

// ── ADMIN REQUIRE ─────────────────────────────────────────────────────────────
function requireAdmin(): void {
    if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if (time() - $lastActivity > SESSION_TIMEOUT_SECS) {
            $_SESSION['_timed_out'] = true;
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit;
        }
        $_SESSION['last_activity'] = time();
        // Do NOT call session_write_close() here!
        // csrfToken() needs an open session to write the token to storage.
        // PHP will auto-flush session data when the script ends.
        return;
    }
    header('Location: login.php');
    exit;
}

function isAdmin(): bool {
    return !empty($_SESSION[ADMIN_SESSION_KEY]);
}
