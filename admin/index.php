<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();
$pageTitle = 'Dashboard';
$pageSubtitle = 'Manage all portfolio content from here.';

$counts = [
  'education'  => dbRow("SELECT COUNT(*) as c FROM education")['c'] ?? 0,
  'experience' => dbRow("SELECT COUNT(*) as c FROM experience")['c'] ?? 0,
  'training'   => dbRow("SELECT COUNT(*) as c FROM training")['c'] ?? 0,
  'awards'     => dbRow("SELECT COUNT(*) as c FROM awards")['c'] ?? 0,
  'research'   => dbRow("SELECT COUNT(*) as c FROM research")['c'] ?? 0,
  'news'       => dbRow("SELECT COUNT(*) as c FROM news")['c'] ?? 0,
  'skills'     => dbRow("SELECT COUNT(*) as c FROM skills")['c'] ?? 0,
  'projects'   => dbRow("SELECT COUNT(*) as c FROM projects")['c'] ?? 0,
  'portfolio'  => dbRow("SELECT COUNT(*) as c FROM portfolio_sites")['c'] ?? 0,
  'services'   => dbRow("SELECT COUNT(*) as c FROM services_about")['c'] ?? 0,
  'interests'  => dbRow("SELECT COUNT(*) as c FROM interests")['c'] ?? 0,
  'messages'   => dbRow("SELECT COUNT(*) as c FROM messages WHERE is_read=0")['c'] ?? 0,
  'totalMsg'   => dbRow("SELECT COUNT(*) as c FROM messages")['c'] ?? 0,
];

include __DIR__ . '/header.php';
?>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:28px">
<?php
$cards = [
  ['👤','Profile','Edit profile','profile.php','rgba(139,92,246,.08)'],
  ['🎓','Education',$counts['education'].' entries','education.php','rgba(34,211,238,.08)'],
  ['🏢','Experience',$counts['experience'].' positions','experience.php','rgba(34,211,238,.08)'],
  ['📜','Training',$counts['training'].' certificates','training.php','rgba(34,211,238,.08)'],
  ['🏆','Awards',$counts['awards'].' awards','awards.php','rgba(250,204,21,.08)'],
  ['📄','Research',$counts['research'].' papers','research.php','rgba(34,211,238,.08)'],
  ['📰','News',$counts['news'].' items','news.php','rgba(34,211,238,.08)'],
  ['⚡','Skills',$counts['skills'].' skills','skills.php','rgba(34,211,238,.08)'],
  ['🚀','Projects',$counts['projects'].' projects','projects.php','rgba(139,92,246,.08)'],
  ['🌐','Portfolio',$counts['portfolio'].' sites','portfolio_sites.php','rgba(34,211,238,.08)'],
  ['🛒','Services',$counts['services'].' services','services_about.php','rgba(34,211,238,.08)'],
  ['❤️','Interests',$counts['interests'].' items','interests.php','rgba(34,211,238,.08)'],
  ['✉️','Messages',$counts['messages'].' unread','messages.php','rgba(239,68,68,.08)'],
];
foreach($cards as $c): ?>
<a href="<?=$c[3]?>" style="background:<?=$c[4]?>;border:1px solid #1e2638;border-radius:12px;padding:18px;text-decoration:none;transition:.15s;display:block" onmouseover="this.style.borderColor='#22d3ee'" onmouseout="this.style.borderColor='#1e2638'">
  <div style="font-size:26px;margin-bottom:8px"><?=$c[0]?></div>
  <div style="font-size:13px;font-weight:700;color:#fff"><?=$c[1]?></div>
  <div style="font-size:12px;color:#64748b;margin-top:2px"><?=$c[2]?></div>
</a>
<?php endforeach; ?>
</div>

<div class="card">
  <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:12px">📨 Recent Messages (<?=$counts['totalMsg']?> total)</div>
  <?php $msgs = dbRows("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5"); ?>
  <?php if(!$msgs): ?><p style="color:#64748b;font-size:13px">No messages yet.</p><?php else: ?>
  <table>
    <thead><tr><th>From</th><th>Subject</th><th>Date</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach($msgs as $m): ?>
    <tr>
      <td><strong style="color:#fff"><?=h($m['name'])?></strong><br><span style="color:#64748b;font-size:11px"><?=h($m['email'])?></span></td>
      <td style="color:#8892a4"><?=h(mb_substr($m['subject']?:$m['message'],0,60))?></td>
      <td style="color:#64748b;font-size:11px"><?=date('d M Y, H:i', strtotime($m['created_at']))?></td>
      <td><?=$m['is_read']?'<span style="color:#64748b;font-size:11px">Read</span>':'<span class="badge">New</span>'?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <div style="margin-top:12px"><a href="messages.php" class="btn btn-secondary" style="font-size:12px">View All Messages →</a></div>
  <?php endif; ?>
</div>

<div class="card" style="background:rgba(139,92,246,.04);border-color:rgba(139,92,246,.2)">
  <div class="section-heading-sm">🔑 Change Admin Password</div>
  <p style="font-size:12px;color:#64748b;margin-bottom:12px">Use the secure password change page to upgrade to a bcrypt-hashed password — no file editing required.</p>
  <a href="changepassword.php" class="btn btn-secondary" style="font-size:12px;border:1px solid rgba(139,92,246,.3);color:#a78bfa">🔐 Change Password →</a>
</div>

<?php include __DIR__ . '/footer.php'; ?>
