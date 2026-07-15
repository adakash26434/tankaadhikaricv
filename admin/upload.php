<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Image & File Uploads';
$pageSubtitle = 'Upload images to img/ folder and files to files/ folder.';

$msg = '';
$msgType = 'success';

$allowedImages = ['image/jpeg','image/jpg','image/png','image/webp','image/gif'];
$allowedFiles  = ['application/pdf'];
$maxSize = 5 * 1024 * 1024; // 5MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $uploadType = $_POST['upload_type'] ?? 'image';
    $file = $_FILES['upload_file'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File too large (server limit).',
            UPLOAD_ERR_FORM_SIZE  => 'File too large (form limit).',
            UPLOAD_ERR_NO_FILE    => 'No file selected.',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporary folder missing.',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk.',
        ];
        $msg = $errors[$file['error'] ?? 4] ?? 'Upload failed.';
        $msgType = 'error';
    } else {
        $mime = mime_content_type($file['tmp_name']);
        $size = $file['size'];

        if ($size > $maxSize) {
            $msg = 'File too large. Maximum size is 5 MB.';
            $msgType = 'error';
        } elseif ($uploadType === 'image' && !in_array($mime, $allowedImages)) {
            $msg = 'Only JPEG, PNG, WebP, or GIF images are allowed.';
            $msgType = 'error';
        } elseif ($uploadType === 'file' && !in_array($mime, $allowedFiles)) {
            $msg = 'Only PDF files are allowed in the files/ folder.';
            $msgType = 'error';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-z0-9_\-]/i', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $safeName = strtolower(substr($safeName, 0, 60));
            $filename = $safeName . '_' . time() . '.' . strtolower($ext);

            $destDir  = $uploadType === 'image'
                ? __DIR__ . '/../img/'
                : __DIR__ . '/../files/';
            $destPath = $destDir . $filename;
            $webPath  = ($uploadType === 'image' ? 'img/' : 'files/') . $filename;

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $msg = 'Uploaded successfully! Path: <strong>' . htmlspecialchars($webPath) . '</strong> — copy this path and paste it into the relevant admin field.';
                $msgType = 'success';
            } else {
                $msg = 'Failed to move file. Check folder permissions (chmod 755 on img/ and files/).';
                $msgType = 'error';
            }
        }
    }
}

// List existing files
function listDir(string $dir, string $prefix): array {
    if (!is_dir($dir)) return [];
    $files = [];
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $full = $dir . $f;
        if (is_file($full)) {
            $files[] = [
                'name' => $f,
                'path' => $prefix . $f,
                'size' => filesize($full),
                'time' => filemtime($full),
                'type' => mime_content_type($full),
            ];
        }
    }
    usort($files, fn($a,$b) => $b['time'] <=> $a['time']);
    return $files;
}

$images = listDir(__DIR__ . '/../img/', 'img/');
$pdfs   = listDir(__DIR__ . '/../files/', 'files/');

include __DIR__ . '/header.php';
?>

<?php if($msg): ?>
<div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>">
  <?=$msg?>
</div>
<?php endif; ?>

<div class="grid-2" style="margin-bottom:20px">

  <!-- Upload Image -->
  <div class="card">
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px">📷 Upload Image</div>
    <form method="POST" enctype="multipart/form-data" action="upload.php">
      <?=csrfField()?>
      <input type="hidden" name="upload_type" value="image">
      <label for="img-file">Choose image (JPEG, PNG, WebP — max 5 MB)</label>
      <input type="file" id="img-file" name="upload_file" accept="image/jpeg,image/png,image/webp,image/gif" required
             style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:9px 12px;font-size:13px;cursor:pointer">
      <div style="margin-top:12px">
        <button class="btn btn-primary" type="submit">⬆️ Upload Image</button>
      </div>
    </form>
  </div>

  <!-- Upload PDF -->
  <div class="card">
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px">📄 Upload PDF File</div>
    <form method="POST" enctype="multipart/form-data" action="upload.php">
      <?=csrfField()?>
      <input type="hidden" name="upload_type" value="file">
      <label for="pdf-file">Choose PDF file (CV, Certificate, Research — max 5 MB)</label>
      <input type="file" id="pdf-file" name="upload_file" accept="application/pdf" required
             style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:9px 12px;font-size:13px;cursor:pointer">
      <div style="margin-top:12px">
        <button class="btn btn-primary" type="submit">⬆️ Upload PDF</button>
      </div>
    </form>
  </div>

</div>

<div style="background:#0f1420;border:1px solid #2a3347;border-radius:10px;padding:14px 18px;font-size:12px;color:#64748b;margin-bottom:20px">
  💡 <strong style="color:#c9d1e3">How to use:</strong> Upload a photo, then copy the path (e.g. <code style="background:#161b27;padding:2px 6px;border-radius:4px;color:#22d3ee">img/myphoto.jpg</code>) and paste it into the corresponding field in Awards, Projects, News, Profile, etc.
</div>

<!-- Images Gallery -->
<div class="card" style="margin-bottom:20px">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px">🖼️ Images in img/ folder (<?=count($images)?>)</div>
  <?php if(!$images): ?>
    <p style="color:#64748b;font-size:13px">No images uploaded yet.</p>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
    <?php foreach($images as $img): ?>
    <div style="background:#0f1420;border:1px solid #1e2638;border-radius:8px;overflow:hidden">
      <?php if(strpos($img['type'],'image')===0): ?>
      <img src="../<?=htmlspecialchars($img['path'])?>" alt="<?=htmlspecialchars($img['name'])?>"
           style="width:100%;height:100px;object-fit:cover;display:block" loading="lazy" />
      <?php else: ?>
      <div style="width:100%;height:100px;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:24px">📄</div>
      <?php endif; ?>
      <div style="padding:8px">
        <div style="font-size:10px;color:#c9d1e3;margin-bottom:4px;word-break:break-all;line-height:1.4"><?=htmlspecialchars($img['name'])?></div>
        <div style="font-size:10px;color:#64748b;margin-bottom:6px"><?=round($img['size']/1024,1)?> KB</div>
        <button onclick="copyPath('<?=htmlspecialchars($img['path'])?>')" class="btn btn-secondary"
                style="font-size:10px;padding:3px 8px;width:100%">📋 Copy Path</button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- PDFs -->
<div class="card">
  <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:14px">📁 Files in files/ folder (<?=count($pdfs)?>)</div>
  <?php if(!$pdfs): ?>
    <p style="color:#64748b;font-size:13px">No files uploaded yet.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>Filename</th><th>Size</th><th>Date</th><th>Path</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($pdfs as $f): ?>
    <tr>
      <td style="color:#fff">📄 <?=htmlspecialchars($f['name'])?></td>
      <td style="color:#64748b"><?=round($f['size']/1024,1)?> KB</td>
      <td style="color:#64748b;font-size:11px"><?=date('d M Y', $f['time'])?></td>
      <td><code style="background:#0f1420;padding:2px 6px;border-radius:4px;font-size:11px;color:#22d3ee"><?=htmlspecialchars($f['path'])?></code></td>
      <td style="display:flex;gap:6px">
        <button onclick="copyPath('<?=htmlspecialchars($f['path'])?>')" class="btn btn-secondary btn-sm">📋 Copy</button>
        <a href="../<?=htmlspecialchars($f['path'])?>" target="_blank" class="btn btn-secondary btn-sm">View ↗</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div id="copy-toast" style="position:fixed;bottom:24px;right:24px;background:#22d3ee;color:#0f1420;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:700;display:none;z-index:9999">
  ✅ Path copied to clipboard!
</div>

<script>
function copyPath(path) {
  navigator.clipboard.writeText(path).then(() => {
    const toast = document.getElementById('copy-toast');
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 2500);
  });
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
