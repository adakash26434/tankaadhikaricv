<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Portfolio Websites';
$pageSubtitle = 'Add, edit or delete websites shown in the Portfolio section gallery.';

$msg = ''; $action = $_GET['action'] ?? ''; $id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf();
  if ($action === 'add') {
    dbExec("INSERT INTO portfolio_sites (image,title,subtitle,url,sort_order) VALUES (?,?,?,?,?)", [
      trim($_POST['image']), trim($_POST['title']), trim($_POST['subtitle']),
      trim($_POST['url']), (int)($_POST['sort_order']??0)
    ]);
    $msg = 'added';
  } elseif ($action === 'edit' && $id) {
    getDB()->prepare("UPDATE portfolio_sites SET image=?,title=?,subtitle=?,url=?,sort_order=? WHERE id=?")->execute([
      trim($_POST['image']), trim($_POST['title']), trim($_POST['subtitle']),
      trim($_POST['url']), (int)($_POST['sort_order']??0), $id
    ]);
    $msg = 'updated';
  } elseif ($action === 'delete' && $id) {
    getDB()->prepare("DELETE FROM portfolio_sites WHERE id=?")->execute([$id]);
    header('Location: portfolio_sites.php?deleted=1'); exit;
  }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM portfolio_sites WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM portfolio_sites ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Entry deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Portfolio site added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Portfolio site updated!</div><?php endif; ?>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow?'✏️ Edit Portfolio Site':'➕ Add New Portfolio Site'?></div>
  <form method="POST" action="portfolio_sites.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label>Site / Project Name *</label>
        <input type="text" name="title" value="<?=h($editRow['title']??'')?>" required placeholder="Jaya Sahakari">
        <label>Subtitle / Category</label>
        <input type="text" name="subtitle" value="<?=h($editRow['subtitle']??'')?>" placeholder="Cooperative Website">
        <label>Image Path (e.g. img/jayasahakari-thumb.jpg)</label>
        <input type="text" name="image" value="<?=h($editRow['image']??'')?>" placeholder="img/site-thumb.jpg">
      </div>
      <div>
        <label>Live URL (leave blank if no website)</label>
        <input type="url" name="url" value="<?=h($editRow['url']??'')?>" placeholder="https://jayasahakari.com.np">
        <label>Sort Order (lower = shown first)</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="portfolio_sites.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All Portfolio Sites (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No entries yet. Add one above.</p><?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px">
    <?php foreach($list as $row): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-radius:10px;overflow:hidden">
      <?php if($row['image']): ?><img src="../<?=h($row['image'])?>" style="width:100%;height:100px;object-fit:cover" /><?php else: ?><div style="width:100%;height:100px;background:#161b27;display:flex;align-items:center;justify-content:center;color:#64748b"><i class="fa fa-image fa-2x"></i></div><?php endif; ?>
      <div style="padding:12px">
        <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:2px"><?=h($row['title'])?></div>
        <div style="font-size:11px;color:#64748b;margin-bottom:8px"><?=h($row['subtitle'])?></div>
        <?php if($row['url']): ?><a href="<?=h($row['url'])?>" target="_blank" style="font-size:11px;color:#22d3ee">↗ <?=h(parse_url($row['url'],PHP_URL_HOST))?></a><?php endif; ?>
        <div style="display:flex;gap:6px;margin-top:10px">
          <a href="portfolio_sites.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary" style="font-size:11px;padding:4px 10px;flex:1;text-align:center">Edit</a>
          <form method="POST" action="portfolio_sites.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
            <?=csrfField()?>
            <button class="btn btn-danger" style="font-size:11px;padding:4px 10px" type="submit">Delete</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
