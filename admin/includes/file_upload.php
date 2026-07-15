<?php
/**
 * Shared file upload helpers for admin pages.
 * Include: require_once __DIR__ . '/includes/file_upload.php';
 *
 * Functions:
 *   handleAdminUpload($file, $type, $existing, $prefix, $subfolder)
 *   renderUploadField($label, $fieldName, $currentPath, $type, $subfolder)
 */

/**
 * Handle file upload with validation and existing file management.
 *
 * @param array|null $file         $_FILES['fieldname'] entry
 * @param string     $type         'image' or 'pdf'
 * @param string     $existing     Path currently in DB
 * @param string     $prefix       Filename prefix (award, news, project, etc.)
 * @param string     $subfolder    'img/' or 'files/'
 * @return array ['path' => string, 'error' => string|null]
 */
function handleAdminUpload(?array $file, string $type, string $existing = '', string $prefix = 'file', string $subfolder = 'img/'): array {
    $baseDir = __DIR__ . '/../';

    // Determine the remove checkbox name from field name
    $removeName = 'remove_';
    if (is_array($file) && !empty($file['name'])) {
        $removeName .= preg_replace('/[^a-z0-9]/i', '_', $file['name']);
    }

    $remove = isset($_POST[$removeName]) && $_POST[$removeName] === '1';
    $hasNew = is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && !empty($file['name']);

    if ($hasNew) {
        $mime = mime_content_type($file['tmp_name'] ?? '');
        if ($type === 'image') {
            $allowed = ['image/jpeg','image/jpg','image/png','image/webp','image/gif'];
        } else {
            $allowed = ['application/pdf'];
        }
        if (!in_array($mime, $allowed, true)) {
            return [
                'path'  => $existing,
                'error' => 'Invalid file type. ' . ($type === 'image' ? 'Only JPEG, PNG, WebP, GIF allowed.' : 'Only PDF allowed.')
            ];
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return ['path' => $existing, 'error' => 'File too large. Maximum size is 5 MB.'];
        }
        $safeName = preg_replace('/[^a-z0-9_\-]/i', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $safeName = strtolower(substr($safeName, 0, 50));
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = $prefix . '_' . $safeName . '_' . time() . '.' . $ext;
        $destDir = $baseDir . $subfolder;
        if (!is_dir($destDir)) { mkdir($destDir, 0755, true); }
        if (!move_uploaded_file($file['tmp_name'] ?? '', $destDir . $filename)) {
            return [
                'path'  => $existing,
                'error' => 'Failed to save file. Check folder permissions (chmod 755 on ' . $subfolder . ').'
            ];
        }
        return ['path' => $subfolder . $filename, 'error' => null];
    }

    if ($remove) {
        return ['path' => '', 'error' => null];
    }

    return ['path' => $existing, 'error' => null];
}

/**
 * Render a file upload field with existing file preview.
 *
 * @param string      $label          Label text
 * @param string      $fieldName      Form field name (e.g. 'image1')
 * @param string|null $currentPath    Path from DB (null = add mode)
 * @param string      $type           'image' or 'pdf'
 */
function renderUploadField(string $label, string $fieldName, ?string $currentPath, string $type = 'image'): void {
    $accepted = $type === 'pdf' ? 'application/pdf' : 'image/jpeg,image/png,image/webp,image/gif';
    $fileInputId = 'file_' . preg_replace('/[^a-z0-9]/i', '_', $fieldName);
    $removeName = 'remove_' . $fieldName;
    ?>
    <label for="<?=htmlspecialchars($fileInputId)?>"><?=htmlspecialchars($label)?></label>
    <input type="file" id="<?=htmlspecialchars($fileInputId)?>" name="<?=htmlspecialchars($fieldName)?>" accept="<?=htmlspecialchars($accepted)?>"
           style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:9px 12px;font-size:13px;cursor:pointer">
    <?php if (!empty($currentPath) && $type === 'image'): ?>
      <div style="margin-top:6px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <img src="../<?=htmlspecialchars($currentPath)?>" style="width:60px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #1e2638" alt="Current" />
        <a href="../<?=htmlspecialchars($currentPath)?>" target="_blank" class="btn btn-secondary" style="font-size:11px;padding:3px 8px">View ↗</a>
        <label style="font-size:11px;color:#64748b;cursor:pointer">
          <input type="checkbox" name="<?=htmlspecialchars($removeName)?>" value="1" style="cursor:pointer"> Remove
        </label>
      </div>
    <?php elseif (!empty($currentPath) && $type === 'pdf'): ?>
      <div style="margin-top:6px;display:flex;align-items:center;gap:8px">
        <span style="font-size:12px;color:#67e8f9">📄 <?=htmlspecialchars($currentPath)?></span>
        <a href="../<?=htmlspecialchars($currentPath)?>" target="_blank" class="btn btn-secondary" style="font-size:11px;padding:3px 8px">View ↗</a>
        <label style="font-size:11px;color:#64748b;cursor:pointer">
          <input type="checkbox" name="<?=htmlspecialchars($removeName)?>" value="1" style="cursor:pointer"> Remove
        </label>
      </div>
    <?php endif;
}
