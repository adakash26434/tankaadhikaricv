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

// Site base URL (with trailing slash)
define('SITE_URL', '');
