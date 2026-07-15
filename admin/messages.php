<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Contact Messages';
$pageSubtitle = 'Messages received through the contact form.';

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $postAction = $_POST['action'] ?? '';
    $postId = (int)($_POST['id'] ?? 0);
    if ($postAction === 'delete' && $postId) {
        getDB()->prepare("DELETE FROM messages WHERE id=?")->execute([$postId]);
        header('Location: messages.php?deleted=1'); exit;
    }
    if ($postAction === 'read' && $postId) {
        getDB()->prepare("UPDATE messages SET is_read=1 WHERE id=?")->execute([$postId]);
        header('Location: messages.php?id=' . $postId); exit;
    }
}

if ($action === 'read' && $id) {
    getDB()->prepare("UPDATE messages SET is_read=1 WHERE id=?")->execute([$id]);
}

$view = $id ? dbRow("SELECT * FROM messages WHERE id=?", [$id]) : null;
if ($view && !$view['is_read']) {
    getDB()->prepare("UPDATE messages SET is_read=1 WHERE id=?")->execute([$id]);
}
$list = dbRows("SELECT * FROM messages ORDER BY created_at DESC");
include __DIR__ . '/header.php';
?>
<?php if(isset($_GET['deleted'])): ?><div class="alert-success">✅ Message deleted.</div><?php endif; ?>

<?php if($view): ?>
<div class="card" style="margin-bottom:16px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <div style="font-size:13px;font-weight:700;color:#fff">📨 Message from <?=h($view['name'])?></div>
    <a href="messages.php" class="btn btn-secondary" style="font-size:12px">← Back</a>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;font-size:13px">
    <div><span style="color:#64748b">Name:</span> <strong style="color:#fff"><?=h($view['name'])?></strong></div>
    <div><span style="color:#64748b">Email:</span> <a href="mailto:<?=h($view['email'])?>"><?=h($view['email'])?></a></div>
    <div><span style="color:#64748b">Subject:</span> <span style="color:#c9d1e3"><?=h($view['subject']?:'—')?></span></div>
    <div><span style="color:#64748b">Date:</span> <span style="color:#c9d1e3"><?=date('d M Y, H:i', strtotime($view['created_at']))?></span></div>
  </div>
  <div style="background:#0f1420;border:1px solid #1e2638;border-radius:8px;padding:16px;color:#c9d1e3;font-size:14px;line-height:1.7;white-space:pre-wrap"><?=h($view['message'])?></div>
  <div style="margin-top:14px;display:flex;gap:8px">
    <a href="mailto:<?=h($view['email'])?>" class="btn btn-primary">📧 Reply via Email</a>
    <form method="POST" action="messages.php" onsubmit="return confirm('Delete this message permanently?')">
      <?=csrfField()?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?=(int)$view['id']?>">
      <button class="btn btn-danger" type="submit">🗑 Delete</button>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="section-heading-sm">All Messages (<?=count($list)?>)</div>
  <?php if(!$list): ?><p style="color:#64748b;font-size:13px">No messages yet.</p><?php else: ?>
  <table>
    <thead><tr><th>From</th><th>Subject / Message</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $row): ?>
    <tr style="<?=!$row['is_read']?'background:rgba(34,211,238,.03)':''?>">
      <td>
        <strong style="color:<?=!$row['is_read']?'#fff':'#8892a4'?>"><?=h($row['name'])?></strong>
        <br><span style="color:#64748b;font-size:11px"><?=h($row['email'])?></span>
        <?php if(!$row['is_read']): ?><br><span class="badge" style="margin-top:3px">New</span><?php endif; ?>
      </td>
      <td style="color:#8892a4;max-width:300px">
        <?php if($row['subject']): ?><strong style="color:#c9d1e3;font-size:12px"><?=h($row['subject'])?></strong><br><?php endif; ?>
        <span style="font-size:12px"><?=h(mb_substr($row['message'],0,80)).(mb_strlen($row['message'])>80?'…':'')?></span>
      </td>
      <td style="color:#64748b;font-size:11px;white-space:nowrap"><?=date('d M Y', strtotime($row['created_at']))?></td>
      <td>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <a href="messages.php?id=<?=$row['id']?>" class="btn btn-secondary btn-sm">View</a>
          <form method="POST" action="messages.php" onsubmit="return confirm('Delete?')">
            <?=csrfField()?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?=(int)$row['id']?>">
            <button class="btn btn-danger btn-sm" type="submit">Del</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>
