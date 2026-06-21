<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Projects';
$pageSubtitle = 'Manage portfolio projects.';

$msg = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $fields = ['title','subtitle','description','url','image1','image2','tags','sort_order'];
    $vals = [];
    foreach ($fields as $f) $vals[] = $f === 'sort_order' ? (int)($_POST[$f]??0) : trim($_POST[$f]??'');
    if ($action === 'add') {
        dbExec("INSERT INTO projects (title,subtitle,description,url,image1,image2,tags,sort_order) VALUES (?,?,?,?,?,?,?,?)", $vals);
        $msg = 'added';
    } elseif ($action === 'edit' && $id) {
        $vals[] = $id;
        getDB()->prepare("UPDATE projects SET title=?,subtitle=?,description=?,url=?,image1=?,image2=?,tags=?,sort_order=? WHERE id=?")->execute($vals);
        $msg = 'updated';
    } elseif ($action === 'delete' && $id) {
        getDB()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
        header('Location: projects.php?deleted=1'); exit;
    }
}
$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM projects WHERE id=?", [$id]) : null;
$list = dbRows("SELECT * FROM projects ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Project deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert-success">✅ Project added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert-success">✅ Project updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px"><?=$editRow?'✏️ Edit Project':'➕ Add New Project'?></div>
  <form method="POST" action="projects.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-2">
      <div>
        <label for="proj-title">Title</label>
        <input type="text" id="proj-title" name="title" value="<?=h($editRow['title']??'')?>" required>
        <label for="proj-sub">Subtitle</label>
        <input type="text" id="proj-sub" name="subtitle" value="<?=h($editRow['subtitle']??'')?>">
        <label for="proj-url">Live URL (optional)</label>
        <input type="url" id="proj-url" name="url" value="<?=h($editRow['url']??'')?>">
      </div>
      <div>
        <label for="proj-img1">Image 1 path (e.g. img/project1.jpg)</label>
        <input type="text" id="proj-img1" name="image1" value="<?=h($editRow['image1']??'')?>">
        <label for="proj-img2">Image 2 path (optional)</label>
        <input type="text" id="proj-img2" name="image2" value="<?=h($editRow['image2']??'')?>">
        <label for="proj-tags">Tags (comma separated)</label>
        <input type="text" id="proj-tags" name="tags" value="<?=h($editRow['tags']??'')?>">
        <label for="proj-sort">Sort Order</label>
        <input type="number" id="proj-sort" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>
    <label for="proj-desc">Description</label>
    <textarea id="proj-desc" name="description" rows="4"><?=h($editRow['description']??'')?></textarea>
    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="projects.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px">All Projects (<?=count($list)?>)</div>
  <table>
    <thead><tr><th>#</th><th>Title</th><th>URL</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr>
      <td style="color:#64748b"><?=$row['sort_order']?></td>
      <td>
        <strong style="color:#fff"><?=h($row['title'])?></strong>
        <?php if($row['image1']): ?><br><small style="color:#64748b"><?=h($row['image1'])?></small><?php endif; ?>
      </td>
      <td><?=$row['url']?'<a href="'.h($row['url']).'" target="_blank" style="font-size:12px">'.h(parse_url($row['url'],PHP_URL_HOST)).' ↗</a>':'<span style="color:#334155">—</span>'?></td>
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
</div>
<?php include __DIR__ . '/footer.php'; ?>
