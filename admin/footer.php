<?php if(!defined('ADMIN_PAGE')) die('Direct access not allowed'); ?>
</div><!-- end .main -->
<div style="text-align:center;padding:20px;color:#334155;font-size:12px">
  Portfolio Admin &copy; <?=date('Y')?> — Aakash Digital Pvt. Ltd.
</div>
<script>
function toggleMobileNav() {
    var nav = document.getElementById('mobileNav');
    if (!nav) return;
    nav.classList.toggle('open');
    document.body.style.overflow = nav.classList.contains('open') ? 'hidden' : '';
}
// Close mobile nav when clicking overlay background
document.getElementById('mobileNav')?.addEventListener('click', function(e) {
    if (e.target === this) toggleMobileNav();
});
// Remove current file — button has data-hidden="r_SAFEID"
function removeCurrentFile(btn) {
    var row = btn.closest('.file-preview');
    var hiddenId = btn.getAttribute('data-hidden');
    if (hiddenId) {
        var hidden = document.getElementById(hiddenId);
        if (hidden) hidden.value = '1';
    }
    if (row) row.style.display = 'none';
}
// Remove a file from multi-upload list
document.querySelectorAll('.rm-multi-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var fieldName = btn.getAttribute('data-field');
        var path = btn.getAttribute('data-path');
        var removedId = btn.getAttribute('data-removed');
        var removedField = document.getElementById(removedId);
        if (!removedField) return;
        var removed = removedField.value ? JSON.parse(removedField.value) : [];
        if (!removed.includes(path)) removed.push(path);
        removedField.value = JSON.stringify(removed);
        var item = btn.closest('.thumb-item');
        if (item) item.style.display = 'none';
    });
});
</script>
</body>
</html>
