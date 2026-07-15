<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'Research Publications';
$pageSubtitle = 'Add, edit or delete research paper entries.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    // --- Handle delete ---
    if ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM research WHERE id=?")->execute([$id]);
        header('Location: research.php?deleted=1');
        exit;
    }

    // --- File upload handler ---
    $pdfFile = '';
    $fileError = '';
    if (!empty($_FILES['pdf_file']['name']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['pdf_file'];
        $mime = mime_content_type($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            $fileError = 'Only PDF files are allowed.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $fileError = 'File too large (max 5 MB).';
        } else {
            $safeName = preg_replace('/[^a-z0-9_\-]/i', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $safeName = strtolower(substr($safeName, 0, 50));
            $filename = 'research_' . $safeName . '_' . time() . '.pdf';
            $destDir = __DIR__ . '/../files/';
            if (!is_dir($destDir)) { mkdir($destDir, 0755, true); }
            if (move_uploaded_file($file['tmp_name'], $destDir . $filename)) {
                $pdfFile = 'files/' . $filename;
            } else {
                $fileError = 'Failed to save file. Check folder permissions (chmod 755 on files/).';
            }
        }
    }

    // --- Determine final PDF path ---
    $finalPdf = $pdfFile;
    if (!$finalPdf) {
        if ($action === 'edit' && $id) {
            $existing = dbRow("SELECT pdf_file FROM research WHERE id=?", [$id]);
            $finalPdf = $existing['pdf_file'] ?? '';
        }
    }
    // Allow clearing PDF via remove button (hidden field)
    if (isset($_POST['remove_pdf']) && $_POST['remove_pdf'] === '1') {
        $finalPdf = '';
    }

    if ($fileError) {
        $msg = $fileError;
        $msgType = 'error';
    } else {
        if ($action === 'add') {
            dbExec("INSERT INTO research (title,description,pdf_file,year,journal,url,sort_order) VALUES (?,?,?,?,?,?,?)", [
                trim($_POST['title']), trim($_POST['description']), $finalPdf,
                trim($_POST['year']), trim($_POST['journal'] ?? ''), trim($_POST['url'] ?? ''),
                (int)($_POST['sort_order'] ?? 0)
            ]);
            $msg = 'Research paper added!';
        } elseif ($action === 'edit' && $id) {
            getDB()->prepare("UPDATE research SET title=?,description=?,pdf_file=?,year=?,journal=?,url=?,sort_order=? WHERE id=?")->execute([
                trim($_POST['title']), trim($_POST['description']), $finalPdf,
                trim($_POST['year']), trim($_POST['journal'] ?? ''), trim($_POST['url'] ?? ''),
                (int)($_POST['sort_order'] ?? 0), $id
            ]);
            $msg = 'Research paper updated!';
        }
    }
}

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM research WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM research ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-success">✅ Entry deleted.</div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>"><?=h($msg)?></div>
<?php endif; ?>

<div class="card">
  <div class="section-heading">
    <?=$editRow ? '✏️ Edit Research Paper' : '➕ Add New Research Paper'?>
  </div>

  <!-- NOTE: enctype needed for file upload -->
  <form method="POST" action="research.php?action=<?=$editRow ? 'edit&id=' . $id : 'add'?>" enctype="multipart/form-data">
    <?=csrfField()?>

    <label>Paper Title *</label>
    <input type="text" name="title" value="<?=h($editRow['title'] ?? '')?>" required placeholder="Digital Transformation in Nepal's Cooperative Sector">

    <label>Abstract / Description *</label>
    <textarea name="description" rows="3"><?=h($editRow['description'] ?? '')?></textarea>

    <div class="grid-2" style="margin-top:0">
      <div>
        <label>Journal / Conference / Publisher Name</label>
        <input type="text" name="journal" value="<?=h($editRow['journal'] ?? '')?>" placeholder="SIP 2026 Canada — Abstract ID: SIP26-CA-166">

        <?php renderUploadField('Upload PDF File (max 5 MB)', 'pdf_file', $editRow['pdf_file'] ?? null, 'pdf'); ?>

        <label>Online Publication URL <span style="color:#64748b">(ResearchGate, Google Scholar, conference site, etc.)</span></label>
        <input type="url" name="url" value="<?=h($editRow['url'] ?? '')?>" placeholder="https://www.researchgate.net/publication/...">
      </div>

      <div>
        <label>Year Published</label>
        <input type="text" name="year" value="<?=h($editRow['year'] ?? '')?>" placeholder="2024">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order'] ?? '0')?>">
      </div>
    </div>

    <div style="margin-top:10px;background:#0f1420;border:1px solid #2a3347;border-radius:8px;padding:10px 14px;font-size:12px;color:#64748b">
      💡 <strong style="color:#c9d1e3">Upload PDF:</strong> Click "Choose File" above to upload a PDF directly. Or leave blank to keep the existing file. Use the "Remove" checkbox to delete the current PDF.
    </div>

    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow ? 'Update' : 'Add'?></button>
      <?php if ($editRow): ?>
        <a href="research.php" class="btn btn-secondary">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Research Papers (<?=count($list)?>)</div>
  <?php if (!$list): ?>
    <p style="color:#64748b;font-size:13px">No entries yet. Add one above.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Title</th><th>Year</th><th>PDF</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><strong style="color:#fff"><?=h(mb_substr($row['title'], 0, 60))?></strong><br><span style="color:#64748b;font-size:11px"><?=h(mb_substr($row['description'], 0, 80))?></span></td>
      <td style="color:#22d3ee;font-size:12px"><?=h($row['year'])?></td>
      <td>
        <?php if (!empty($row['pdf_file'])): ?>
          <a href="../<?=h($row['pdf_file'])?>" target="_blank" style="font-size:11px;color:#67e8f9">📄 View</a>
        <?php else: ?>
          <span style="color:#64748b;font-size:11px">—</span>
        <?php endif; ?>
      </td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="research.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="research.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
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
