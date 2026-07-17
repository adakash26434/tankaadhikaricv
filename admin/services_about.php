<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$pageTitle = 'Services';
$pageSubtitle = 'Manage Services I Offer — shown in About section.';

$msg = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$editRow = null;
if ($action === 'edit' && $id > 0) {
    $editRow = dbRow("SELECT * FROM services_about WHERE id=?", [$id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    
    if ($action === 'delete' && $id > 0) {
        getDB()->prepare("DELETE FROM services_about WHERE id=?")->execute([$id]);
        header('Location: services_about.php?deleted=1');
        exit;
    }
    
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'globe');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    
    if ($action === 'add' && $name) {
        getDB()->prepare("INSERT INTO services_about (name, icon, description, sort_order, is_pricing) VALUES (?, ?, ?, ?, 0)")
            ->execute([$name, $icon, $description, $sortOrder]);
        header('Location: services_about.php?added=1');
        exit;
    }
    
    if ($action === 'edit' && $id > 0 && $name) {
        getDB()->prepare("UPDATE services_about SET name=?, icon=?, description=?, sort_order=? WHERE id=?")
            ->execute([$name, $icon, $description, $sortOrder, $id]);
        header('Location: services_about.php?updated=1');
        exit;
    }
}

// Get only regular services (is_pricing = 0 or is_pricing column doesn't exist)
try {
    $db = getDB();
    $cols = $db->query("SHOW COLUMNS FROM services_about LIKE 'is_pricing'")->fetchAll();
    if (empty($cols)) {
        // Column doesn't exist - show all services
        $services = dbRows("SELECT * FROM services_about ORDER BY sort_order, id");
    } else {
        $services = dbRows("SELECT * FROM services_about WHERE is_pricing = 0 ORDER BY sort_order, id");
    }
} catch (Exception $e) {
    $services = [];
}

include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">✅ Service deleted.</div>
<?php endif; ?>
<?php if (isset($_GET['added'])): ?>
<div class="alert alert-success">✅ Service added!</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success">✅ Service updated!</div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div class="section-heading">🎯 Services I Offer</div>
  <p style="font-size:12px;color:#64748b;margin-bottom:16px">These services are shown in the About section as icon cards.</p>
  
  <?php if (empty($services)): ?>
    <p style="color:#64748b;text-align:center;padding:20px">No services yet.</p>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px">
    <?php foreach ($services as $row): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-left:3px solid #22d3ee;border-radius:10px;padding:16px;text-align:center">
      <i class="fa fa-<?php echo h($row['icon']); ?>" style="color:#22d3ee;font-size:26px;margin-bottom:10px;display:block"></i>
      <div style="font-size:11px;font-weight:700;color:#fff;margin-bottom:4px"><?php echo h($row['name']); ?></div>
      <div style="font-size:10px;color:#64748b;margin-bottom:10px"><?php echo h($row['description']); ?></div>
      <div style="display:flex;gap:6px;justify-content:center">
        <a href="services_about.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
        <form method="POST" action="services_about.php?action=delete&id=<?php echo $row['id']; ?>" onsubmit="return confirm('Delete?')" style="display:inline">
          <?php echo csrfField(); ?>
          <button class="btn btn-danger btn-sm" type="submit">🗑</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="section-heading">➕ <?php echo $editRow ? 'Edit Service' : 'Add New Service'; ?></div>
  
  <form method="POST" action="services_about.php?action=<?php echo $editRow ? 'edit&id=' . $id : 'add'; ?>">
    <?php echo csrfField(); ?>
    
    <div class="grid-3">
      <div>
        <label>Service Name *</label>
        <input type="text" name="name" value="<?php echo h($editRow['name'] ?? ''); ?>" required placeholder="Web Development">
      </div>
      <div>
        <label>Icon (e.g. globe, code, envelope)</label>
        <input type="text" name="icon" value="<?php echo h($editRow['icon'] ?? 'globe'); ?>" placeholder="globe">
      </div>
      <div>
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?php echo h($editRow['sort_order'] ?? '0'); ?>">
      </div>
    </div>
    
    <label style="margin-top:12px">Short Description</label>
    <input type="text" name="description" value="<?php echo h($editRow['description'] ?? ''); ?>" placeholder="Brief description of the service">
    
    <div style="margin-top:16px;display:flex;gap:10px">
      <button class="btn btn-primary" type="submit">💾 <?php echo $editRow ? 'Update' : 'Add Service'; ?></button>
      <?php if ($editRow): ?>
        <a href="services_about.php" class="btn btn-secondary">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
