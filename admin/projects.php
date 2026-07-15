<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'Projects';
$pageSubtitle = 'Manage portfolio projects.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
        header('Location: projects.php?deleted=1');
        exit;
    }

    $img1Result = handleAdminUpload($_FILES['image1'] ?? null, 'image', $editRow['image1'] ?? '', 'project', 'img/');
    $img2Result = handleAdminUpload($_FILES['image2'] ?? null, 'image', $editRow['image2'] ?? '', 'project2', 'img/');

    if ($img1Result['error']) { $msg = $img1Result['error']; $msgType = 'error'; }
    elseif ($img2Result['error']) { $msg = $img2Result['error']; $msgType = 'error'; }
    else {
        $vals = [
            trim($_POST['title'] ?? ''),
            trim($_POST['subtitle'] ?? ''),
            trim($_POST['description'] ?? ''),
            trim($_POST['url'] ?? ''),
            $img1Result['path'],
            $img2Result['path'],
            trim($_POST['tags'] ?? ''),
            (int)($_POST['sort_order'] ?? 0),
        ];
        if ($action === 'add') {
            dbExec("INSERT INTO projects (title,subtitle,description,url,image1,image2,tags,sort_order) VALUES (?,?,?,?,?,?,?,?)", $vals);
            $msg = 'Project added!';
        } elseif ($action === 'edit' && $id) {
            $vals[] = $id;
            getDB()->prepare("UPDATE projects SET title=?,subtitle=?,description=?,url=?,image1=?,image2=?,tags=?,sort_order=? WHERE id=?")->execute($vals);
            $msg = 'Project updated!';
        }
    }
}

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM projects WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM projects ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert-success">✅ Project deleted.</div><?php endif; ?>
<?php if ($msg): ?>
  <div style="border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:18px;<?=$msgType === 'error' ? 'background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#f87171' : 'background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);color:#67e8f9'?>">
    <?=h($msg)?>
  </div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow ? '✏️ Edit Project' : '➕ Add New Project'?></div>
  <form method="POST" action="projects.php?action=<?=$editRow ? 'edit&id=' . $id : 'add'?>" enctype="multipart/form-data">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Title *</label>
        <input type="text" name="title" value="<?=h($editRow['title'] ?? '')?>" required>
        <label>Subtitle</label>
        <input type="text" name="subtitle" value="<?=h($editRow['subtitle'] ?? '')?>">
        <label>Live URL (optional)</label>
        <input type="url" name="url" value="<?=h($editRow['url'] ?? '')?>">
        <label>Tags (comma separated, e.g. PHP, MySQL, Web)</label>
        <input type="text" name="tags" value="<?=h($editRow['tags'] ?? '')?>">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order'] ?? '0')?>">
      </div>
      <div>
        <?php renderUploadField('Image 1 (main thumbnail)', 'image1', $editRow['image1'] ?? null, 'image'); ?>
        <?php renderUploadField('Image 2 (optional, for gallery)', 'image2', $editRow['image2'] ?? null, 'image'); ?>
      </div>
    </div>
    <label>Description</label>
    <textarea name="description" rows="4"><?=h($editRow['description'] ?? '')?></textarea>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow ? 'Update' : 'Add'?></button>
      <?php if ($editRow): ?><a href="projects.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All Projects (<?=count($list)?>)</div>
  <?php if (!$list): ?>
    <p style="color:#64748b;font-size:13px">No projects yet.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Title</th><th>Image</th><th>URL</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><strong style="color:#fff"><?=h($row['title'])?></strong><?php if ($row['subtitle']): ?><br><span style="color:#64748b;font-size:11px"><?=h($row['subtitle'])?></span><?php endif; ?></td>
      <td><?=$row['image1'] ? '<img src="../' . h($row['image1']) . '" style="width:50px;height:35px;object-fit:cover;border-radius:4px;border:1px solid #1e2638" />' : '<span style="color:#64748b;font-size:11px">—</span>'?></td>
      <td><?=$row['url'] ? '<a href="' . h($row['url']) . '" target="_blank" style="font-size:12px">' . h(parse_url($row['url'], PHP_URL_HOST)) . ' ↗</a>' : '<span style="color:#334155">—</span>'?></td>
      <td style="display:flex;gap:6px">
        <a href="projects.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px">Edit</a>
        <form method="POST" action="projects.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
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
