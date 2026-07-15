<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'Awards & Nominations';
$pageSubtitle = 'Manage awards and recognitions.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM awards WHERE id=?")->execute([$id]);
        header('Location: awards.php?deleted=1');
        exit;
    }

    // Handle file uploads
    $img1Result = handleAdminUpload($_FILES['image1'] ?? null, 'image', $editRow['image1'] ?? '', 'award', 'img/');
    $img2Result = handleAdminUpload($_FILES['image2'] ?? null, 'image', $editRow['image2'] ?? '', 'award2', 'img/');

    if ($img1Result['error']) { $msg = $img1Result['error']; $msgType = 'error'; }
    elseif ($img2Result['error']) { $msg = $img2Result['error']; $msgType = 'error'; }
    else {
        $vals = [
            trim($_POST['title'] ?? ''),
            trim($_POST['organization'] ?? ''),
            trim($_POST['year'] ?? ''),
            trim($_POST['description'] ?? ''),
            trim($_POST['url'] ?? ''),
            $img1Result['path'],
            $img2Result['path'],
            trim($_POST['color'] ?? 'cyan'),
            trim($_POST['icon'] ?? 'trophy'),
            (int)($_POST['sort_order'] ?? 0),
        ];
        if ($action === 'add') {
            dbExec("INSERT INTO awards (title,organization,year,description,url,image1,image2,color,icon,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)", $vals);
            $msg = 'Award added!';
        } elseif ($action === 'edit' && $id) {
            $vals[] = $id;
            getDB()->prepare("UPDATE awards SET title=?,organization=?,year=?,description=?,url=?,image1=?,image2=?,color=?,icon=?,sort_order=? WHERE id=?")->execute($vals);
            $msg = 'Award updated!';
        }
    }
}

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM awards WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM awards ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">✅ Award deleted.</div><?php endif; ?>
<?php if ($msg): ?>
  <div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>"><?=h($msg)?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div class="section-heading"><?=$editRow ? '✏️ Edit Award' : '➕ Add New Award'?></div>
  <form method="POST" action="awards.php?action=<?=$editRow ? 'edit&id=' . $id : 'add'?>" enctype="multipart/form-data">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Award Title *</label>
        <input type="text" name="title" value="<?=h($editRow['title'] ?? '')?>" required>
        <label>Organization / Issuer</label>
        <input type="text" name="organization" value="<?=h($editRow['organization'] ?? '')?>">
        <label>Year</label>
        <input type="text" name="year" value="<?=h($editRow['year'] ?? '')?>">
        <label>URL (optional)</label>
        <input type="url" name="url" value="<?=h($editRow['url'] ?? '')?>">
      </div>
      <div>
        <?php renderUploadField('Image 1', 'image1', $editRow['image1'] ?? null, 'image'); ?>
        <?php renderUploadField('Image 2 (optional)', 'image2', $editRow['image2'] ?? null, 'image'); ?>
        <label>Color (cyan / violet / amber)</label>
        <input type="text" name="color" value="<?=h($editRow['color'] ?? 'cyan')?>">
        <label>Icon (Font Awesome class without fa-, e.g. trophy)</label>
        <input type="text" name="icon" value="<?=h($editRow['icon'] ?? 'trophy')?>">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order'] ?? '0')?>">
      </div>
    </div>
    <label>Description</label>
    <textarea name="description" rows="3"><?=h($editRow['description'] ?? '')?></textarea>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow ? 'Update' : 'Add'?></button>
      <?php if ($editRow): ?><a href="awards.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Awards (<?=count($list)?>)</div>
  <?php if (!$list): ?>
    <p style="color:#64748b;font-size:13px">No entries yet.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Title</th><th>Organization</th><th>Year</th><th>Image</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><strong style="color:#fff"><?=h($row['title'])?></strong></td>
      <td style="color:#8892a4"><?=h($row['organization'])?></td>
      <td style="color:#64748b"><?=h($row['year'])?></td>
      <td><?=$row['image1'] ? '<img src="../' . h($row['image1']) . '" style="width:40px;height:30px;object-fit:cover;border-radius:4px;border:1px solid #1e2638" />' : '<span style="color:#64748b;font-size:11px">—</span>'?></td>
      <td style="display:flex;gap:6px">
        <a href="awards.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="awards.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
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
