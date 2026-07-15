<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Services';
$pageSubtitle = 'Manage About section icons AND Digital Services pricing cards.';

$msg = ''; $msgType = 'success';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Load FIRST (needed for POST handler + form display)
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
$list = dbRows("SELECT * FROM services_about ORDER BY sort_order, id");
include __DIR__ . '/header.php';
?>

<?php if(isset($_GET['deleted'])): ?><div class="alert alert-success">✅ Service deleted.</div><?php endif; ?>
<?php if($msg==='added'): ?><div class="alert alert-success">✅ Service added!</div><?php endif; ?>
<?php if($msg==='updated'): ?><div class="alert alert-success">✅ Service updated!</div><?php endif; ?>

<div class="card" style="margin-bottom:10px;background:#0f1420;border-color:#2a3347;font-size:12px;color:#64748b">
  💡 <strong style="color:#c9d1e3">Icon examples:</strong> globe, envelope, code, server, database, chart-line, mobile-alt, shield-alt, laptop-code, cloud, chalkboard-teacher<br>
  💡 <strong style="color:#c9d1e3">Accent colors:</strong> cyan, violet, yellow, red, amber, green
</div>

<div class="card">
  <div class="section-heading"><?=$editRow?'✏️ Edit Service':'➕ Add New Service'?></div>
  <form method="POST" action="services_about.php?action=<?=$editRow?'edit&id='.$id:'add'?>">
    <?=csrfField()?>
    <div class="grid-3">
      <div>
        <label>Service Name *</label>
        <input type="text" name="name" value="<?=h($editRow['name']??'')?>" required placeholder="Web Development">
      </div>
      <div>
        <label>Icon (Font Awesome, without fa-)</label>
        <input type="text" name="icon" value="<?=h($editRow['icon']??'globe')?>" placeholder="globe">
      </div>
      <div>
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="<?=h($editRow['sort_order']??'0')?>">
      </div>
    </div>

    <label>Short Description</label>
    <input type="text" name="description" value="<?=h($editRow['description']??'')?>" placeholder="Advanced UI/UX focused development">

    <div class="divider"></div>
    <div class="form-group">
      <label>
        <input type="checkbox" name="is_pricing" id="isPricingCheck" value="1" <?=(!empty($editRow['is_pricing']))?'checked':''?> style="width:auto;margin-right:8px" />
        <strong>Digital Services Card</strong> (with price, features list & CTA — shown in Digital Services section)
      </label>
    </div>

    <div id="pricingFields" style="<?=empty($editRow['is_pricing'])?'display:none':''?>">
      <div class="grid-3">
        <div>
          <label>Price (e.g. 15000)</label>
          <input type="text" name="price" value="<?=h($editRow['price']??'')?>" placeholder="15000">
        </div>
        <div>
          <label>Price Unit (e.g. NPR, /year, per person)</label>
          <input type="text" name="price_unit" value="<?=h($editRow['price_unit']??'')?>" placeholder="NPR 15,000 /year">
        </div>
        <div>
          <label>Accent Color</label>
          <select name="accent_color">
            <?php foreach(['cyan','violet','yellow','red','amber','green'] as $c): ?>
              <option value="<?=$c?>" <?=($editRow['accent_color']??'')===$c?'selected':''?>><?=ucfirst($c)?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <label>Features (one per line)</label>
      <textarea name="features" rows="5" placeholder="Responsive design&#10;SEO optimized&#10;Free support"><?=h(
        isset($editRow['features']) && $editRow['features']
          ? implode("\n", json_decode($editRow['features'], true) ?? [])
          : ''
      )?></textarea>
      <div class="grid-2" style="margin-top:12px">
        <div>
          <label>CTA Button Text</label>
          <input type="text" name="cta_text" value="<?=h($editRow['cta_text']??'')?>" placeholder="Request a Quote →">
        </div>
        <div>
          <label>CTA Button Link</label>
          <input type="text" name="cta_link" value="<?=h($editRow['cta_link']??'#contact')?>" placeholder="#contact">
        </div>
      </div>
    </div>

    <div style="margin-top:14px;display:flex;gap:8px">
      <button class="btn btn-primary" type="submit">💾 <?=$editRow?'Update':'Add'?></button>
      <?php if($editRow): ?><a href="services_about.php" class="btn btn-secondary">Cancel</a><?php endif;?>
    </div>
  </form>
</div>

<script>
document.getElementById('isPricingCheck').addEventListener('change', function() {
  document.getElementById('pricingFields').style.display = this.checked ? '' : 'none';
});
</script>

<div class="card">
  <div class="section-heading-sm">All Services (<?=count($list)?>)</div>
  <?php if(!$list): ?>
    <p style="color:#64748b;font-size:13px">No services yet.</p>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px">
    <?php foreach($list as $row):
      $isPricing = !empty($row['is_pricing']);
      $accent = h($row['accent_color'] ?? 'cyan');
      $features = $isPricing && $row['features'] ? json_decode($row['features'], true) : [];
    ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-left:3px solid var(--<?=$accent?>);border-radius:10px;padding:16px">
      <?php if($isPricing): ?>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
          <i class="fa fa-<?=h($row['icon'])?>" style="color:var(--<?=$accent?>);font-size:22px"></i>
          <?php if($row['price']): ?>
            <span style="font-size:20px;font-weight:900;color:var(--<?=$accent?>)"><?=h($row['price'])?><?php if($row['price_unit']): ?> <small style="font-size:11px;font-weight:400"><?=h($row['price_unit'])?></small><?php endif; ?></span>
          <?php endif; ?>
        </div>
        <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:4px"><?=h($row['name'])?></div>
        <div style="font-size:11px;color:#64748b;margin-bottom:10px"><?=h($row['description'])?></div>
        <?php if($features): ?>
          <ul style="font-size:11px;color:#64748b;line-height:1.8;padding-left:14px;margin-bottom:12px">
            <?php foreach(array_slice($features,0,5) as $f): ?>
              <li><?=h($f)?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <?php if($row['cta_text']): ?>
          <a href="<?=h($row['cta_link']??'#contact')?>" class="tag" style="display:block;text-align:center;background:rgba(0,0,0,.2);border-color:var(--<?=$accent?>);color:var(--<?=$accent?>);font-size:11px"><?=h($row['cta_text'])?></a>
        <?php endif; ?>
      <?php else: ?>
        <div style="text-align:center">
          <i class="fa fa-<?=h($row['icon'])?>" style="color:#22d3ee;font-size:22px;margin-bottom:8px;display:block"></i>
          <div style="font-size:12px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px"><?=h($row['name'])?></div>
          <div style="font-size:11px;color:#64748b"><?=h($row['description'])?></div>
        </div>
      <?php endif; ?>
      <div style="display:flex;gap:6px;margin-top:12px;justify-content:flex-end">
        <a href="services_about.php?action=edit&id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Edit</a>
        <form method="POST" action="services_about.php?action=delete&id=<?=$row['id']?>" onsubmit="return confirm('Delete?')">
          <?=csrfField()?>
          <button class="btn btn-danger btn-sm" type="submit">✕</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php';
