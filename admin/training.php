<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'Training & Certificates';
$pageSubtitle = 'Add, edit or delete training and certificate entries.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM training WHERE id=?")->execute([$id]);
        header('Location: training.php?deleted=1');
        exit;
    }

    $certResult = handleAdminUpload($_FILES['certificate_file'] ?? null, 'pdf', $editRow['certificate_file'] ?? '', 'cert', 'files/');

    if ($certResult['error']) { $msg = $certResult['error']; $msgType = 'error'; }
    else {
        $vals = [
            trim($_POST['icon']    ?? 'certificate'),
            trim($_POST['name']   ?? ''),
            trim($_POST['organizer'] ?? ''),
            trim($_POST['year']   ?? ''),
            $certResult['path'],
            trim($_POST['certificate_url'] ?? ''),
            (int)($_POST['sort_order'] ?? 0),
        ];
        if ($action === 'add') {
            dbExec("INSERT INTO training (icon,name,organizer,year,certificate_file,certificate_url,sort_order) VALUES (?,?,?,?,?,?,?)", $vals);
            $msg = 'Training added!';
        } elseif ($action === 'edit' && $id) {
            $vals[] = $id;
            getDB()->prepare("UPDATE training SET icon=?,name=?,organizer=?,year=?,certificate_file=?,certificate_url=?,sort_order=? WHERE id=?")->execute($vals);
            $msg = 'Training updated!';
        }
    }
}

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM training WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM training ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if ($msg): ?>
  <div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>"
    <?=h($msg)?>
  </div>
<?php endif; ?>

<div class="card" style="margin-bottom:10px;background:#0f1420;border-color:#2a3347;font-size:12px;color:#64748b">
  💡 <strong style="color:#c9d1e3">Icon examples:</strong> certificate, award, laptop, book-open, shield-alt, users, chart-bar, university, globe, leaf, bullhorn, hand-holding-usd
</div>

<div class="card">
  <div class="section-heading"><?=$editRow ? '✏️ Edit Training' : '➕ Add New Training / Certificate'?></div>
  <form method="POST" action="training.php?action=<?=$editRow ? 'edit&id=' . $id : 'add'?>" enctype="multipart/form-data">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Certificate / Training Name *</label>
        <input type="text" name="name" value="<?=h($editRow['name'] ?? '')?>" required placeholder="Training on Cooperative Management">
        <label>Organizer / Institution *</label>
        <input type="text" name="organizer" value="<?=h($editRow['organizer'] ?? '')?>" required placeholder="Ministry of Land Management">
        <label>Certificate URL <span style="color:#64748b">(external link, e.g. Coursera/Google credential)</span></label>
        <input type="url" name="certificate_url" value="<?=h($editRow['certificate_url'] ?? '')?>" placeholder="https://www.coursera.org/verify/...">
        <?php renderUploadField('Certificate PDF (optional)', 'certificate_file', $editRow['certificate_file'] ?? null, 'pdf'); ?>
      </div>
      <div>
        <label>Year / Period</label>
        <input type="text" name="year" value="<?=h($editRow['year'] ?? '')?>" placeholder="2019">
        <label>Icon (Font Awesome class name, without fa-)</label>
        <input type="text" name="icon" value="<?=h($editRow['icon'] ?? 'certificate')?>" placeholder="certificate">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order'] ?? '0')?>">
      </div>
    </div>
    <div style="margin-top:10px;background:#0f1420;border:1px solid #2a3347;border-radius:8px;padding:10px 14px;font-size:12px;color:#64748b">
      💡 <strong style="color:#c9d1e3">Certificate link priority:</strong> If both PDF File and URL are filled, the URL takes priority. Leave both blank to hide the "View Certificate" button.
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow ? 'Update' : 'Add'?></button>
      <?php if ($editRow): ?><a href="training.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Training & Certificates (<?=count($list)?>)</div>
  <?php if (!$list): ?>
    <p style="color:#64748b;font-size:13px">No entries yet.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Icon</th><th>Name</th><th>Organizer</th><th>Year</th><th>Cert</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><i class="fa fa-<?=h($row['icon'])?>" style="color:#22d3ee"></i> <span style="color:#64748b;font-size:11px"><?=h($row['icon'])?></span></td>
      <td><strong style="color:#fff"><?=h($row['name'])?></strong></td>
      <td style="color:#8892a4"><?=h($row['organizer'])?></td>
      <td style="color:#22d3ee;font-size:12px"><?=h($row['year'])?></td>
      <td>
        <?php if (!empty($row['certificate_file'])): ?>
          <a href="../<?=h($row['certificate_file'])?>" target="_blank" style="font-size:11px;color:#67e8f9">📄 View</a>
        <?php elseif (!empty($row['certificate_url'])): ?>
          <a href="<?=h($row['certificate_url'])?>" target="_blank" style="font-size:11px;color:#22d3ee">↗ Link</a>
        <?php else: ?>
          <span style="color:#64748b;font-size:11px">—</span>
        <?php endif; ?>
      </td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="training.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="training.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
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
