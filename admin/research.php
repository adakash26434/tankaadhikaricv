<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Research Publications';
$pageSubtitle = 'Add, edit or delete research paper entries.';

$msg = ''; $action = $_GET['action'] ?? ''; $id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf();
  if ($action === 'add') {
    dbExec("INSERT INTO research (title,description,pdf_file,year,journal,url,sort_order) VALUES (?,?,?,?,?,?,?)", [
      trim($_POST['title']), trim($_POST['description']), trim($_POST['pdf_file']),
      trim($_POST['year']), trim($_POST['journal']??''), trim($_POST['url']??''),
      (int)($_POST['sort_order']??0)
    ]);
    $msg = 'added';
  } elseif ($action === 'edit' && $id) {
    getDB()->prepare("UPDATE research SET title=?,description=?,pdf_file=?,year=?,journal=?,url=?,sort_order=? WHERE id=?")->execute([
      trim($_POST['title']), trim($_POST['description']), trim($_POST['pdf_file']),
      trim($_POST['year']), trim($_POST['journal']??''), trim($_POST['url']??''),
      (int)($_POST['sort_order']??0), $id
    ]);
    $msg = 'updated';
  } elseif ($action === 'delete' && $id) {
    getDB()->prepare("DELETE FROM research WHERE id=?")->execute([$id]);
    header('Location: research.php?deleted=1'); exit;
  }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM research WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM research ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Research paper added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Research paper updated!</div><?php endif; ?>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow?'✏️ Edit Research Paper':'➕ Add New Research Paper'?></div>
  <form method="POST" action="research.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <label>Paper Title *</label>
    <input type="text" name="title" value="<?=h($editRow['title']??'')?>" required placeholder="Digital Transformation in Nepal's Cooperative Sector">
    <label>Abstract / Description *</label>
    <textarea name="description" rows="3"><?=h($editRow['description']??'')?></textarea>
    <div class="grid-2" style="margin-top:0">
      <div>
        <label>Journal / Conference / Publisher Name</label>
        <input type="text" name="journal" value="<?=h($editRow['journal']??'')?>" placeholder="SIP 2026 Canada — Abstract ID: SIP26-CA-166">
        <label>PDF File Path (e.g. files/research1.pdf)</label>
        <input type="text" name="pdf_file" value="<?=h($editRow['pdf_file']??'')?>" placeholder="files/research1.pdf">
        <label>Online Publication URL <span style="color:#64748b">(where paper is published — ResearchGate, Google Scholar, conference site, etc.)</span></label>
        <input type="url" name="url" value="<?=h($editRow['url']??'')?>" placeholder="https://www.researchgate.net/publication/...">
      </div>
      <div>
        <label>Year Published</label>
        <input type="text" name="year" value="<?=h($editRow['year']??'')?>" placeholder="2024">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <div style="margin-top:10px;background:#0f1420;border:1px solid #2a3347;border-radius:8px;padding:10px 14px;font-size:12px;color:#64748b">
      💡 <strong style="color:#c9d1e3">Online link:</strong> If the paper is published on ResearchGate, Google Scholar, SSRN, or any conference website, paste that URL here. A purple <em>"Published Online ↗"</em> button will appear on the portfolio.
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="research.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All Research Papers (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No entries yet. Add one above.</p><?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Title</th><th>Year</th><th>PDF</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td><strong style="color:#fff"><?=h(mb_substr($row['title'],0,60))?></strong><br><span style="color:#64748b;font-size:11px"><?=h(mb_substr($row['description'],0,80))?></span></td>
      <td style="color:#22d3ee;font-size:12px"><?=h($row['year'])?></td>
      <td><?=$row['pdf_file']?'<a href="../'.h($row['pdf_file']).'" target="_blank" style="font-size:11px">📄 View</a>':'<span style="color:#64748b;font-size:11px">—</span>'?></td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <a href="research.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px">Edit</a>
        <form method="POST" action="research.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete this entry?')">
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
