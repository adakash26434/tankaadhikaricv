<?php
require_once __DIR__ . '/auth.php';
if (isAdmin()) { header('Location: index.php'); exit; }

$error = '';
$locked = false;

// Show timeout notice
if (isset($_GET['timeout'])) {
    $error = 'You were automatically logged out after 30 minutes of inactivity.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attempts    = (int)($_SESSION['login_attempts'] ?? 0);
    $lastAttempt = (int)($_SESSION['login_last_attempt'] ?? 0);
    $lockoutSecs = 300;

    if ($attempts >= 5 && (time() - $lastAttempt) < $lockoutSecs) {
        $remaining = $lockoutSecs - (time() - $lastAttempt);
        $error = 'Too many failed attempts. Please wait ' . ceil($remaining / 60) . ' minute(s).';
        $locked = true;
    } else {
        $input = $_POST['password'] ?? '';

        // Check for stored bcrypt hash first (admin/changepassword.php sets this)
        $hashFile = __DIR__ . '/password.php';
        $storedHash = null;
        if (is_file($hashFile)) {
            $hashData = include $hashFile;
            if (is_string($hashData) && strlen($hashData) > 20) {
                $storedHash = $hashData;
            }
        }

        $valid = false;
        if ($storedHash && (str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$2a$') || str_starts_with($storedHash, '$argon'))) {
            // Bcrypt / Argon2 stored hash
            $valid = password_verify($input, $storedHash);
        } else {
            // Legacy plain-text comparison (constant-time to prevent timing attacks)
            $valid = hash_equals(hash('sha256', ADMIN_PASSWORD), hash('sha256', $input));
        }

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION[ADMIN_SESSION_KEY] = true;
            unset($_SESSION['login_attempts'], $_SESSION['login_last_attempt']);
            header('Location: index.php'); exit;
        }

        $_SESSION['login_attempts']    = $attempts + 1;
        $_SESSION['login_last_attempt'] = time();
        $attemptsLeft = max(0, 5 - ($attempts + 1));
        $error = 'Incorrect password.' . ($attemptsLeft > 0 ? " $attemptsLeft attempt(s) remaining." : ' Account locked for 5 minutes.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login — Tanka Portfolio</title>
<meta name="robots" content="noindex, nofollow">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#0f1420;color:#c9d1e3;min-height:100vh;display:flex;align-items:center;justify-content:center}
.box{background:#161b27;border:1px solid #1e2638;border-radius:16px;padding:40px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,0.5)}
h1{font-size:20px;font-weight:700;color:#fff;margin-bottom:4px}
p{font-size:12px;color:#64748b;margin-bottom:28px}
label{display:block;font-size:12px;color:#8892a4;margin-bottom:6px;font-weight:500}
input[type=password]{width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;transition:border-color .2s}
input[type=password]:focus{border-color:#22d3ee;box-shadow:0 0 0 3px rgba(34,211,238,.12)}
.btn{width:100%;margin-top:16px;padding:12px;background:#22d3ee;border:none;border-radius:8px;color:#0f1420;font-weight:700;font-size:14px;cursor:pointer;transition:background .2s}
.btn:hover:not(:disabled){background:#06b6d4}
.btn:disabled{opacity:.5;cursor:not-allowed}
.btn:focus{outline:3px solid rgba(34,211,238,.5);outline-offset:2px}
.error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:16px}
.icon{width:48px;height:48px;background:rgba(34,211,238,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;font-size:22px}
</style>
</head>
<body>
<div class="box">
  <div class="icon">🔐</div>
  <h1>Admin Panel</h1>
  <p>Tanka Prasad Adhikari — Portfolio Management</p>
  <?php if($error): ?><div class="error" role="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <form method="POST" autocomplete="off">
    <label for="password">Admin Password</label>
    <input type="password" id="password" name="password" placeholder="Enter password" autofocus required <?=$locked?'disabled':''?> />
    <button class="btn" type="submit" <?=$locked?'disabled':''?>>Login →</button>
  </form>
</div>
</body>
</html>
