<?php
// ============================================================
// DATABASE CONFIGURATION — cPanel ma setup garda change garnu
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');       // cPanel database name
define('DB_USER', 'your_db_user');       // cPanel database user
define('DB_PASS', 'your_db_password');   // cPanel database password

// ============================================================
// ADMIN PASSWORD — superadmin.php file ma change garnu
// ============================================================
require_once __DIR__ . '/superadmin.php';  // Password yahibata aaucha

define('ADMIN_SESSION_KEY', 'tanka_admin_auth');

// ============================================================
// 2FA / TWO-FACTOR AUTHENTICATION
// ============================================================
// Remove the // prefix to DISABLE 2FA (Google Authenticator):
// define('DISABLE_2FA', true);

// ============================================================
// ADMIN SESSION TIMEOUT — idle time paune paxi logout huncha
// ============================================================
// Seconds:   1800 = 30 min   |  7200 = 2 hours  |  14400 = 4 hours
// Yo value config ma define na vaye default 2 hours use huncha
if (!defined('SESSION_TIMEOUT_SECS')) {
    define('SESSION_TIMEOUT_SECS', 7200);  // 7200 = 2 hours idle timeout
}

// Site base URL (with trailing slash)
define('SITE_URL', '');
