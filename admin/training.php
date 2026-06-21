<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Training & Certificates';
$pageSubtitle = 'Add, edit or delete training and certificate entries.';

$msg = ''; $action = $_GET['action'] ?? ''; $id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf();
  if ($action === 'add') {
    dbExec("INSERT INTO training (icon,name,organizer,year,certificate_file,certificate_url,sort_order) VALUES (?,?,?,?,?,?,?)", [
      trim($_POST['icon']??'certificate'), trim($_POST['name']), trim($_POST['organizer']),
      trim($_POST['year']), trim($_POST['certificate_file']??''), trim($_POST['certificate_url']??''),
      (int)($_POST['sort_order']??0)
    ]);
    $msg = 'added';
  } elseif ($action === 'edit' && $id) {
    getDB()->prepare("UPDATE training SET icon=?,name=?,organizer=?,year=?,certificate_file=?,certificate_url=?,sort_order=? WHERE id=?")->execute([
      trim($_POST['icon']??'certificate'), trim($_POST['name']), trim($_POST['organizer']),
      trim($_POST['year']), trim($_POST['certificate_file']??''), trim($_POST['certificate_url']??''),
      (int)($_POST['sort_order']??0), $id
    ]);
    $msg = 'updated';
  } elseif ($action === 'delete' && $id) {
    getDB()->prepare("DELETE FROM training WHERE id=?")->execute([$id]);
    header('Location: training.php?deleted=1'); exit;
  }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM training WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM training ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Training added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Training updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:10px;background:#0f1420;border-color:#2a3347;font-size:12px;color:#64748b">
  💡 <strong style="color:#c9d1e3">Icon examples:</strong> certificate, award, laptop, book-open, shield-alt, users, chart-bar, university, globe, leaf, bullhorn, hand-holding-usd
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow?'✏️ Edit Training':'➕ Add New Training / Certificate'?></div>
  <form method="POST" action="training.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Certificate / Training Name *</label>
        <input type="text" name="name" value="<?=h($editRow['name']??'')?>" required placeholder="Training on Cooperative Management">
        <label>Organizer / Institution *</label>
        <input type="text" name="organizer" value="<?=h($editRow['organizer']??'')?>" required placeholder="Ministry of Land Management">
        <label>Certificate File Path <span style="color:#64748b">(upload to files/ folder, then paste path)</span></label>
        <input type="text" name="certificate_file" value="<?=h($editRow['certificate_file']??'')?>" placeholder="files/cert-cybersecurity.pdf">
        <label>Certificate URL <span style="color:#64748b">(external link, e.g. Coursera/Google credential)</span></label>
        <input type="url" name="certificate_url" value="<?=h($editRow['certificate_url']??'')?>" placeholder="https://www.coursera.org/verify/...">
      </div>
      <div>
        <label>Year / Period</label>
        <input type="text" name="year" value="<?=h($editRow['year']??'')?>" placeholder="2019">
        <label>Icon (Font Awesome class name, without fa-)</label>
        <input type="text" name="icon" value="<?=h($editRow['icon']??'certificate')?>" placeholder="certificate">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <div style="margin-top:10px;background:#0f1420;border:1px solid #2a3347;border-radius:8px;padding:10px 14px;font-size:12px;color:#64748b">
      💡 <strong style="color:#c9d1e3">Certificate link priority:</strong> If both File Path and URL are filled, the URL takes priority. Leave both blank to hide the "View Certificate" button.
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="training.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All Training & Certificates (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No entries yet. Add one above.</p><?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Icon</th><th>Name</th><th>Organizer</th><th>Year</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><i class="fa fa-<?=h($row['icon'])?>" style="color:#22d3ee"></i> <span style="color:#64748b;font-size:11px"><?=h($row['icon'])?></span></td>
      <td><strong style="color:#fff"><?=h($row['name'])?></strong></td>
      <td style="color:#8892a4"><?=h($row['organizer'])?></td>
      <td style="color:#22d3ee;font-size:12px"><?=h($row['year'])?></td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="training.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px">Edit</a>
        <form method="POST" action="training.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
          <?=csrfField()?>
          <button class="btn btn-danger" style="font-size:11px;padding:4px 10px" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
