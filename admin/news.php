<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'News & Publications';
$pageSubtitle = 'Add, edit or delete news and online publication entries.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM news WHERE id=?")->execute([$id]);
        header('Location: news.php?deleted=1');
        exit;
    }

    $imgResult  = handleAdminUpload($_FILES['image']    ?? null, 'image', $editRow['image']    ?? '', 'news', 'img/');
    $pdfResult = handleAdminUpload($_FILES['pdf_file'] ?? null, 'pdf',   $editRow['pdf_file'] ?? '', 'newsdoc', 'files/');

    if ($imgResult['error'])  { $msg = $imgResult['error'];  $msgType = 'error'; }
    elseif ($pdfResult['error']) { $msg = $pdfResult['error']; $msgType = 'error'; }
    else {
        $vals = [
            trim($_POST['title']   ?? ''),
            trim($_POST['source']  ?? ''),
            $imgResult['path'],
            $pdfResult['path'],
            trim($_POST['url']    ?? ''),
            (int)($_POST['sort_order'] ?? 0),
        ];
        if ($action === 'add') {
            dbExec("INSERT INTO news (title,source,image,pdf_file,url,sort_order) VALUES (?,?,?,?,?,?)", $vals);
            $msg = 'News item added!';
        } elseif ($action === 'edit' && $id) {
            $vals[] = $id;
            getDB()->prepare("UPDATE news SET title=?,source=?,image=?,pdf_file=?,url=?,sort_order=? WHERE id=?")->execute($vals);
            $msg = 'News item updated!';
        }
    }
}

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM news WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM news ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if ($msg): ?>
  <div style="border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:18px;<?=$msgType === 'error' ? 'background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#f87171' : 'background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);color:#67e8f9'?>">
    <?=h($msg)?>
  </div>
<?php endif; ?>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow ? '✏️ Edit News Item' : '➕ Add New News Item'?></div>
  <form method="POST" action="news.php?action=<?=$editRow ? 'edit&id=' . $id : 'add'?>" enctype="multipart/form-data">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Headline / Title *</label>
        <input type="text" name="title" value="<?=h($editRow['title'] ?? '')?>" required placeholder="ICT Award Winner 2024 — Aakash Digital">
        <label>Source / Publication Name *</label>
        <input type="text" name="source" value="<?=h($editRow['source'] ?? '')?>" required placeholder="Gorkhapatra National Daily">
        <?php renderUploadField('News Image (optional)', 'image', $editRow['image'] ?? null, 'image'); ?>
      </div>
      <div>
        <?php renderUploadField('PDF File (optional)', 'pdf_file', $editRow['pdf_file'] ?? null, 'pdf'); ?>
        <label>External URL (if online article, leave blank if PDF)</label>
        <input type="url" name="url" value="<?=h($editRow['url'] ?? '')?>" placeholder="https://...">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order'] ?? '0')?>">
      </div>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow ? 'Update' : 'Add'?></button>
      <?php if ($editRow): ?><a href="news.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All News Items (<?=count($list)?>)</div>
  <?php if (!$list): ?>
    <p style="color:#64748b;font-size:13px">No entries yet.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Image</th><th>Title</th><th>Source</th><th>Link</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><?=$row['image'] ? '<img src="../' . h($row['image']) . '" style="width:50px;height:35px;object-fit:cover;border-radius:4px;border:1px solid #1e2638" />' : '<span style="color:#64748b;font-size:11px">—</span>'?></td>
      <td><strong style="color:#fff"><?=h(mb_substr($row['title'], 0, 50))?></strong><br><span style="color:#64748b;font-size:11px"><?=h($row['source'])?></span></td>
      <td style="color:#8892a4;font-size:12px"><?=h($row['source'])?></td>
      <td>
        <?php $link = $row['url'] ?: $row['pdf_file']; ?>
        <?=$link ? '<a href="../' . h($link) . '" target="_blank" style="font-size:11px">↗ Open</a>' : '<span style="color:#64748b;font-size:11px">—</span>'?>
      </td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="news.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px">Edit</a>
        <form method="POST" action="news.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
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
