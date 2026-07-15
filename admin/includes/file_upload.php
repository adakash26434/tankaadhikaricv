<?php
/**
 * Shared file upload helpers for admin pages.
 * Include: require_once __DIR__ . '/includes/file_upload.php';
 */

function handleAdminUpload(?array $file, string $type, string $existing = "", string $prefix = "file", string $subfolder = "img/"): array {
    $baseDir = __DIR__ . "/../";
    $hasNew  = is_array($file) && ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && !empty($file["name"]);
    if ($hasNew) {
        $mime = mime_content_type($file["tmp_name"] ?? "");
        if ($type === "image") { $allowed = ["image/jpeg","image/jpg","image/png","image/webp","image/gif"]; }
        else                   { $allowed = ["application/pdf"]; }
        if (!in_array($mime, $allowed, true)) {
            return ["path" => $existing, "error" => "Invalid file type. " . ($type === "image" ? "Only JPEG, PNG, WebP, GIF allowed." : "Only PDF allowed.")];
        }
        if (($file["size"] ?? 0) > 5 * 1024 * 1024) {
            return ["path" => $existing, "error" => "File too large. Maximum size is 5 MB."];
        }
        $safeName = preg_replace("/[^a-z0-9_\-]/i", "_", pathinfo($file["name"], PATHINFO_FILENAME));
        $safeName = strtolower(substr($safeName, 0, 50));
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $filename = $prefix . "_" . $safeName . "_" . time() . "." . $ext;
        $destDir  = $baseDir . $subfolder;
        if (!is_dir($destDir)) { mkdir($destDir, 0755, true); }
        if (!move_uploaded_file($file["tmp_name"] ?? "", $destDir . $filename)) {
            return ["path" => $existing, "error" => "Failed to save file. Check folder permissions (chmod 755 on " . $subfolder . ")."];
        }
        return ["path" => $subfolder . $filename, "error" => null];
    }
    return ["path" => $existing, "error" => null];
}

/** Handle removal: POST["remove_FIELDNAME"] == "1" clears existing path. */
function handleRemove(string $fieldName, string $existing): string {
    if (isset($_POST["remove_" . $fieldName]) && $_POST["remove_" . $fieldName] === "1") { return ""; }
    return $existing;
}

/**
 * Render a single-file upload field with existing file preview.
 * Uses data attributes + footer.js for remove functionality.
 */
function renderUploadField(string $label, string $fieldName, ?string $currentPath, string $type = "image"): void {
    $accepted    = ($type === "pdf") ? "application/pdf" : "image/jpeg,image/png,image/webp,image/gif";
    $fileInputId = "file_" . preg_replace("/[^a-z0-9]/i", "_", $fieldName);
    $safeField   = preg_replace("/[^a-zA-Z0-9]/i", "_", $fieldName);
    $hiddenId    = "r_" . $safeField;
    ?>
<label for="<?php echo $fileInputId; ?>"><?php echo htmlspecialchars($label); ?></label>
<input type="file" id="<?php echo $fileInputId; ?>" name="<?php echo htmlspecialchars($fieldName); ?>" accept="<?php echo htmlspecialchars($accepted); ?>" style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:9px 12px;font-size:13px;cursor:pointer">
<?php if (!empty($currentPath)): ?>
<div class="file-preview" style="margin-top:6px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
<?php if ($type === "image"): ?>
<img src="../<?php echo htmlspecialchars($currentPath); ?>" style="width:60px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #1e2638" alt="Current" />
<?php else: ?>
<span style="font-size:12px;color:#67e8f9">&#128196; <?php echo htmlspecialchars(basename($currentPath)); ?></span>
<?php endif; ?>
<a href="../<?php echo htmlspecialchars($currentPath); ?>" target="_blank" class="btn btn-secondary" style="font-size:11px;padding:3px 8px">View</a>
<button type="button" class="btn btn-danger" style="font-size:11px;padding:3px 8px" data-hidden="<?php echo $hiddenId; ?>" onclick="removeCurrentFile(this)">&#10005; Remove</button>
<input type="hidden" name="remove_<?php echo htmlspecialchars($fieldName); ?>" id="<?php echo $hiddenId; ?>" value="">
</div>
<?php endif;
}

/** Render a multi-file upload field. */
function renderMultiUploadField(string $label, string $fieldName, array $currentPaths = [], string $type = "image"): void {
    $accepted   = ($type === "pdf") ? "application/pdf" : "image/jpeg,image/png,image/webp,image/gif";
    $fileInputId = "files_" . preg_replace("/[^a-z0-9]/i", "_", $fieldName);
    $previewId  = "prev_" . preg_replace("/[^a-zA-Z0-9]/i", "_", $fieldName);
    $removedId  = "rem_" . preg_replace("/[^a-zA-Z0-9]/i", "_", $fieldName);
    ?>
<label><?php echo htmlspecialchars($label); ?> <span style="color:#64748b;font-weight:400">(select multiple files)</span></label>
<input type="file" id="<?php echo $fileInputId; ?>" name="<?php echo htmlspecialchars($fieldName); ?>[]" accept="<?php echo htmlspecialchars($accepted); ?>" multiple style="width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:9px 12px;font-size:13px;cursor:pointer">
<div id="<?php echo $previewId; ?>" class="multi-preview" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px">
<?php if (!empty($currentPaths)): foreach ($currentPaths as $p): ?>
<div class="thumb-item" style="position:relative;display:inline-block">
<?php if ($type === "image"): ?>
<img src="../<?php echo htmlspecialchars($p); ?>" style="width:60px;height:45px;object-fit:cover;border-radius:6px;border:1px solid #1e2638" />
<?php else: ?>
<div style="width:60px;height:45px;background:#1e2638;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:20px">&#128196;</div>
<?php endif; ?>
<button type="button" class="rm-multi-btn" style="position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:10px;cursor:pointer;line-height:18px;text-align:center" data-field="<?php echo htmlspecialchars($fieldName); ?>" data-path="<?php echo htmlspecialchars($p); ?>" data-removed="<?php echo $removedId; ?>">&#10005;</button>
</div>
<?php endforeach; endif; ?>
</div>
<input type="hidden" name="removed_<?php echo htmlspecialchars($fieldName); ?>" id="<?php echo $removedId; ?>" value="">
<?php
}
