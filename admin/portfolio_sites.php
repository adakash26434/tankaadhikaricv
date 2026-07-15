<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/file_upload.php';
requireAdmin();
$pageTitle = 'Portfolio Websites';
$pageSubtitle = 'Add, edit or delete websites shown in the Portfolio section gallery.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM portfolio_sites WHERE id=?")->execute([$id]);
        header('Location: portfolio_sites.php?deleted=1');
        exit;
    }

    $imgResult = handleAdminUpload($_FILES['image'] ?? null, 'image', $editRow['image'] ?? '', 'portfolio', 'img/');

    if ($imgResult['error']) { $msg = $imgResult['error']; $msgType = 'error'; }
    else {
        $vals = [
            trim($_POST['title']    ?? ''),
            trim($_POST['subtitle'] ?? ''),
            $imgResult['path'],
            trim($_POST['url']     ?? ''),
            (int)($_POST['sort_order'] ?? 0),
        ];
        if ($action === 'add') {
            dbExec("INSERT INTO portfolio_sites (title,subtitle,image,url,sort_order) VALUES (?,?,?,?,?)", $vals);
            $msg = 'Portfolio site added!';
        } elseif ($action === 'edit' && $id) {
            $vals[] = $id;
            getDB()->prepare("UPDATE portfolio_sites SET title=?,subtitle=?,image=?,url=?,sort_order=? WHERE id=?")->execute($vals);
            $msg = 'Portfolio site updated!';
        }
    }
}

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM portfolio_sites WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM portfolio_sites ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if ($msg): ?>
  <div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>"><?=h($msg)?></div>
<?php endif; ?>

<div class="card">
  <div class="section-heading"><?=$editRow ? '✏️ Edit Portfolio Site' : '➕ Add New Portfolio Site'?></div>
  <form method="POST" action="portfolio_sites.php?action=<?=$editRow ? 'edit&id=' . $id : 'add'?>" enctype="multipart/form-data">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Site / Project Name *</label>
        <input type="text" name="title" value="<?=h($editRow['title'] ?? '')?>" required placeholder="Jaya Sahakari">
        <label>Subtitle / Category</label>
        <input type="text" name="subtitle" value="<?=h($editRow['subtitle'] ?? '')?>" placeholder="Cooperative Website">
        <?php renderUploadField('Website Thumbnail Image', 'image', $editRow['image'] ?? null, 'image'); ?>
      </div>
      <div>
        <label>Live URL (leave blank if no website)</label>
        <input type="url" name="url" value="<?=h($editRow['url'] ?? '')?>" placeholder="https://jayasahakari.com.np">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order'] ?? '0')?>">
      </div>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow ? 'Update' : 'Add'?></button>
      <?php if ($editRow): ?><a href="portfolio_sites.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="section-heading-sm">All Portfolio Sites (<?=count($list)?>)</div>
  <?php if (!$list): ?>
    <p style="color:#64748b;font-size:13px">No entries yet.</p>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px">
    <?php foreach ($list as $row): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-radius:10px;overflow:hidden">
      <?php if ($row['image']): ?>
        <img src="../<?=h($row['image'])?>" style="width:100%;height:100px;object-fit:cover" />
      <?php else: ?>
        <div style="width:100%;height:100px;background:#161b27;display:flex;align-items:center;justify-content:center;color:#64748b"><i class="fa fa-image fa-2x"></i></div>
      <?php endif; ?>
      <div style="padding:12px">
        <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:2px"><?=h($row['title'])?></div>
        <div style="font-size:11px;color:#64748b;margin-bottom:8px"><?=h($row['subtitle'])?></div>
        <?php if ($row['url']): ?><a href="<?=h($row['url'])?>" target="_blank" style="font-size:11px;color:#22d3ee">↗ <?=h(parse_url($row['url'], PHP_URL_HOST))?></a><?php endif; ?>
        <div style="display:flex;gap:6px;margin-top:10px">
          <a href="portfolio_sites.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px;flex:1;text-align:center">Edit</a>
          <form method="POST" action="portfolio_sites.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
            <?=csrfField()?>
            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
