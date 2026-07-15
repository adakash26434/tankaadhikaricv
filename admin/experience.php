<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Work Experience';
$pageSubtitle = 'Add, edit or delete work experience entries.';

$msg = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if ($action === 'add') {
        dbExec("INSERT INTO experience (company,role,period,description,color,sort_order) VALUES (?,?,?,?,?,?)", [
            trim($_POST['company']), trim($_POST['role']), trim($_POST['period']),
            trim($_POST['description']), trim($_POST['color']??'cyan'), (int)($_POST['sort_order']??0)
        ]);
        $msg = 'added';
    } elseif ($action === 'edit' && $id) {
        getDB()->prepare("UPDATE experience SET company=?,role=?,period=?,description=?,color=?,sort_order=? WHERE id=?")->execute([
            trim($_POST['company']), trim($_POST['role']), trim($_POST['period']),
            trim($_POST['description']), trim($_POST['color']??'cyan'), (int)($_POST['sort_order']??0), $id
        ]);
        $msg = 'updated';
    } elseif ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM experience WHERE id=?")->execute([$id]);
        header('Location: experience.php?deleted=1'); exit;
    }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM experience WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM experience ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Experience added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Experience updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div class="section-heading"><?=$editRow?'✏️ Edit Experience':'➕ Add New Experience'?></div>
  <form method="POST" action="experience.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Company / Organization</label>
        <input type="text" name="company" value="<?=h($editRow['company']??'')?>" required>
        <label>Role / Position</label>
        <input type="text" name="role" value="<?=h($editRow['role']??'')?>" required>
        <label>Period (e.g. Jun 2017 – Present)</label>
        <input type="text" name="period" value="<?=h($editRow['period']??'')?>" required>
      </div>
      <div>
        <label>Sort Order (lower = first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
        <label>Color (cyan / violet / amber)</label>
        <input type="text" name="color" value="<?=h($editRow['color']??'cyan')?>">
      </div>
    </div>
    <label>Description</label>
    <textarea name="description" rows="4"><?=h($editRow['description']??'')?></textarea>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="experience.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Experience (<?=count($list)?>)</div>
  <table>
    <thead><tr><th>#</th><th>Company</th><th>Role</th><th>Period</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><strong style="color:#fff"><?=h($row['company'])?></strong></td>
      <td style="color:#8892a4"><?=h($row['role'])?></td>
      <td style="color:#64748b;font-size:12px"><?=h($row['period'])?></td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="experience.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="experience.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
          <?=csrfField()?>
          <button class="btn btn-danger btn-sm" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
