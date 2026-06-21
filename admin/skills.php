<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Skills';
$pageSubtitle = 'Manage skill categories and levels.';

$msg = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if ($action === 'add') {
        dbExec("INSERT INTO skills (category,name,level,sort_order) VALUES (?,?,?,?)", [
            trim($_POST['category']), trim($_POST['name']), (int)($_POST['level']??80), (int)($_POST['sort_order']??0)
        ]);
        $msg = 'added';
    } elseif ($action === 'edit' && $id) {
        getDB()->prepare("UPDATE skills SET category=?,name=?,level=?,sort_order=? WHERE id=?")->execute([
            trim($_POST['category']), trim($_POST['name']), (int)($_POST['level']??80), (int)($_POST['sort_order']??0), $id
        ]);
        $msg = 'updated';
    } elseif ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM skills WHERE id=?")->execute([$id]);
        header('Location: skills.php?deleted=1'); exit;
    }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM skills WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM skills ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Skill deleted.</div><?php endif; ?>
<?php if($msg): ?><div class="alert-success">✅ Skill <?=$msg?>!</div><?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow?'✏️ Edit Skill':'➕ Add New Skill'?></div>
  <form method="POST" action="skills.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Category (Professional / Code / Software / Language)</label>
        <input type="text" name="category" value="<?=h($editRow['category']??'')?>" required>
        <label>Skill Name</label>
        <input type="text" name="name" value="<?=h($editRow['name']??'')?>" required>
      </div>
      <div>
        <label>Level (0–100)</label>
        <input type="number" name="level" min="0" max="100" value="<?=h($editRow['level']??'80')?>">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="skills.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <table>
    <thead><tr><th>Category</th><th>Skill</th><th>Level</th><th>#</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td><span class="badge"><?=h($row['category'])?></span></td>
      <td style="color:#fff"><?=h($row['name'])?></td>
      <td>
        <div style="display:flex;align-items:center;gap:8px">
          <div style="flex:1;background:#1e2638;border-radius:4px;height:6px;width:80px">
            <div style="background:#22d3ee;width:<?=$row['level']?>%;height:100%;border-radius:4px"></div>
          </div>
          <span style="color:#64748b;font-size:12px"><?=$row['level']?>%</span>
        </div>
      </td>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td style="display:flex;gap:6px">
        <a href="skills.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px">Edit</a>
        <form method="POST" action="skills.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
          <?=csrfField()?>
          <button class="btn btn-danger" style="font-size:11px;padding:4px 10px" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
