<?php if(!defined('ADMIN_PAGE')) die('Direct access not allowed'); ?>
</div><!-- /main -->
<div style="text-align:center;padding:20px;color:#334155;font-size:12px">
  Portfolio Admin &copy; <?=date('Y')?> — Aakash Digital Pvt. Ltd.
</div>
<script>
// Remove current file — sets hidden input then hides preview row
function removeCurrentFile(btn) {
    var row    = btn.closest('.file-preview');
    var target = btn.getAttribute('data-target');
    var hidden = document.getElementById('remove_' + target);
    if (hidden) hidden.value = '1';
    if (row) row.style.display = 'none';
}

// Remove a file from multi-upload list
function removeMultiFile(btn, fieldName, path) {
    var removedField = document.getElementById('removed_' + fieldName);
    if (!removedField) return;
    var removed = removedField.value ? JSON.parse(removedField.value) : [];
    if (!removed.includes(path)) removed.push(path);
    removedField.value = JSON.stringify(removed);
    var item = btn.closest('.thumb-item');
    if (item) item.style.display = 'none';
}
</script>
</body>
</html>
