<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Edit Profile';
$pageSubtitle = 'Update your personal information and bio.';

$msg = '';
$profile = dbRow("SELECT * FROM profile LIMIT 1");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $fields = ['full_name','title','bio','email','phone','location','born','company','company_url','role','facebook_url','tiktok_url','whatsapp_url','linkedin_url','youtube_url','cv_file'];
    $vals = [];
    $sets = [];
    foreach ($fields as $f) {
        $sets[] = "`$f`=?";
        $vals[] = trim($_POST[$f] ?? '');
    }
    $vals[] = $profile['id'];
    getDB()->prepare("UPDATE profile SET ".implode(',',$sets)." WHERE id=?")->execute($vals);
    $profile = dbRow("SELECT * FROM profile LIMIT 1");
    $msg = 'success';
}
include __DIR__ . '/header.php';
?>
<?php if($msg==='success'): ?><div class="alert-success">✅ Profile updated successfully!</div><?php endif; ?>
<form method="POST" class="card">
  <?=csrfField()?>
  <div class="grid-2">
    <div>
      <label for="pf-name">Full Name</label>
      <input type="text" id="pf-name" name="full_name" value="<?=h($profile['full_name']??'')?>">
      <label for="pf-title">Title / Designation</label>
      <input type="text" id="pf-title" name="title" value="<?=h($profile['title']??'')?>">
      <label for="pf-role">Role (for Profile section)</label>
      <input type="text" id="pf-role" name="role" value="<?=h($profile['role']??'')?>">
      <label for="pf-email">Email</label>
      <input type="email" id="pf-email" name="email" value="<?=h($profile['email']??'')?>">
      <label for="pf-phone">Phone</label>
      <input type="text" id="pf-phone" name="phone" value="<?=h($profile['phone']??'')?>">
      <label for="pf-loc">Location</label>
      <input type="text" id="pf-loc" name="location" value="<?=h($profile['location']??'')?>">
      <label for="pf-born">Date of Birth / Born</label>
      <input type="text" id="pf-born" name="born" value="<?=h($profile['born']??'')?>">
    </div>
    <div>
      <label for="pf-company">Company Name</label>
      <input type="text" id="pf-company" name="company" value="<?=h($profile['company']??'')?>">
      <label for="pf-compurl">Company URL</label>
      <input type="url" id="pf-compurl" name="company_url" value="<?=h($profile['company_url']??'')?>">
      <label for="pf-fb">Facebook URL</label>
      <input type="url" id="pf-fb" name="facebook_url" value="<?=h($profile['facebook_url']??'')?>">
      <label for="pf-tt">TikTok URL</label>
      <input type="url" id="pf-tt" name="tiktok_url" value="<?=h($profile['tiktok_url']??'')?>">
      <label for="pf-wa">WhatsApp URL</label>
      <input type="url" id="pf-wa" name="whatsapp_url" value="<?=h($profile['whatsapp_url']??'')?>">
      <label for="pf-li">LinkedIn URL</label>
      <input type="url" id="pf-li" name="linkedin_url" value="<?=h($profile['linkedin_url']??'')?>">
      <label for="pf-yt">YouTube URL</label>
      <input type="url" id="pf-yt" name="youtube_url" value="<?=h($profile['youtube_url']??'')?>">
      <label for="pf-cv">CV File path (e.g. files/cv.pdf)</label>
      <input type="text" id="pf-cv" name="cv_file" value="<?=h($profile['cv_file']??'')?>">
    </div>
  </div>
  <label for="pf-bio">Bio / About Text</label>
  <textarea id="pf-bio" name="bio" rows="5"><?=h($profile['bio']??'')?></textarea>
  <div style="margin-top:18px"><button class="btn btn-primary" type="submit">💾 Save Profile</button></div>
</form>
<?php include __DIR__ . '/footer.php'; ?>
