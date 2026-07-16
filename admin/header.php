<?php if(!defined('ADMIN_PAGE')) die('Direct access not allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?=htmlspecialchars($pageTitle??'Admin')?> — Portfolio Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f1420;--surface:#161b27;--card:#161b27;
  --border:#1e2638;--border-light:#263048;
  --text:#c9d1e3;--text-muted:#64748b;--text-dim:#8892a4;
  --cyan:#22d3ee;--cyan-dark:#06b6d4;
  --red:#ef4444;--red-bg:rgba(239,68,68,.1);--red-border:rgba(239,68,68,.3);
  --violet:#8b5cf6;--amber:#f59e0b;
  --radius:12px;--radius-sm:8px;
  --shadow:0 4px 16px rgba(0,0,0,.4);
}
html{font-size:16px;scroll-behavior:smooth}
body{font-family:'Inter','Segoe UI',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column;line-height:1.5;-webkit-font-smoothing:antialiased}
a{color:var(--cyan);text-decoration:none;transition:color .15s}
a:hover{text-decoration:underline}

/* ── TOPBAR ─────────────────────────────────────────── */
.topbar{background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:58px;position:sticky;top:0;z-index:100;gap:12px}
.topbar-brand{font-weight:800;color:#fff;font-size:14px;display:flex;align-items:center;gap:8px;white-space:nowrap;text-decoration:none}
.topbar-brand:hover{text-decoration:none;color:#fff}
.topbar-brand span{color:var(--cyan)}
.topbar-brand small{font-weight:400;color:var(--text-muted);font-size:11px}

/* Desktop nav */
.topbar-nav{display:flex;align-items:center;gap:1px;flex:1;justify-content:center}
.nav-link{padding:6px 10px;border-radius:var(--radius-sm);font-size:12px;color:var(--text-dim);transition:.15s;white-space:nowrap;text-decoration:none;position:relative}
.nav-link:hover{background:var(--border);color:var(--text);text-decoration:none}
.nav-link.active{background:var(--border);color:#fff;font-weight:600}
.nav-link.active::after{content:'';position:absolute;bottom:-1px;left:50%;transform:translateX(-50%);width:16px;height:2px;background:var(--cyan);border-radius:2px}
.nav-link.special{color:var(--cyan)}
.nav-link.special2{color:var(--violet)}

/* Mobile hamburger */
.menu-toggle{display:none;background:none;border:none;color:var(--text);font-size:20px;cursor:pointer;padding:6px;border-radius:var(--radius-sm);transition:.15s}
.menu-toggle:hover{background:var(--border);color:#fff}
.menu-toggle:focus-visible{outline:2px solid var(--cyan);outline-offset:2px}

/* Mobile nav overlay */
.mobile-nav{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:99;backdrop-filter:blur(4px)}
.mobile-nav.open{display:block}
.mobile-nav-inner{position:absolute;top:0;left:0;bottom:0;width:240px;background:var(--surface);border-right:1px solid var(--border);padding:16px;overflow-y:auto;transform:translateX(-100%);transition:transform .25s ease}
.mobile-nav.open .mobile-nav-inner{transform:translateX(0)}
.mobile-nav-brand{font-weight:800;color:#fff;font-size:15px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.mobile-nav-brand span{color:var(--cyan)}
.mobile-nav-link{display:block;padding:10px 12px;border-radius:var(--radius-sm);font-size:13px;color:var(--text-dim);text-decoration:none;transition:.15s;margin-bottom:2px}
.mobile-nav-link:hover,.mobile-nav-link.active{background:var(--border);color:#fff;text-decoration:none}
.mobile-nav-close{position:absolute;top:12px;right:12px;background:none;border:none;color:var(--text-muted);font-size:22px;cursor:pointer;padding:4px;line-height:1}

/* Topbar right */
.topbar-right{display:flex;align-items:center;gap:12px}
.topbar-right a{font-size:12px;color:var(--text-muted);transition:color .15s}
.topbar-right a:hover{color:var(--text)}
.btn-logout{padding:7px 14px;background:var(--red-bg);border:1px solid var(--red-border);color:#f87171;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;transition:.15s;text-decoration:none}
.btn-logout:hover{background:rgba(239,68,68,.2);text-decoration:none}

/* ── MAIN CONTENT ────────────────────────────────────── */
.main{padding:32px 24px;max-width:1140px;margin:0 auto;width:100%}
.page-header{margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid var(--border)}
.page-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;letter-spacing:-.3px}
.page-sub{font-size:13px;color:var(--text-muted)}

/* ── CARDS ───────────────────────────────────────────── */
.card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:20px;transition:border-color .2s}
.card:hover{border-color:var(--border-light)}
.card-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.card-header-title{font-size:14px;font-weight:700;color:#fff}
.card-header-actions{display:flex;align-items:center;gap:8px}

/* ── FORM ELEMENTS ──────────────────────────────────── */
label{display:block;font-size:12px;color:var(--text-dim);margin-bottom:6px;font-weight:600;letter-spacing:.2px}
.form-group{margin-bottom:16px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
input[type=text],input[type=email],input[type=url],input[type=number],input[type=password],textarea,select{
  width:100%;background:var(--bg);border:1.5px solid var(--border);color:var(--text);
  border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;outline:none;
  transition:border-color .2s,box-shadow .2s;font-family:inherit;
}
input::placeholder,textarea::placeholder{color:var(--text-muted);opacity:.7}
input:focus,textarea:focus,select:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(34,211,238,.1)}
textarea{resize:vertical;min-height:90px;line-height:1.6}
select{cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238889a4' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px}
input[type=file]{background:var(--bg);border:1.5px dashed var(--border);color:var(--text);border-radius:var(--radius-sm);padding:10px 14px;font-size:13px;cursor:pointer;width:100%;transition:border-color .2s}
input[type=file]:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(34,211,238,.1)}

/* ── BUTTONS ─────────────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;transition:.15s;text-decoration:none;white-space:nowrap;line-height:1}
.btn:focus-visible{outline:2px solid var(--cyan);outline-offset:2px}
.btn-primary{background:var(--cyan);color:#08121e}
.btn-primary:hover{background:var(--cyan-dark);transform:translateY(-1px);box-shadow:0 4px 12px rgba(34,211,238,.25)}
.btn-danger{background:var(--red-bg);border:1.5px solid var(--red-border);color:#f87171}
.btn-danger:hover{background:rgba(239,68,68,.2);transform:translateY(-1px)}
.btn-secondary{background:var(--border);color:var(--text)}
.btn-secondary:hover{background:var(--border-light);transform:translateY(-1px)}
.btn-sm{padding:6px 12px;font-size:12px}
.btn-block{display:flex;width:100%;justify-content:center}

/* ── ALERTS ─────────────────────────────────────────── */
.alert{padding:12px 16px;border-radius:var(--radius-sm);font-size:13px;margin-bottom:18px;display:flex;align-items:flex-start;gap:10px;font-weight:500}
.alert-success{background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);color:#67e8f9}
.alert-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#f87171}
.alert-warning{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);color:#fcd34d}
.alert-icon{font-size:16px;flex-shrink:0;margin-top:1px}
.alert-close{background:none;border:none;color:inherit;cursor:pointer;padding:0;margin-left:auto;opacity:.7;font-size:16px;line-height:1}
.alert-close:hover{opacity:1}

/* ── TABLES ─────────────────────────────────────────── */
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;padding:10px 14px;color:var(--text-muted);border-bottom:2px solid var(--border);font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.6px;white-space:nowrap}
td{padding:12px 14px;border-bottom:1px solid rgba(30,38,56,.6);vertical-align:top}
tr:last-child td{border-bottom:none}
tbody tr:hover td{background:rgba(30,38,56,.4)}
tbody tr:nth-child(even) td{background:rgba(255,255,255,.015)}
tbody tr:nth-child(even):hover td{background:rgba(30,38,56,.4)}
.td-muted{color:var(--text-muted);font-size:12px}
.td-actions{display:flex;align-items:center;gap:6px;flex-wrap:wrap}

/* ── BADGES & TAGS ──────────────────────────────────── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(34,211,238,.1);color:var(--cyan)}
.badge-violet{background:rgba(139,92,246,.1);color:#a78bfa}
.badge-amber{background:rgba(245,158,11,.1);color:#fcd34d}
.badge-red{background:rgba(239,68,68,.1);color:#f87171}
.badge-green{background:rgba(34,197,94,.1);color:#4ade80}

/* ── GRIDS ───────────────────────────────────────────── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}

/* ── EMPTY STATE ─────────────────────────────────────── */
.empty-state{text-align:center;padding:40px 20px;color:var(--text-muted)}
.empty-state-icon{font-size:36px;margin-bottom:12px;opacity:.4}
.empty-state-text{font-size:13px;margin-bottom:16px}
.empty-state-action{color:var(--text-dim);font-size:12px}

/* ── SECTION HEADINGS ─────────────────────────────────── */
.section-heading{font-size:14px;font-weight:700;color:#fff;margin-bottom:14px;letter-spacing:-.1px}
.section-heading-sm{font-size:13px;font-weight:700;color:#fff;margin-bottom:12px}

/* ── DASHBOARD CARDS ─────────────────────────────────── */
.dash-card{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius);padding:18px;text-decoration:none;transition:border-color .15s,transform .15s;display:block}
.dash-card:hover{border-color:var(--cyan);transform:translateY(-1px);text-decoration:none}
.dash-card-icon{font-size:26px;margin-bottom:8px}
.dash-card-title{font-size:13px;font-weight:700;color:#fff}
.dash-card-sub{font-size:12px;color:var(--text-muted);margin-top:2px}

/* ── MESSAGE CARD ────────────────────────────────────── */
.msg-meta{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;font-size:13px}
.msg-meta-label{color:var(--text-muted)}
.msg-meta-val{color:var(--text)}
.msg-meta-val strong{color:#fff}
.msg-body{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;color:var(--text);font-size:14px;line-height:1.7;white-space:pre-wrap}
.msg-actions{margin-top:14px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.msg-row-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px}
.msg-row-unread{background:rgba(34,211,238,.03)}

/* ── DIVIDERS ────────────────────────────────────────── */
.divider{height:1px;background:var(--border);margin:20px 0}

/* ── FOCUS VISIBLE ──────────────────────────────────── */
:focus-visible{outline:2px solid var(--cyan);outline-offset:2px;border-radius:4px}
a:focus-visible,button:focus-visible{outline:2px solid var(--cyan);outline-offset:2px}

/* ── RESPONSIVE ─────────────────────────────────────── */
@media(max-width:768px){
  .form-row,.form-row-3{grid-template-columns:1fr}
  .grid-2,.grid-3{grid-template-columns:1fr}
  .topbar-nav,.topbar-right{display:none}
  .menu-toggle{display:flex;align-items:center;justify-content:center}
  .main{padding:20px 16px}
  .page-title{font-size:20px}
  .card{padding:18px}
}
</style>
</head>
<body>

<!-- Mobile nav overlay -->
<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-inner">
    <button class="mobile-nav-close" onclick="toggleMobileNav()" aria-label="Close menu">&#215;</button>
    <div class="mobile-nav-brand">⚡ <span>Tanka</span> Admin</div>
    <?php $cur = basename($_SERVER['PHP_SELF']); $mobileLinks = [
      ['index.php','Dashboard','&#128200;'],
      ['profile.php','Profile','&#128100;'],
      ['education.php','Education','&#127891;'],
      ['experience.php','Experience','&#127970;'],
      ['training.php','Training','&#127912;'],
      ['awards.php','Awards','&#127942;'],
      ['research.php','Research','&#128196;'],
      ['news.php','News','&#128188;'],
      ['skills.php','Skills','&#9889;'],
      ['projects.php','Projects','&#128640;'],
      ['portfolio_sites.php','Portfolio','&#127760;'],
      ['services_about.php','Services','&#129309;'],
      ['interests.php','Interests','&#10084;&#65039;'],
      ['messages.php','Messages','&#128172;'],
      ['upload.php','Upload','&#128247;'],
      ['ai_settings.php','AI Chat','&#129504;'],
      ['changepassword.php','Password','&#128273;'],
    ]; ?>
    <?php foreach($mobileLinks as $l): ?>
    <a href="<?=$l[0]?>" class="mobile-nav-link <?=$cur==$l[0]?'active':''?>"><?=htmlspecialchars($l[1])?></a>
    <?php endforeach; ?>
    <div class="divider" style="margin:16px 0"></div>
    <a href="../index.php" target="_blank" class="mobile-nav-link">View Site ↗</a>
    <a href="logout.php" class="mobile-nav-link" style="color:#f87171">Logout</a>
  </div>
</div>

<div class="topbar">
  <button class="menu-toggle" onclick="toggleMobileNav()" aria-label="Open menu">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
      <line x1="3" y1="6" x2="17" y2="6"/><line x1="3" y1="10" x2="17" y2="10"/><line x1="3" y1="14" x2="17" y2="14"/>
    </svg>
  </button>
  <a href="index.php" class="topbar-brand">⚡ <span>Tanka</span> <small>Admin</small></a>
  <nav class="topbar-nav">
    <?php $cur = basename($_SERVER['PHP_SELF']); ?>
    <a href="index.php" class="nav-link <?=$cur=='index.php'?'active':''?>">Dashboard</a>
    <a href="profile.php" class="nav-link <?=$cur=='profile.php'?'active':''?>">Profile</a>
    <a href="education.php" class="nav-link <?=$cur=='education.php'?'active':''?>">Education</a>
    <a href="experience.php" class="nav-link <?=$cur=='experience.php'?'active':''?>">Experience</a>
    <a href="training.php" class="nav-link <?=$cur=='training.php'?'active':''?>">Training</a>
    <a href="awards.php" class="nav-link <?=$cur=='awards.php'?'active':''?>">Awards</a>
    <a href="research.php" class="nav-link <?=$cur=='research.php'?'active':''?>">Research</a>
    <a href="news.php" class="nav-link <?=$cur=='news.php'?'active':''?>">News</a>
    <a href="skills.php" class="nav-link <?=$cur=='skills.php'?'active':''?>">Skills</a>
    <a href="projects.php" class="nav-link <?=$cur=='projects.php'?'active':''?>">Projects</a>
    <a href="portfolio_sites.php" class="nav-link <?=$cur=='portfolio_sites.php'?'active':''?>">Portfolio</a>
    <a href="services_about.php" class="nav-link <?=$cur=='services_about.php'?'active':''?>">Services</a>
    <a href="interests.php" class="nav-link <?=$cur=='interests.php'?'active':''?>">Interests</a>
    <a href="messages.php" class="nav-link <?=$cur=='messages.php'?'active':''?>">Messages</a>
    <a href="upload.php" class="nav-link <?=$cur=='upload.php'?'active':''?> special">&#128247; Upload</a>
    <a href="ai_settings.php" class="nav-link <?=$cur=='ai_settings.php'?'active':''?> special">&#129504; AI Chat</a>
    <a href="changepassword.php" class="nav-link <?=$cur=='changepassword.php'?'active':''?> special2">&#128273;</a>
  </nav>
  <div class="topbar-right">
    <a href="../index.php" target="_blank">View Site ↗</a>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</div>
<div class="main">
<div class="page-header">
<div class="page-title"><?=htmlspecialchars($pageTitle??'Dashboard')?></div>
<div class="page-sub"><?=htmlspecialchars($pageSubtitle??'')?></div>
</div>
