<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Interests';
$pageSubtitle = 'Add, edit or delete interest items shown in the Interests section.';

$msg = ''; $action = $_GET['action'] ?? ''; $id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf();
  if ($action === 'add') {
    dbExec("INSERT INTO interests (icon,name,sort_order) VALUES (?,?,?)", [
      trim($_POST['icon']??'heart'), trim($_POST['name']), (int)($_POST['sort_order']??0)
    ]);
    $msg = 'added';
  } elseif ($action === 'edit' && $id) {
    getDB()->prepare("UPDATE interests SET icon=?,name=?,sort_order=? WHERE id=?")->execute([
      trim($_POST['icon']??'heart'), trim($_POST['name']), (int)($_POST['sort_order']??0), $id
    ]);
    $msg = 'updated';
  } elseif ($action === 'delete' && $id) {
    getDB()->prepare("DELETE FROM interests WHERE id=?")->execute([$id]);
    header('Location: interests.php?deleted=1'); exit;
  }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM interests WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM interests ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Interest added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Interest updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:10px;background:#0f1420;border-color:#2a3347;font-size:12px;color:#64748b">
  💡 <strong style="color:#c9d1e3">Icon examples:</strong> laptop-code, chart-line, users, globe, book, microphone, plane, mountain, music, camera, heart, star, football-ball, paint-brush, tree, coffee
</div>

<div class="card">
  <div class="section-heading"><?=$editRow?'✏️ Edit Interest':'➕ Add New Interest'?></div>
  <form method="POST" action="interests.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-3">
      <div>
        <label>Interest Name *</label>
        <input type="text" name="name" value="<?=h($editRow['name']??'')?>" required placeholder="Digital Innovation">
      </div>
      <div>
        <label>Icon (Font Awesome, without fa-)</label>
        <input type="text" name="icon" value="<?=h($editRow['icon']??'heart')?>" placeholder="laptop-code">
      </div>
      <div>
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="interests.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Interests (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No entries yet. Add one above.</p><?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-bottom:16px">
    <?php foreach($list as $row): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-radius:10px;padding:14px;display:flex;align-items:center;gap:10px">
      <i class="fa fa-<?=h($row['icon'])?>" style="color:#22d3ee;font-size:18px;width:20px;text-align:center"></i>
      <div style="flex:1">
        <div style="font-size:13px;font-weight:600;color:#fff"><?=h($row['name'])?></div>
        <div style="font-size:11px;color:#64748b">order: <?=$row['sort_order']?></div>
      </div>
      <div style="display:flex;gap:4px">
        <a href="interests.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:10px;padding:3px 8px">Edit</a>
        <form method="POST" action="interests.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
          <?=csrfField()?>
          <button class="btn btn-danger" style="font-size:10px;padding:3px 8px" type="submit">✕</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
