<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'Edit Profile';
$pageSubtitle = 'Update your personal information and bio.';

$msg = ''; $msgType = 'success';
$profile = dbRow("SELECT * FROM profile LIMIT 1");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    // Handle file uploads
    $avatarResult = handleAdminUpload($_FILES['avatar'] ?? null, 'image', $profile['avatar'] ?? '', 'avatar', 'img/');
    $ogResult     = handleAdminUpload($_FILES['og_image'] ?? null, 'image', $profile['og_image'] ?? '', 'og', 'img/');
    $cvResult     = handleAdminUpload($_FILES['cv_file'] ?? null, 'pdf',   $profile['cv_file'] ?? '', 'cv', 'files/');

    if ($avatarResult['error']) { $msg = $avatarResult['error']; $msgType = 'error'; }
    elseif ($ogResult['error'])     { $msg = $ogResult['error'];     $msgType = 'error'; }
    elseif ($cvResult['error'])     { $msg = $cvResult['error'];     $msgType = 'error'; }
    else {
        $fields = ['full_name','title','bio','email','phone','location','born','company','company_url','role','facebook_url','tiktok_url','whatsapp_url','linkedin_url','youtube_url','cv_file','og_image','contact_email'];
        $vals = [];
        $sets = [];
        foreach ($fields as $f) {
            $sets[] = "`$f`=?";
            if ($f === 'avatar')      { $vals[] = $avatarResult['path']; continue; }
            if ($f === 'og_image')     { $vals[] = $ogResult['path'];    continue; }
            if ($f === 'cv_file')      { $vals[] = $cvResult['path'];    continue; }
            $vals[] = trim($_POST[$f] ?? '');
        }
        $vals[] = $profile['id'];
        getDB()->prepare("UPDATE profile SET " . implode(',', $sets) . " WHERE id=?")->execute($vals);
        $profile = dbRow("SELECT * FROM profile LIMIT 1");
        $msg = 'Profile updated successfully!';
    }
}
include __DIR__ . '/header.php';
?>

<?php if ($msg): ?>
  <div style="border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:18px;<?=$msgType === 'error' ? 'background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#f87171' : 'background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);color:#67e8f9'?>">
    <?=h($msg)?>
  </div>
<?php endif; ?>

<form method="POST" class="card" enctype="multipart/form-data">
  <?=csrfField()?>
  <div class="grid-2">
    <div>
      <label>Full Name</label>
      <input type="text" name="full_name" value="<?=h($profile['full_name'] ?? '')?>">
      <label>Title / Designation</label>
      <input type="text" name="title" value="<?=h($profile['title'] ?? '')?>">
      <label>Role (for Profile section)</label>
      <input type="text" name="role" value="<?=h($profile['role'] ?? '')?>">
      <label>Email</label>
      <input type="email" name="email" value="<?=h($profile['email'] ?? '')?>">
      <label>Phone</label>
      <input type="text" name="phone" value="<?=h($profile['phone'] ?? '')?>">
      <label>Location</label>
      <input type="text" name="location" value="<?=h($profile['location'] ?? '')?>">
      <label>Date of Birth / Born</label>
      <input type="text" name="born" value="<?=h($profile['born'] ?? '')?>">
    </div>
    <div>
      <label>Company Name</label>
      <input type="text" name="company" value="<?=h($profile['company'] ?? '')?>">
      <label>Company URL</label>
      <input type="url" name="company_url" value="<?=h($profile['company_url'] ?? '')?>">
      <label>Facebook URL</label>
      <input type="url" name="facebook_url" value="<?=h($profile['facebook_url'] ?? '')?>">
      <label>LinkedIn URL</label>
      <input type="url" name="linkedin_url" value="<?=h($profile['linkedin_url'] ?? '')?>">
      <label>YouTube URL</label>
      <input type="url" name="youtube_url" value="<?=h($profile['youtube_url'] ?? '')?>">
      <label>TikTok URL</label>
      <input type="url" name="tiktok_url" value="<?=h($profile['tiktok_url'] ?? '')?>">
      <label>WhatsApp URL</label>
      <input type="url" name="whatsapp_url" value="<?=h($profile['whatsapp_url'] ?? '')?>">
      <label>Admin Email for Contact Notifications <span style="color:#64748b;font-weight:400">(new messages will be emailed here)</span></label>
      <input type="email" name="contact_email" value="<?=h($profile['contact_email'] ?? '')?>" placeholder="aakashpame@gmail.com">

      <?php renderUploadField('Profile Photo / Avatar', 'avatar', $profile['avatar'] ?? null, 'image'); ?>
      <?php renderUploadField('CV / Resume PDF', 'cv_file', $profile['cv_file'] ?? null, 'pdf'); ?>
      <?php renderUploadField('OG Image (LinkedIn/Twitter share card — 1200x630px recommended)', 'og_image', $profile['og_image'] ?? null, 'image'); ?>
    </div>
  </div>
  <label>Bio / About Text</label>
  <textarea name="bio" rows="5"><?=h($profile['bio'] ?? '')?></textarea>
  <div style="margin-top:18px"><button class="btn btn-primary" type="submit">💾 Save Profile</button></div>
</form>
<?php include __DIR__ . '/footer.php'; ?>
