<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Services (About Section)';
$pageSubtitle = 'Manage the service cards shown in the About section of the portfolio.';

$msg = ''; $action = $_GET['action'] ?? ''; $id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf();
  if ($action === 'add') {
    dbExec("INSERT INTO services_about (icon,name,description,sort_order) VALUES (?,?,?,?)", [
      trim($_POST['icon']??'globe'), trim($_POST['name']), trim($_POST['description']),
      (int)($_POST['sort_order']??0)
    ]);
    $msg = 'added';
  } elseif ($action === 'edit' && $id) {
    getDB()->prepare("UPDATE services_about SET icon=?,name=?,description=?,sort_order=? WHERE id=?")->execute([
      trim($_POST['icon']??'globe'), trim($_POST['name']), trim($_POST['description']),
      (int)($_POST['sort_order']??0), $id
    ]);
    $msg = 'updated';
  } elseif ($action === 'delete' && $id) {
    getDB()->prepare("DELETE FROM services_about WHERE id=?")->execute([$id]);
    header('Location: services_about.php?deleted=1'); exit;
  }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM services_about WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM services_about ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Service deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Service added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Service updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:10px;background:#0f1420;border-color:#2a3347;font-size:12px;color:#64748b">
  💡 <strong style="color:#c9d1e3">Icon examples:</strong> globe, envelope, credit-card, code, database, chart-line, robot, chalkboard-teacher, server, mobile-alt, shield-alt, cogs, wifi, cloud
</div>

<div class="card">
  <div class="section-heading"><?=$editRow?'✏️ Edit Service':'➕ Add New Service Card'?></div>
  <form method="POST" action="services_about.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-3">
      <div>
        <label>Service Name *</label>
        <input type="text" name="name" value="<?=h($editRow['name']??'')?>" required placeholder="Web Development">
      </div>
      <div>
        <label>Icon (Font Awesome, without fa-)</label>
        <input type="text" name="icon" value="<?=h($editRow['icon']??'globe')?>" placeholder="globe">
      </div>
      <div>
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <label>Short Description (shown under the icon)</label>
    <input type="text" name="description" value="<?=h($editRow['description']??'')?>" placeholder="Advanced UI/UX focused development">
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="services_about.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Services (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No services yet. Add one above.</p><?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px">
    <?php foreach($list as $row): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-radius:10px;padding:16px;text-align:center">
      <i class="fa fa-<?=h($row['icon'])?>" style="color:#22d3ee;font-size:22px;margin-bottom:8px;display:block"></i>
      <div style="font-size:12px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px"><?=h($row['name'])?></div>
      <div style="font-size:11px;color:#64748b;margin-bottom:10px"><?=h($row['description'])?></div>
      <div style="display:flex;gap:6px;justify-content:center">
        <a href="services_about.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="services_about.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
          <?=csrfField()?>
          <button class="btn btn-danger btn-sm" type="submit">✕</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
