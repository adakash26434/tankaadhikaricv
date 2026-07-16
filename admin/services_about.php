<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Services';
$pageSubtitle = 'Manage Services I Offer and Digital Services separately.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$editRow = ($action === 'edit' && $id) ? dbRow("SELECT * FROM services_about WHERE id=?", [$id]) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrf();
  if ($action === 'delete' && $id) {
    getDB()->prepare("DELETE FROM services_about WHERE id=?")->execute([$id]);
    header('Location: services_about.php?deleted=1'); exit;
  }

  $isPricing  = !empty($_POST['is_pricing']);
  $price      = trim($_POST['price'] ?? '');
  $priceUnit  = trim($_POST['price_unit'] ?? '');
  $featuresRaw = trim($_POST['features'] ?? '');
  $features   = $isPricing && $featuresRaw
    ? json_encode(array_filter(array_map('trim', explode("\n", $featuresRaw)), 'strlen'))
    : '';
  $accentColor = trim($_POST['accent_color'] ?? 'cyan');
  $ctaText    = trim($_POST['cta_text'] ?? '');
  $ctaLink    = trim($_POST['cta_link'] ?? '#contact');

  $vals = [
    trim($_POST['name']        ?? ''),
    trim($_POST['icon']        ?? 'globe'),
    trim($_POST['description'] ?? ''),
    (int)($_POST['sort_order'] ?? 0),
    $isPricing ? 1 : 0,
    $price,
    $priceUnit,
    $features,
    $accentColor,
    $ctaText,
    $ctaLink,
  ];

  if ($action === 'add') {
    dbExec("INSERT INTO services_about (name,icon,description,sort_order,is_pricing,price,price_unit,features,accent_color,cta_text,cta_link) VALUES (?,?,?,?,?,?,?,?,?,?,?)", $vals);
    $msg = 'added';
  } elseif ($action === 'edit' && $id) {
    $vals[] = $id;
    getDB()->prepare("UPDATE services_about SET name=?,icon=?,description=?,sort_order=?,is_pricing=?,price=?,price_unit=?,features=?,accent_color=?,cta_text=?,cta_link=? WHERE id=?")->execute($vals);
    $msg = 'updated';
  }
}

$allServices = dbRows("SELECT * FROM services_about ORDER BY is_pricing DESC, sort_order, id");
$regularServices = array_filter($allServices, function($s) { return empty($s['is_pricing']); });
$pricingServices = array_filter($allServices, function($s) { return !empty($s['is_pricing']); });

include __DIR__ . '/header.php';
?>

<?php if(isset($_GET['deleted'])): ?><div class="alert alert-success">✅ Service deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert alert-success">✅ Service added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert alert-success">✅ Service updated!</div><?php endif; ?>

<!-- ======================= -->
<!-- SECTION 1: SERVICES I OFFER -->
<!-- ======================= -->
<div style="margin-bottom:40px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #1e2638">
    <div>
      <h2 style="font-size:18px;font-weight:700;color:#fff;margin:0">🎯 Services I Offer</h2>
      <p style="font-size:12px;color:#64748b;margin:6px 0 0">Shown in About section as icon grid</p>
    </div>
    <a href="services_about.php?action=add&type=regular" class="btn btn-primary btn-sm">➕ Add Service</a>
  </div>

  <?php if(empty($regularServices)): ?>
    <div class="card" style="text-align:center;padding:32px;color:#64748b">
      No services yet. <a href="services_about.php?action=add&type=regular" style="color:#22d3ee">Add your first service</a>
    </div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px">
    <?php foreach($regularServices as $row): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-left:3px solid #22d3ee;border-radius:10px;padding:16px;text-align:center">
      <i class="fa fa-<?=h($row['icon'])?>" style="color:#22d3ee;font-size:26px;margin-bottom:10px;display:block"></i>
      <div style="font-size:11px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px"><?=h($row['name'])?></div>
      <div style="font-size:10px;color:#64748b;margin-bottom:10px"><?=h($row['description'])?></div>
      <div style="display:flex;gap:6px;justify-content:center">
        <a href="services_about.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">✏️</a>
        <form method="POST" action="services_about.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')" style="display:inline">
          <?=csrfField()?>
          <button class="btn btn-danger btn-sm" type="submit">🗑</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ======================= -->
<!-- SECTION 2: DIGITAL SERVICES -->
<!-- ======================= -->
<div>
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #1e2638">
    <div>
      <h2 style="font-size:18px;font-weight:700;color:#fff;margin:0">💰 Digital Services</h2>
      <p style="font-size:12px;color:#64748b;margin:6px 0 0">Shown in Digital Services section with pricing cards</p>
    </div>
    <a href="services_about.php?action=add&type=pricing" class="btn btn-primary btn-sm">➕ Add Digital Service</a>
  </div>

  <?php if(empty($pricingServices)): ?>
    <div class="card" style="text-align:center;padding:32px;color:#64748b">
      No digital services yet. <a href="services_about.php?action=add&type=pricing" style="color:#22d3ee">Add your first pricing card</a>
    </div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">
    <?php foreach($pricingServices as $row):
      $accent = h($row['accent_color'] ?? 'cyan');
      $features = $row['features'] ? json_decode($row['features'], true) : [];
    ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-left:4px solid var(--<?=$accent?>);border-radius:12px;padding:20px">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
        <i class="fa fa-<?=h($row['icon'])?>" style="color:var(--<?=$accent?>);font-size:28px"></i>
        <?php if($row['price']): ?>
          <div style="text-align:right">
            <span style="font-size:22px;font-weight:900;color:var(--<?=$accent?>)"><?=h($row['price'])?></span>
            <?php if($row['price_unit']): ?><br><span style="font-size:10px;color:#64748b"><?=h($row['price_unit'])?></span><?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:6px"><?=h($row['name'])?></div>
      <?php if($row['description']): ?>
        <div style="font-size:11px;color:#64748b;margin-bottom:12px"><?=h($row['description'])?></div>
      <?php endif; ?>
      <?php if($features): ?>
        <ul style="font-size:11px;color:#8892a4;line-height:1.8;padding-left:14px;margin-bottom:12px">
          <?php foreach(array_slice($features,0,4) as $f): ?>
            <li><?=h($f)?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if($row['cta_text']): ?>
        <a href="<?=h($row['cta_link']??'#contact')?>" class="tag" style="display:block;text-align:center;background:rgba(0,0,0,.3);border-color:var(--<?=$accent?>);color:var(--<?=$accent?>);font-size:11px;margin-bottom:12px;text-decoration:none"><?=h($row['cta_text'])?></a>
      <?php endif; ?>
      <div style="display:flex;gap:6px;justify-content:flex-end">
        <a href="services_about.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">✏️</a>
        <form method="POST" action="services_about.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')" style="display:inline">
          <?=csrfField()?>
          <button class="btn btn-danger btn-sm" type="submit">🗑</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ======================= -->
<!-- ADD/EDIT MODAL -->
<!-- ======================= -->
<?php if($editRow || $action === 'add'): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this)window.location='services_about.php'">
  <div style="background:#161b27;border:1px solid #1e2638;border-radius:16px;padding:28px;max-width:580px;width:100%;max-height:90vh;overflow-y:auto" onclick="event.stopPropagation()">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
      <h3 style="font-size:16px;font-weight:700;color:#fff;margin:0">
        <?=$editRow ? '✏️ Edit Service' : '➕ Add ' . (isset($_GET['type']) && $_GET['type'] === 'pricing' ? 'Digital Service' : 'Service')?>
      </h3>
      <a href="services_about.php" style="color:#64748b;font-size:20px;text-decoration:none;padding:4px">✕</a>
    </div>

    <?php if(!$editRow && isset($_GET['type']) && $_GET['type'] === 'pricing'): ?>
    <div style="background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);border-radius:10px;padding:14px;margin-bottom:20px;font-size:12px;color:#67e8f9">
      💰 Adding a <strong>Digital Service</strong> with price, features list, and CTA button.
    </div>
    <?php elseif(!$editRow): ?>
    <div style="background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.2);border-radius:10px;padding:14px;margin-bottom:20px;font-size:12px;color:#a78bfa">
      🎯 Adding a <strong>Service I Offer</strong> — simple icon card for About section.
    </div>
    <?php endif; ?>

    <form method="POST" action="services_about.php?action=<?=$editRow?'edit&id='.$id:'add'?><?=isset($_GET['type'])?'&type='.$_GET['type']:''?>">
      <?=csrfField()?>
      
      <div class="grid-3">
        <div>
          <label>Service Name *</label>
          <input type="text" name="name" value="<?=h($editRow['name']??'')?>" required placeholder="Web Development">
        </div>
        <div>
          <label>Icon (e.g. globe, code)</label>
          <input type="text" name="icon" value="<?=h($editRow['icon']??'globe')?>" placeholder="globe">
        </div>
        <div>
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
        </div>
      </div>

      <label style="margin-top:14px;display:block">Short Description</label>
      <input type="text" name="description" value="<?=h($editRow['description']??'')?>" placeholder="Brief description">

      <div style="margin-top:16px;padding:14px;background:#0f1420;border:1px solid #2a3347;border-radius:10px">
        <label style="display:flex;align-items:flex-start;cursor:pointer;gap:10px">
          <input type="checkbox" name="is_pricing" id="isPricingCheck" value="1" <?=(!empty($editRow['is_pricing']))?'checked':''?> style="margin-top:3px" onchange="togglePricingFields()" />
          <div>
            <strong style="color:#22d3ee;font-size:13px">💰 Digital Service (with pricing)</strong>
            <div style="font-size:11px;color:#64748b;margin-top:4px">Check this for pricing card (price, features, CTA)</div>
          </div>
        </label>
      </div>

      <div id="pricingFields" style="<?=empty($editRow['is_pricing'])?'display:none':''>;margin-top:16px;padding:16px;background:#0f1420;border:1px solid #2a3347;border-radius:10px">
        <div class="grid-3">
          <div>
            <label>Price</label>
            <input type="text" name="price" value="<?=h($editRow['price']??'')?>" placeholder="15000">
          </div>
          <div>
            <label>Price Unit</label>
            <input type="text" name="price_unit" value="<?=h($editRow['price_unit']??'')?>" placeholder="NPR /year">
          </div>
          <div>
            <label>Accent Color</label>
            <select name="accent_color">
              <?php foreach(['cyan','violet','yellow','red','amber','green'] as $c): ?>
                <option value="<?=$c?>" <?=($editRow['accent_color']??'cyan')===$c?'selected':''?>><?=ucfirst($c)?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <label style="margin-top:14px;display:block">Features (one per line)</label>
        <textarea name="features" rows="4" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"><?=h(
          isset($editRow['features']) && $editRow['features']
            ? implode("\n", json_decode($editRow['features'], true) ?? [])
            : ''
        )?></textarea>
        <div class="grid-2" style="margin-top:14px">
          <div>
            <label>CTA Button Text</label>
            <input type="text" name="cta_text" value="<?=h($editRow['cta_text']??'')?>" placeholder="Request a Quote →">
          </div>
          <div>
            <label>CTA Link</label>
            <input type="text" name="cta_link" value="<?=h($editRow['cta_link']??'#contact')?>" placeholder="#contact">
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-top:24px">
        <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
        <a href="services_about.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
function togglePricingFields() {
  var checked = document.getElementById('isPricingCheck').checked;
  document.getElementById('pricingFields').style.display = checked ? '' : 'none';
}
</script>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
