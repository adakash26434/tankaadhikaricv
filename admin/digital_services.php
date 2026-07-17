<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$pageTitle = 'Digital Services';
$pageSubtitle = 'Manage Digital Services pricing cards — shown in Digital Services section.';

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
        header('Location: digital_services.php?deleted=1');
        exit;
    }
    
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'globe');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $price = trim($_POST['price'] ?? '');
    $priceUnit = trim($_POST['price_unit'] ?? '');
    $accentColor = trim($_POST['accent_color'] ?? 'cyan');
    
    $featuresRaw = trim($_POST['features'] ?? '');
    $features = $featuresRaw 
        ? json_encode(array_filter(array_map('trim', explode("\n", $featuresRaw)), 'strlen'))
        : '';
    
    $ctaText = trim($_POST['cta_text'] ?? '');
    $ctaLink = trim($_POST['cta_link'] ?? '#contact');
    
    if ($action === 'add' && $name) {
        getDB()->prepare("INSERT INTO services_about (name, icon, description, sort_order, is_pricing, price, price_unit, features, accent_color, cta_text, cta_link) VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?)")
            ->execute([$name, $icon, $description, $sortOrder, $price, $priceUnit, $features, $accentColor, $ctaText, $ctaLink]);
        header('Location: digital_services.php?added=1');
        exit;
    }
    
    if ($action === 'edit' && $id > 0 && $name) {
        getDB()->prepare("UPDATE services_about SET name=?, icon=?, description=?, sort_order=?, price=?, price_unit=?, features=?, accent_color=?, cta_text=?, cta_link=? WHERE id=?")
            ->execute([$name, $icon, $description, $sortOrder, $price, $priceUnit, $features, $accentColor, $ctaText, $ctaLink, $id]);
        header('Location: digital_services.php?updated=1');
        exit;
    }
}

// Get only pricing services (is_pricing = 1)
try {
    $db = getDB();
    $cols = $db->query("SHOW COLUMNS FROM services_about LIKE 'is_pricing'")->fetchAll();
    if (empty($cols)) {
        // Column doesn't exist - show empty (user needs to run upgrade)
        $services = [];
    } else {
        $services = dbRows("SELECT * FROM services_about WHERE is_pricing = 1 ORDER BY sort_order, id");
    }
} catch (Exception $e) {
    $services = [];
}

include __DIR__ . '/header.php';
?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">✅ Digital Service deleted.</div>
<?php endif; ?>
<?php if (isset($_GET['added'])): ?>
<div class="alert alert-success">✅ Digital Service added!</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success">✅ Digital Service updated!</div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <div class="section-heading">💰 Digital Services</div>
  <p style="font-size:12px;color:#64748b;margin-bottom:16px">These are shown in the Digital Services section with pricing, features, and CTA buttons.</p>
  
  <?php if (empty($services)): ?>
    <p style="color:#64748b;text-align:center;padding:20px">No digital services yet.</p>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    <?php foreach ($services as $row): 
        $accent = h($row['accent_color'] ?? 'cyan');
        $features = $row['features'] ? json_decode($row['features'], true) : [];
    ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-left:4px solid var(--<?php echo $accent; ?>);border-radius:12px;padding:20px">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
        <i class="fa fa-<?php echo h($row['icon']); ?>" style="color:var(--<?php echo $accent; ?>);font-size:28px"></i>
        <?php if ($row['price']): ?>
          <div style="text-align:right">
            <span style="font-size:22px;font-weight:900;color:var(--<?php echo $accent; ?>)"><?php echo h($row['price']); ?></span>
            <?php if ($row['price_unit']): ?>
              <br><span style="font-size:10px;color:#64748b"><?php echo h($row['price_unit']); ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:6px"><?php echo h($row['name']); ?></div>
      <?php if ($row['description']): ?>
        <div style="font-size:11px;color:#64748b;margin-bottom:12px"><?php echo h($row['description']); ?></div>
      <?php endif; ?>
      <?php if ($features): ?>
        <ul style="font-size:11px;color:#8892a4;line-height:1.8;padding-left:14px;margin-bottom:12px">
          <?php foreach (array_slice($features, 0, 4) as $f): ?>
            <li><?php echo h($f); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if ($row['cta_text']): ?>
        <a href="<?php echo h($row['cta_link'] ?? '#contact'); ?>" class="tag" style="display:block;text-align:center;background:rgba(0,0,0,.3);border-color:var(--<?php echo $accent; ?>);color:var(--<?php echo $accent; ?>);font-size:11px;margin-bottom:12px;text-decoration:none"><?php echo h($row['cta_text']); ?></a>
      <?php endif; ?>
      <div style="display:flex;gap:6px;justify-content:flex-end">
        <a href="digital_services.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
        <form method="POST" action="digital_services.php?action=delete&id=<?php echo $row['id']; ?>" onsubmit="return confirm('Delete?')" style="display:inline">
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
  <div class="section-heading">➕ <?php echo $editRow ? 'Edit Digital Service' : 'Add New Digital Service'; ?></div>
  
  <form method="POST" action="digital_services.php?action=<?php echo $editRow ? 'edit&id=' . $id : 'add'; ?>">
    <?php echo csrfField(); ?>
    
    <div class="grid-3">
      <div>
        <label>Service Name *</label>
        <input type="text" name="name" value="<?php echo h($editRow['name'] ?? ''); ?>" required placeholder="Web Development">
      </div>
      <div>
        <label>Icon (e.g. globe, code, envelope, cloud)</label>
        <input type="text" name="icon" value="<?php echo h($editRow['icon'] ?? 'globe'); ?>" placeholder="globe">
      </div>
      <div>
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?php echo h($editRow['sort_order'] ?? '0'); ?>">
      </div>
    </div>
    
    <label style="margin-top:12px">Short Description</label>
    <input type="text" name="description" value="<?php echo h($editRow['description'] ?? ''); ?>" placeholder="Brief description">
    
    <div class="grid-3" style="margin-top:14px">
      <div>
        <label>Price (e.g. 15000)</label>
        <input type="text" name="price" value="<?php echo h($editRow['price'] ?? ''); ?>" placeholder="15000">
      </div>
      <div>
        <label>Price Unit (e.g. NPR /year)</label>
        <input type="text" name="price_unit" value="<?php echo h($editRow['price_unit'] ?? ''); ?>" placeholder="NPR /year">
      </div>
      <div>
        <label>Accent Color</label>
        <select name="accent_color">
          <?php foreach (['cyan', 'violet', 'yellow', 'red', 'amber', 'green'] as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo ($editRow['accent_color'] ?? 'cyan') === $c ? 'selected' : ''; ?>><?php echo ucfirst($c); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    
    <label style="margin-top:14px">Features (one per line)</label>
    <textarea name="features" rows="4" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"><?php echo h(
        isset($editRow['features']) && $editRow['features']
            ? implode("\n", json_decode($editRow['features'], true) ?? [])
            : ''
    ); ?></textarea>
    
    <div class="grid-2" style="margin-top:14px">
      <div>
        <label>CTA Button Text</label>
        <input type="text" name="cta_text" value="<?php echo h($editRow['cta_text'] ?? ''); ?>" placeholder="Request a Quote →">
      </div>
      <div>
        <label>CTA Link</label>
        <input type="text" name="cta_link" value="<?php echo h($editRow['cta_link'] ?? '#contact'); ?>" placeholder="#contact">
      </div>
    </div>
    
    <div style="margin-top:16px;display:flex;gap:10px">
      <button class="btn btn-primary" type="submit">💾 <?php echo $editRow ? 'Update' : 'Add Digital Service'; ?></button>
      <?php if ($editRow): ?>
        <a href="digital_services.php" class="btn btn-secondary">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
