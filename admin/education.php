<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Education';
$pageSubtitle = 'Add, edit or delete education entries.';

$msg = ''; $action = $_GET['action'] ?? ''; $id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if ($action === 'add') {
        dbExec("INSERT INTO education (degree_code,degree_name,institution,period,sort_order) VALUES (?,?,?,?,?)", [
            trim($_POST['degree_code']), trim($_POST['degree_name']), trim($_POST['institution']),
            trim($_POST['period']), (int)($_POST['sort_order']??0)
        ]);
        $msg = 'added';
    } elseif ($action === 'edit' && $id) {
        getDB()->prepare("UPDATE education SET degree_code=?,degree_name=?,institution=?,period=?,sort_order=? WHERE id=?")->execute([
            trim($_POST['degree_code']), trim($_POST['degree_name']), trim($_POST['institution']),
            trim($_POST['period']), (int)($_POST['sort_order']??0), $id
        ]);
        $msg = 'updated';
    } elseif ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM education WHERE id=?")->execute([$id]);
        header('Location: education.php?deleted=1'); exit;
    }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM education WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM education ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Education added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Education updated!</div><?php endif; ?>

<div class="card">
  <div class="section-heading"><?=$editRow?'✏️ Edit Education':'➕ Add New Education'?></div>
  <form method="POST" action="education.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label for="edu-code">Degree Code (e.g. MBS, BBS, SLC)</label>
        <input type="text" id="edu-code" name="degree_code" value="<?=h($editRow['degree_code']??'')?>" required placeholder="MBS">
        <label for="edu-name">Degree / Program Name</label>
        <input type="text" id="edu-name" name="degree_name" value="<?=h($editRow['degree_name']??'')?>" required placeholder="Master of Business Studies">
      </div>
      <div>
        <label for="edu-inst">Institution / University</label>
        <input type="text" id="edu-inst" name="institution" value="<?=h($editRow['institution']??'')?>" required placeholder="Pokhara University">
        <label for="edu-period">Period (e.g. 2013 – 2015)</label>
        <input type="text" id="edu-period" name="period" value="<?=h($editRow['period']??'')?>" required placeholder="2013 – 2015">
        <label for="edu-sort">Sort Order (lower = shown first)</label>
        <input type="number" id="edu-sort" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="education.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Education (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No entries yet. Add one above.</p><?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Code</th><th>Degree / Program</th><th>Institution</th><th>Period</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><span style="background:rgba(34,211,238,.1);color:#22d3ee;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700"><?=h($row['degree_code'])?></span></td>
      <td><strong style="color:#fff"><?=h($row['degree_name'])?></strong></td>
      <td style="color:#8892a4"><?=h($row['institution'])?></td>
      <td style="color:#64748b;font-size:12px"><?=h($row['period'])?></td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="education.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="education.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
          <?=csrfField()?>
          <button class="btn btn-danger btn-sm" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
