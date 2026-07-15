<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle    = 'Change Admin Password';
$pageSubtitle = 'Upgrade to a secure hashed password.';

$msg     = '';
$msgType = 'success';

// Get current hash from file if it exists
$hashFile   = __DIR__ . '/password.php';
$usingBcrypt = false;
if (is_file($hashFile)) {
    $h = include $hashFile;
    if (is_string($h) && str_starts_with($h, '$2y$')) {
        $usingBcrypt = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $currentPw  = $_POST['current_password']  ?? '';
    $newPw      = $_POST['new_password']       ?? '';
    $confirmPw  = $_POST['confirm_password']   ?? '';

    // Verify current password
    $valid = false;
    if ($usingBcrypt) {
        $stored = include $hashFile;
        $valid  = password_verify($currentPw, $stored);
    } else {
        $valid = hash_equals(hash('sha256', ADMIN_PASSWORD), hash('sha256', $currentPw));
    }

    if (!$valid) {
        $msg = 'Current password is incorrect.';
        $msgType = 'error';
    } elseif (strlen($newPw) < 8) {
        $msg = 'New password must be at least 8 characters.';
        $msgType = 'error';
    } elseif ($newPw !== $confirmPw) {
        $msg = 'New passwords do not match.';
        $msgType = 'error';
    } else {
        $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
        // Write hash to password.php (web-blocked by .htaccess)
        $written = file_put_contents(
            $hashFile,
            "<?php\nreturn " . var_export($hash, true) . ";\n"
        );
        if ($written !== false) {
            $usingBcrypt = true;
            $msg = 'Password changed successfully! Your password is now stored as a secure bcrypt hash.';
        } else {
            $msg = 'Could not write password file. Check folder permissions on admin/.';
            $msgType = 'error';
        }
    }
}

include __DIR__ . '/header.php';
?>

<?php if($msg): ?>
<div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>"><?=htmlspecialchars($msg)?></div>
<?php endif; ?>

<div style="background:rgba(<?=$usingBcrypt?'34,211,238':'250,204,21'?>,.04);border:1px solid rgba(<?=$usingBcrypt?'34,211,238':'250,204,21'?>,.2);border-radius:10px;padding:14px 18px;font-size:13px;margin-bottom:20px;color:<?=$usingBcrypt?'#67e8f9':'#fde047'?>">
  <?php if($usingBcrypt): ?>
  🔒 <strong>Secure:</strong> Your password is stored as a bcrypt hash (cost 12). Excellent!
  <?php else: ?>
  ⚠️ <strong>Action Required:</strong> Your password is stored as plain-text in <code>superadmin.php</code>. Use this form to upgrade to a secure bcrypt hash.
  <?php endif; ?>
</div>

<div class="card" style="max-width:480px">
  <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:18px">🔑 Change Admin Password</div>
  <form method="POST">
    <?=csrfField()?>
    <label for="cp-cur">Current Password</label>
    <input type="password" id="cp-cur" name="current_password" required autocomplete="current-password" placeholder="Your current password">

    <label for="cp-new" style="margin-top:14px">New Password <span style="color:#64748b;font-weight:400">(min 8 characters)</span></label>
    <input type="password" id="cp-new" name="new_password" required autocomplete="new-password" placeholder="New secure password" minlength="8">

    <label for="cp-confirm" style="margin-top:14px">Confirm New Password</label>
    <input type="password" id="cp-confirm" name="confirm_password" required autocomplete="new-password" placeholder="Repeat new password" minlength="8">

    <div style="margin-top:20px">
      <button class="btn btn-primary" type="submit">🔐 Change Password</button>
    </div>
  </form>
</div>

<div class="card" style="margin-top:16px;background:rgba(0,0,0,.2)">
  <div style="font-size:12px;font-weight:700;color:#8892a4;margin-bottom:8px">ℹ️ How it works</div>
  <ul style="font-size:12px;color:#64748b;line-height:1.8;padding-left:16px">
    <li>Your new password is hashed with <strong style="color:#c9d1e3">bcrypt (cost 12)</strong> — industry standard</li>
    <li>The hash is saved to <code style="background:#0f1420;padding:1px 5px;border-radius:3px;color:#22d3ee">admin/password.php</code> (web-blocked)</li>
    <li>The old <code style="background:#0f1420;padding:1px 5px;border-radius:3px;color:#22d3ee">superadmin.php</code> file is kept as backup but no longer used once you change password here</li>
    <li>If you forget your password, delete <code style="background:#0f1420;padding:1px 5px;border-radius:3px;color:#22d3ee">admin/password.php</code> via cPanel File Manager and the original <code style="background:#0f1420;padding:1px 5px;border-radius:3px;color:#22d3ee">superadmin.php</code> password becomes active again</li>
  </ul>
</div>

<?php include __DIR__ . '/footer.php'; ?>
