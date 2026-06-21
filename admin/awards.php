<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Awards & Nominations';
$pageSubtitle = 'Manage awards and recognitions.';

$msg = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $fields = ['title','organization','year','description','url','image1','image2','color','icon','sort_order'];
    $vals = [];
    foreach ($fields as $f) $vals[] = $f === 'sort_order' ? (int)($_POST[$f]??0) : trim($_POST[$f]??'');
    if ($action === 'add') {
        dbExec("INSERT INTO awards (title,organization,year,description,url,image1,image2,color,icon,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)", $vals);
        $msg = 'added';
    } elseif ($action === 'edit' && $id) {
        $vals[] = $id;
        getDB()->prepare("UPDATE awards SET title=?,organization=?,year=?,description=?,url=?,image1=?,image2=?,color=?,icon=?,sort_order=? WHERE id=?")->execute($vals);
        $msg = 'updated';
    } elseif ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM awards WHERE id=?")->execute([$id]);
        header('Location: awards.php?deleted=1'); exit;
    }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM awards WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM awards ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Award deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Award added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Award updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow?'✏️ Edit Award':'➕ Add New Award'?></div>
  <form method="POST" action="awards.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label for="award-title">Award Title</label>
        <input type="text" id="award-title" name="title" value="<?=h($editRow['title']??'')?>" required>
        <label for="award-org">Organization / Issuer</label>
        <input type="text" id="award-org" name="organization" value="<?=h($editRow['organization']??'')?>">
        <label for="award-year">Year</label>
        <input type="text" id="award-year" name="year" value="<?=h($editRow['year']??'')?>">
        <label for="award-url">URL (optional)</label>
        <input type="url" id="award-url" name="url" value="<?=h($editRow['url']??'')?>">
      </div>
      <div>
        <label for="award-img1">Image 1 path (e.g. img/award.jpg)</label>
        <input type="text" id="award-img1" name="image1" value="<?=h($editRow['image1']??'')?>">
        <label for="award-img2">Image 2 path (optional)</label>
        <input type="text" id="award-img2" name="image2" value="<?=h($editRow['image2']??'')?>">
        <label for="award-color">Color (cyan / violet / amber)</label>
        <input type="text" id="award-color" name="color" value="<?=h($editRow['color']??'cyan')?>">
        <label for="award-icon">Icon (Font Awesome class e.g. fa-trophy)</label>
        <input type="text" id="award-icon" name="icon" value="<?=h($editRow['icon']??'fa-trophy')?>">
        <label for="award-sort">Sort Order</label>
        <input type="number" id="award-sort" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <label for="award-desc">Description</label>
    <textarea id="award-desc" name="description" rows="3"><?=h($editRow['description']??'')?></textarea>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="awards.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All Awards (<?=count($list)?>)</div>
  <table>
    <thead><tr><th>#</th><th>Title</th><th>Organization</th><th>Year</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><strong style="color:#fff"><?=h($row['title'])?></strong></td>
      <td style="color:#8892a4"><?=h($row['organization'])?></td>
      <td style="color:#64748b"><?=h($row['year'])?></td>
      <td style="display:flex;gap:6px">
        <a href="awards.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px">Edit</a>
        <form method="POST" action="awards.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
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
