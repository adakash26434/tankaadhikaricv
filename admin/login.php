<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/totp.php';

if (isAdmin()) { header('Location: index.php'); exit; }

$error = '';
$locked = false;
$step = $_SESSION['login_step'] ?? 'password';  // 'password' | 'setup' | 'otp'
$totpSecret = totpGetSecret();

// Clear pending state on GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['password_verified'], $_SESSION['totp_pending'], $_SESSION['login_step']);
    $step = 'password';
}

// Session timeout notice
if (!empty($_SESSION['_timed_out'])) {
    $mins = (int)(SESSION_TIMEOUT_SECS / 60);
    $error = "You were automatically logged out after {$mins} minutes of inactivity.";
    unset($_SESSION['_timed_out']);
}

// ── PHASE 2: TOTP SETUP (first time - no TOTP_SECRET) ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_verify'])) {
    $code = $_POST['setup_code'] ?? '';
    $secret = $_SESSION['totp_setup_secret'] ?? '';
    if ($secret && totpVerifyCode($secret, $code)) {
        totpSaveSecret($secret);
        unset($_SESSION['totp_setup_secret'], $_SESSION['login_step']);
        $_SESSION[ADMIN_SESSION_KEY] = true;
        $_SESSION['last_activity'] = time();
        session_write_close();
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid code. Please try again.';
        $step = 'setup';
    }
}

// ── PHASE 2: OTP VERIFICATION (TOTP_SECRET exists) ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp_code'])) {
    $code = $_POST['otp_code'] ?? '';
    if ($totpSecret && totpVerifyCode($totpSecret, $code)) {
        unset($_SESSION['password_verified'], $_SESSION['login_step']);
        $_SESSION[ADMIN_SESSION_KEY] = true;
        $_SESSION['last_activity'] = time();
        unset($_SESSION['login_attempts'], $_SESSION['login_last_attempt'], $_SESSION['_timed_out']);
        session_write_close();
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid 2FA code. Please try again.';
        $step = 'otp';
    }
}

// ── PHASE 1: PASSWORD VERIFICATION ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $attempts    = (int)($_SESSION['login_attempts'] ?? 0);
    $lastAttempt = (int)($_SESSION['login_last_attempt'] ?? 0);
    $lockoutSecs = 300;

    if ($attempts >= 5 && (time() - $lastAttempt) < $lockoutSecs) {
        $remaining = $lockoutSecs - (time() - $lastAttempt);
        $error = 'Too many failed attempts. Please wait ' . ceil($remaining / 60) . ' minute(s).';
        $locked = true;
    } else {
        $input = $_POST['password'] ?? '';
        $hashFile   = __DIR__ . '/password.php';
        $storedHash = null;
        if (is_file($hashFile)) {
            $hashData = include $hashFile;
            if (is_string($hashData) && strlen($hashData) > 20) {
                $storedHash = $hashData;
            }
        }

        $valid = false;
        if ($storedHash
            && (str_starts_with($storedHash, '$2y$')
                || str_starts_with($storedHash, '$2a$')
                || str_starts_with($storedHash, '$argon'))
        ) {
            $valid = password_verify($input, $storedHash);
        } else {
            $valid = hash_equals(hash('sha256', ADMIN_PASSWORD), hash('sha256', $input));
        }

        if ($valid) {
            unset($_SESSION['login_attempts'], $_SESSION['login_last_attempt'], $_SESSION['_timed_out']);
            session_write_close();

            if ($totpSecret) {
                // 2FA enabled — go to OTP step
                $_SESSION['password_verified'] = true;
                $_SESSION['login_step'] = 'otp';
                header('Location: login.php');
                exit;
            } else {
                // No 2FA — generate secret + show setup
                $secret = totpGenerateSecret();
                $_SESSION['totp_setup_secret'] = $secret;
                $_SESSION['login_step'] = 'setup';
                header('Location: login.php');
                exit;
            }
        }

        $_SESSION['login_attempts']     = $attempts + 1;
        $_SESSION['login_last_attempt'] = time();
        $attemptsLeft = max(0, 5 - ($attempts + 1));
        $error = 'Incorrect password.' . ($attemptsLeft > 0 ? " $attemptsLeft attempt(s) remaining." : ' Account locked for 5 minutes.');
    }
}

// Retrieve session state for this render
if (isset($_SESSION['login_step'])) {
    $step = $_SESSION['login_step'];
}
$setupSecret = $_SESSION['totp_setup_secret'] ?? null;
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
.error{background:#7f1d1d;border:2px solid #ef4444;color:#fca5a5;border-radius:8px;padding:14px 16px;font-size:14px;margin-bottom:16px;font-weight:700;text-align:center;width:100%}
.icon{width:48px;height:48px;background:rgba(34,211,238,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;font-size:22px}
</style>
</head>
<body>
<div class="box">
  <div class="icon">&#128272;</div>
  <h1>Admin Panel</h1>
  <p>Tanka Prasad Adhikari — Portfolio Management</p>

  <?php if($error): ?><div class="error" role="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>

  <?php if ($step === 'password'): ?>
  <!-- STEP 1: Password -->
  <form method="POST" autocomplete="off">
    <label for="password">Admin Password</label>
    <input type="password" id="password" name="password" placeholder="Enter password" autofocus required />
    <button class="btn" type="submit">Continue &rarr;</button>
  </form>
  <p style="margin-top:16px;font-size:11px;color:#334155;text-align:center">After password, you'll be asked for a 2FA code from Google Authenticator.</p>

  <?php elseif ($step === 'setup'): ?>
  <!-- STEP 2a: 2FA Setup (first time — no secret yet) -->
  <div style="text-align:center;margin-bottom:16px">
    <p style="font-size:13px;color:#67e8f9;font-weight:700;margin-bottom:12px">&#128272; Set Up Two-Factor Authentication</p>
    <p style="font-size:11px;color:#64748b;margin-bottom:16px">Scan this QR code with Google Authenticator app, then enter the 6-digit code to confirm.</p>
    <?php
    $secret = $setupSecret;
    $otpUri = totpGetOtpAuthUri($secret, 'Tanka Portfolio', 'admin');
    $qrUrl = totpGetQrCodeUrl($otpUri, 200);
    ?>
    <img src="<?=htmlspecialchars($qrUrl)?>" alt="QR Code" style="border-radius:12px;border:2px solid #1e2638;margin-bottom:10px" />
    <p style="font-size:10px;color:#64748b;margin-bottom:16px">Manual secret code:<br><code style="background:#0f1420;padding:4px 8px;border-radius:6px;color:#22d3ee;letter-spacing:2px;font-size:11px"><?=htmlspecialchars($secret)?></code></p>
  </div>
  <form method="POST" autocomplete="off">
    <label for="setup_code">2FA Code (from Google Authenticator)</label>
    <input type="text" id="setup_code" name="setup_code" placeholder="6-digit code" maxlength="6" pattern="\d{6}" autofocus required style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:10px 14px;font-size:18px;text-align:center;letter-spacing:4px;outline:none" />
    <button class="btn" type="submit" name="setup_verify" value="1">Verify &amp; Enable 2FA</button>
  </form>
  <p style="margin-top:12px;font-size:11px;color:#334155;text-align:center">Save your secret code somewhere safe — you'll need it if you lose your phone.</p>

  <?php elseif ($step === 'otp'): ?>
  <!-- STEP 2b: 2FA Code Entry (secret exists) -->
  <div style="text-align:center;margin-bottom:16px">
    <p style="font-size:13px;color:#67e8f9;font-weight:700;margin-bottom:6px">&#128272; Two-Factor Authentication</p>
    <p style="font-size:11px;color:#64748b">Enter the 6-digit code from your Google Authenticator app.</p>
  </div>
  <form method="POST" autocomplete="off">
    <label for="otp_code">2FA Code</label>
    <input type="text" id="otp_code" name="otp_code" placeholder="6-digit code" maxlength="6" pattern="\d{6}" autofocus required style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:10px 14px;font-size:18px;text-align:center;letter-spacing:4px;outline:none" />
    <button class="btn" type="submit">Login &rarr;</button>
  </form>
  <p style="margin-top:12px;font-size:11px;color:#334155;text-align:center"><a href="login.php" style="color:#22d3ee">&#8592; Use a different account</a></p>
  <?php endif; ?>

</div>
</body>
</html>