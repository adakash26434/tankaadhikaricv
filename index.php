<?php
require_once __DIR__ . '/db.php';
$p = dbRow("SELECT * FROM profile LIMIT 1") ?: [];
$experiences    = dbRows("SELECT * FROM experience ORDER BY sort_order, id");
$projects       = dbRows("SELECT * FROM projects ORDER BY sort_order, id");
$awards         = dbRows("SELECT * FROM awards ORDER BY sort_order, id");
$allSkills      = dbRows("SELECT * FROM skills ORDER BY sort_order, id");
$education      = dbRows("SELECT * FROM education ORDER BY sort_order, id");
$training       = dbRows("SELECT * FROM training ORDER BY sort_order, id");
$research       = dbRows("SELECT * FROM research ORDER BY sort_order, id");
$newsItems      = dbRows("SELECT * FROM news ORDER BY sort_order, id");
$interests      = dbRows("SELECT * FROM interests ORDER BY sort_order, id");
$portfolioSites = dbRows("SELECT * FROM portfolio_sites ORDER BY sort_order, id");
$servicesAbout  = dbRows("SELECT * FROM services_about ORDER BY sort_order, id");
$skillGroups = [];
foreach ($allSkills as $s) { $skillGroups[$s['category']][] = $s; }
$name     = h($p['full_name'] ?? 'Tanka Prasad Adhikari');
$title    = h($p['title'] ?? 'Founder & CEO');
$bio      = h($p['bio'] ?? '');
$email    = h($p['email'] ?? '');
$phone    = h($p['phone'] ?? '');
$location = h($p['location'] ?? '');
$born     = h($p['born'] ?? '');
$company  = h($p['company'] ?? '');
$compUrl  = h($p['company_url'] ?? '#');
$role     = h($p['role'] ?? '');
$avatar   = h($p['avatar'] ?? 'img/avatar.jpg');
$cvFile   = h($p['cv_file'] ?? 'files/canada.pdf');
$firstName = explode(' ', $p['full_name'] ?? 'Tanka')[0];

// Dynamic hero stats from DB
$statYears = max(1, count($experiences));
$statProjects = count($projects);
$statAwards = count($awards);
$statClients = $p['clients_served'] ?? '50+';
$heroStats = [
  ['num' => $statYears, 'label' => 'Yrs Experience'],
  ['num' => $statProjects > 0 ? $statProjects . '+' : '1+', 'label' => 'Projects'],
  ['num' => is_numeric($statClients) ? $statClients . '+' : $statClients, 'label' => 'Clients Served'],
  ['num' => $statAwards > 0 ? $statAwards : '1', 'label' => "Int'l Awards"],
];
// Social links for Schema.org sameAs
$sameAs = array_filter([
    $p['facebook_url']  ?? '',
    $p['linkedin_url']  ?? '',
    $p['youtube_url']   ?? '',
    $p['tiktok_url']    ?? '',
    $p['whatsapp_url']  ?? '',
]);
// Dynamic site URL — auto-detected, no hardcoding needed
$_proto   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host    = $_SERVER['HTTP_HOST'] ?? 'www.tankaadhikari.com.np';
$siteUrl  = $_proto . '://' . $_host;
// OG image: use dedicated og_image field from profile if set, else avatar
$ogImage  = !empty($p['og_image'])
    ? $siteUrl . '/' . ltrim($p['og_image'], '/')
    : $siteUrl . '/' . ltrim($p['avatar'] ?? 'img/avatar.jpg', '/');
$_fn = $p['full_name'] ?? 'Tanka Prasad Adhikari';
$_ti = $p['title'] ?? 'Founder & CEO';
$_co = $p['company'] ?? 'Aakash Digital Pvt. Ltd.';
$_lo = $p['location'] ?? 'Pokhara';
$siteDesc = htmlspecialchars("{$_fn} is the {$_ti} of {$_co}, leading digital transformation in Nepal's cooperative and fintech sector from {$_lo}.", ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?=$name?> — <?=$title?> | <?=h($p['company']??'Aakash Digital Pvt. Ltd.')?></title>
<meta name="description" content="<?=$siteDesc?>">
<meta name="author" content="<?=$name?>">
<meta property="og:type" content="profile">
<meta property="og:title" content="<?=$name?> — <?=$title?>">
<meta property="og:description" content="<?=$siteDesc?>">
<meta property="og:image" content="<?=$ogImage?>">
<meta property="og:url" content="<?=$siteUrl?>/">
<meta property="og:site_name" content="<?=$name?> Portfolio">
<meta property="og:locale" content="en_US">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?=$name?> — <?=$title?>">
<meta name="twitter:description" content="<?=$siteDesc?>">
<meta name="twitter:image" content="<?=$ogImage?>">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "<?=addslashes($p['full_name']??'Tanka Prasad Adhikari')?>",
  "jobTitle": "<?=addslashes($p['title']??'Founder & CEO')?>",
  "description": "<?=addslashes($p['bio']??'')?>",
  "worksFor": {"@type": "Organization", "name": "<?=addslashes($p['company']??'Aakash Digital Pvt. Ltd.')?>", "url": "<?=addslashes($p['company_url']??'')?>"},
  "email": "<?=addslashes($p['email']??'')?>",
  "telephone": "<?=addslashes($p['phone']??'')?>",
  "address": {"@type": "PostalAddress", "addressLocality": "<?=addslashes($p['location']??'Pokhara')?>", "addressCountry": "NP"},
  "url": "<?=addslashes($siteUrl)?>",
  "image": "<?=addslashes($ogImage)?>"
  <?php if($sameAs): ?>,
  "sameAs": [<?php $sa=array_values($sameAs); foreach($sa as $i=>$u): echo '"'.addslashes($u).'"'.($i<count($sa)-1?',':''); endforeach; ?>]
  <?php endif; ?>
}
</script>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23161b27'/><text x='50%25' y='54%25' dominant-baseline='middle' text-anchor='middle' font-family='Inter,sans-serif' font-weight='800' font-size='14' fill='%2322d3ee'>TA</text></svg>">
<script>// Apply saved theme before first paint — prevents flash
(function(){var t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t)})();</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════
   THEME VARIABLES — Dark (default)
═══════════════════════════════════════════════════════ */
:root,
[data-theme="dark"] {
  --bg:#0d1117;--sidebar:#161b27;--card:#1e2535;--card-alt:#232b3e;
  --border:#263147;--text:#c9d1e3;--muted:#8b99b4;
  --cyan:#22d3ee;--violet:#8b5cf6;--accent:#0ea5e9;
  --green:#22c55e;--red:#ef4444;--yellow:#f59e0b;
  --shadow:rgba(0,0,0,0.5);--shadow-lg:rgba(0,0,0,0.7);
  --glow-cyan:rgba(34,211,238,.3);--glow-violet:rgba(139,92,246,.2);
  --bg-pattern:radial-gradient(circle,rgba(34,211,238,.06) 1px,transparent 1px);
  --bg-gradient:
    radial-gradient(ellipse 80% 60% at 20% 0%,rgba(14,165,233,.18) 0%,transparent 60%),
    radial-gradient(ellipse 60% 80% at 80% 100%,rgba(139,92,246,.14) 0%,transparent 60%),
    linear-gradient(160deg,#070b12 0%,#0b1220 40%,#0f1a2e 100%);
  --theme-icon:&#9790;;/* moon */
  --body-bg:#0d1117;
  --meta-bg:rgba(255,255,255,.03);
  --sec-label-color:var(--muted);
  --table-zebra:rgba(255,255,255,.015);
  --tag-bg:rgba(34,211,238,.06);
  --tag-border:rgba(34,211,238,.2);
  --tag-color:var(--cyan);
}
[data-theme="light"] {
  --bg:#f0f2f8;--sidebar:#ffffff;--card:#ffffff;--card-alt:#f8f9fc;
  --border:#dde1ec;--text:#1a2035;--muted:#6b7594;
  --cyan:#0891b2;--violet:#7c3aed;--accent:#0284c7;
  --green:#16a34a;--red:#dc2626;--yellow:#d97706;
  --shadow:rgba(0,0,0,0.08);--shadow-lg:rgba(0,0,0,0.12);
  --glow-cyan:rgba(8,145,178,.15);--glow-violet:rgba(124,58,237,.12);
  --bg-pattern:radial-gradient(circle,rgba(8,145,178,.08) 1px,transparent 1px);
  --bg-gradient:linear-gradient(160deg,#e8ecf5 0%,#f5f7fc 50%,#eef1f8 100%);
  --theme-icon:&#9728;;/* sun */
  --body-bg:#eef1f8;
  --meta-bg:rgba(0,0,0,.04);
  --sec-label-color:var(--muted);
  --table-zebra:rgba(0,0,0,.018);
  --tag-bg:rgba(8,145,178,.07);
  --tag-border:rgba(8,145,178,.2);
  --tag-color:var(--cyan);
}

/* ═══════════════════════════════════════════════════════
   RESET & BASE
═══════════════════════════════════════════════════════ */
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;font-size:16px}
body{font-family:'Inter',sans-serif;background:var(--body-bg);color:var(--text);min-height:100vh;overflow-x:hidden;
  transition:background .3s,color .3s}

/* Animated background — fixed at root level */
body::before{content:'';position:fixed;inset:0;background:var(--bg-gradient);z-index:0;pointer-events:none}
body::after{content:'';position:fixed;inset:0;background-image:var(--bg-pattern);background-size:28px 28px;z-index:0;pointer-events:none;transition:background-image .3s}
a{color:var(--cyan);text-decoration:none}
a:hover{text-decoration:underline}

/* ═══════════════════════════════════════════════════════
   THEME TOGGLE
═══════════════════════════════════════════════════════ */
#theme-toggle{position:fixed;top:12px;right:72px;z-index:300;width:36px;height:36px;
  background:var(--sidebar);border:1px solid var(--border);border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  color:var(--text);font-size:16px;cursor:pointer;transition:all .2s;
  box-shadow:0 2px 8px var(--shadow)}
#theme-toggle:hover{border-color:var(--cyan);color:var(--cyan);transform:scale(1.05)}

/* ═══════════════════════════════════════════════════════
   LOADER
═══════════════════════════════════════════════════════ */
#loader{position:fixed;inset:0;background:var(--body-bg);z-index:9999;display:flex;
  align-items:center;justify-content:center;flex-direction:column;gap:16px;transition:opacity .4s}
.loader-ring{width:48px;height:48px;border:3px solid var(--border);border-top-color:var(--cyan);border-radius:50%;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* ═══════════════════════════════════════════════════════
   ANIMATIONS
═══════════════════════════════════════════════════════ */
@keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeInLeft{from{opacity:0;transform:translateX(-20px)}to{opacity:1;transform:translateX(0)}}
@keyframes blink{0%,100%{opacity:1}50%{opacity:0}}
@keyframes slideInUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@keyframes gradientShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
@keyframes glowPulse{0%,100%{box-shadow:0 0 8px var(--glow-cyan),0 0 16px var(--glow-violet)}50%{box-shadow:0 0 16px var(--glow-cyan),0 0 32px var(--glow-violet)}}
@keyframes pulse-dot{0%,100%{box-shadow:0 0 6px var(--green)}50%{box-shadow:0 0 14px var(--green)}}

/* ═══════════════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════════════ */
@media(prefers-reduced-motion:reduce){
  *{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}
  html{scroll-behavior:auto!important}
}

/* ═══════════════════════════════════════════════════════
   LAYOUT
═══════════════════════════════════════════════════════ */
.layout{position:relative;z-index:1;display:flex;gap:16px;padding:16px;height:100vh;align-items:stretch}

/* ═══════════════════════════════════════════════════════
   SIDEBAR (LEFT CARD)
═══════════════════════════════════════════════════════ */
.sidebar{width:260px;min-width:260px;
  background:var(--sidebar);border:1px solid var(--border);border-radius:20px;
  display:flex;flex-direction:column;overflow:hidden;
  box-shadow:0 32px 80px var(--shadow-lg),0 0 0 1px var(--border);
  transition:background .3s,border-color .3s,box-shadow .3s}
.sidebar-profile{padding:28px 18px 16px;text-align:center;flex:1;display:flex;
  flex-direction:column;align-items:center;justify-content:center}

/* Avatar */
.avatar-wrap{position:relative;display:inline-block;margin-bottom:14px}
.avatar-glow{position:absolute;inset:-5px;border-radius:50%;
  background:conic-gradient(from 0deg,var(--cyan),var(--violet),var(--cyan));
  animation:spin 4s linear infinite;opacity:.7}
.avatar-ring{position:absolute;inset:-3px;border-radius:50%;background:var(--sidebar);animation:glowPulse 3s ease-in-out infinite}
.avatar-wrap img{position:relative;z-index:1;width:100px;height:100px;border-radius:50%;object-fit:cover;display:block}
.avatar-dot{position:absolute;bottom:5px;right:5px;z-index:2;width:14px;height:14px;
  background:var(--green);border-radius:50%;border:2.5px solid var(--sidebar);
  box-shadow:0 0 8px var(--green);animation:pulse-dot 2s ease-in-out infinite}

/* Profile info */
.profile-name{font-weight:800;color:var(--text);font-size:15px;line-height:1.35;margin-bottom:4px;letter-spacing:-.2px}
.profile-name span{background:linear-gradient(135deg,var(--cyan),var(--violet));-webkit-background-clip:text;background-clip:text;color:transparent}
.profile-role{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.2px;font-weight:600;line-height:1.4}

/* Sidebar stats */
.sidebar-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin:12px 0 10px;width:100%}
.sidebar-stat{background:var(--meta-bg);border:1px solid var(--border);border-radius:8px;padding:8px 4px;text-align:center;transition:background .3s,border-color .3s}
.sidebar-stat-num{font-size:16px;font-weight:800;color:var(--text);line-height:1}
.sidebar-stat-num small{color:var(--cyan);font-size:11px}
.sidebar-stat-lbl{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-top:3px}

/* Social links */
.social-links{display:flex;justify-content:center;gap:6px;margin-top:8px}
.social-links a{width:30px;height:30px;background:var(--meta-bg);border:1px solid var(--border);border-radius:7px;
  display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:11px;transition:.2s}
.social-links a:hover{background:rgba(34,211,238,.12);border-color:rgba(34,211,238,.35);color:var(--cyan);text-decoration:none;transform:translateY(-1px)}

/* Sidebar divider & actions */
.sidebar-divider{height:1px;background:linear-gradient(90deg,transparent,var(--border),transparent);margin:0 14px}
.sidebar-actions{padding:14px 16px;display:flex;flex-direction:column;gap:8px}
.sidebar-copy{padding:10px 14px;text-align:center;font-size:10px;color:var(--muted);border-top:1px solid var(--border);transition:border-color .3s}
.btn-cv{display:flex;align-items:center;justify-content:center;gap:7px;padding:11px;
  background:linear-gradient(135deg,var(--cyan),#06b6d4);border:none;border-radius:10px;
  color:#08121e;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;
  transition:.2s;text-decoration:none;cursor:pointer;box-shadow:0 4px 16px rgba(34,211,238,.25)}
.btn-cv:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(34,211,238,.35);text-decoration:none;color:#08121e}
.btn-msg{display:flex;align-items:center;justify-content:center;gap:7px;padding:11px;
  background:var(--meta-bg);border:1px solid var(--border);border-radius:10px;
  color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
  transition:.2s;cursor:pointer;text-decoration:none}
.btn-msg:hover{border-color:rgba(34,211,238,.4);color:var(--cyan);background:rgba(34,211,238,.06);text-decoration:none}

/* ═══════════════════════════════════════════════════════
   CONTENT (RIGHT CARD)
═══════════════════════════════════════════════════════ */
.content{flex:1;background:var(--card);border:1px solid var(--border);border-radius:20px;
  padding:40px 46px;overflow-y:auto;height:100%;
  box-shadow:0 32px 80px var(--shadow),0 0 0 1px var(--border);
  scroll-behavior:smooth;transition:background .3s,border-color .3s,box-shadow .3s}
.content::-webkit-scrollbar{width:4px}
.content::-webkit-scrollbar-track{background:transparent}
.content::-webkit-scrollbar-thumb{background:var(--border);border-radius:10px}

/* ═══════════════════════════════════════════════════════
   RIGHT NAV
═══════════════════════════════════════════════════════ */
.right-nav{position:fixed;right:20px;top:50%;transform:translateY(-50%);z-index:200;
  display:flex;flex-direction:column;gap:3px;
  background:var(--sidebar);border:1px solid var(--border);border-radius:40px;
  padding:9px 6px;
  box-shadow:0 20px 50px var(--shadow-lg),0 0 0 1px var(--border);
  max-height:calc(100vh - 40px);overflow-y:auto;scrollbar-width:none;
  transition:background .3s,border-color .3s,box-shadow .3s}
.right-nav::-webkit-scrollbar{display:none}
.right-nav a{width:34px;height:34px;display:flex;align-items:center;justify-content:center;
  border-radius:50%;color:var(--muted);font-size:11.5px;transition:.2s;text-decoration:none;position:relative;border:none;background:transparent}
.right-nav a:hover{background:rgba(34,211,238,.12);color:var(--cyan);text-decoration:none}
.right-nav a.active{background:linear-gradient(135deg,rgba(34,211,238,.18),rgba(6,182,212,.1));color:var(--cyan);box-shadow:0 0 0 2px rgba(34,211,238,.35)}
.right-nav a .rnav-tip{position:absolute;right:42px;background:var(--sidebar);border:1px solid var(--border);border-radius:7px;padding:4px 10px;font-size:9.5px;font-weight:700;color:var(--text);white-space:nowrap;opacity:0;pointer-events:none;transition:.15s;text-transform:uppercase;letter-spacing:.5px;box-shadow:0 4px 12px var(--shadow)}
.right-nav a:hover .rnav-tip{opacity:1}

/* ═══════════════════════════════════════════════════════
   SECTIONS
═══════════════════════════════════════════════════════ */
section{display:block;scroll-margin-top:20px}
section+section{margin-top:52px;padding-top:40px;border-top:1px solid var(--border);transition:border-color .3s}
.section-title{font-size:28px;font-weight:900;color:var(--text);margin-bottom:6px;letter-spacing:-.5px;display:block;animation:fadeInUp .6s ease-out both}
.section-underline{width:56px;height:3px;background:linear-gradient(90deg,var(--cyan),var(--violet),transparent);border-radius:2px;margin-bottom:26px}

/* Section label */
.sec-label{display:flex;align-items:center;gap:8px;font-size:11px;color:var(--sec-label-color);text-transform:uppercase;letter-spacing:1.2px;font-weight:700;margin-bottom:14px;transition:color .3s}
.sec-label::after{content:'';flex:1;height:1px;background:var(--border);transition:background .3s}

/* ═══════════════════════════════════════════════════════
   HERO
═══════════════════════════════════════════════════════ */
.hero-eyebrow{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;
  background:var(--tag-bg);border:1px solid var(--tag-border);font-size:11px;font-weight:700;
  text-transform:uppercase;letter-spacing:1.2px;color:var(--cyan);margin-bottom:14px;animation:fadeInLeft .6s ease-out both}
.hero-pulse{width:6px;height:6px;border-radius:50%;background:var(--cyan);display:inline-block;animation:pulse-dot 2s ease-in-out infinite}
.hero{display:flex;align-items:flex-start;justify-content:space-between;gap:24px;margin-bottom:32px;flex-wrap:wrap}
.hero-text{animation:fadeInUp .7s ease-out .15s both}
.hero-text h1{font-size:clamp(26px,5vw,44px);font-weight:900;color:var(--text);line-height:1.15;letter-spacing:-.8px;margin-bottom:6px}
.hero-text h1 span{background:linear-gradient(135deg,var(--cyan) 0%,var(--violet) 100%);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero-sub{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;font-weight:600;margin-bottom:2px;animation:fadeInUp .7s ease-out .3s both}

/* Hero avatar box */
.hero-avatar-box{flex-shrink:0;width:150px;height:150px;border-radius:18px;
  background:linear-gradient(135deg,rgba(34,211,238,.1),rgba(139,92,246,.1));
  border:1px solid var(--border);display:flex;align-items:center;justify-content:center;
  position:relative;overflow:hidden;
  box-shadow:0 16px 48px var(--shadow),inset 0 1px 0 rgba(255,255,255,.08)}
.hero-avatar-box::before{content:'';position:absolute;inset:0;border-radius:18px;
  background:linear-gradient(135deg,var(--cyan),var(--violet),var(--cyan));background-size:200% 200%;
  animation:gradientShift 4s ease infinite;opacity:.15;z-index:0}
.hero-avatar-box img{width:150px;height:150px;object-fit:cover;position:relative;z-index:1;border-radius:18px}
.hero-avatar-initials{font-size:48px;font-weight:900;letter-spacing:-1px;
  background:linear-gradient(135deg,var(--cyan),var(--violet));-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;z-index:1}
.hero-corner{position:absolute;bottom:10px;right:10px;background:var(--tag-bg);border:1px solid var(--tag-border);border-radius:6px;
  padding:3px 10px;font-size:10px;font-weight:700;color:var(--cyan);text-transform:uppercase;letter-spacing:.6px;z-index:2}

/* Meta row */
.meta-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:16px;animation:fadeInUp .7s ease-out .4s both}
.meta-item{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--muted);
  background:var(--meta-bg);border:1px solid var(--border);border-radius:7px;padding:5px 11px;transition:background .3s,border-color .3s}
.meta-item i{color:var(--cyan);font-size:11px}

/* Hero stats */
.hero-stats-row{display:flex;margin-top:18px;background:var(--meta-bg);border:1px solid var(--border);border-radius:12px;overflow:hidden;animation:fadeInUp .7s ease-out .5s both;transition:background .3s,border-color .3s}
.hero-stat{flex:1;padding:12px 8px;text-align:center;border-right:1px solid var(--border);transition:border-color .3s}
.hero-stat:last-child{border-right:none}
.hero-stat-num{font-size:22px;font-weight:900;background:linear-gradient(135deg,var(--cyan),var(--text));-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1}
.hero-stat-lbl{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-top:3px}
/* ═══════════════════════════════════════════════════════
   CARD-DARK & PROFILE TABLE
═══════════════════════════════════════════════════════ */
.card-dark{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px 22px;
  margin-bottom:14px;box-shadow:0 2px 8px var(--shadow);transition:.2s}
.card-dark:hover{border-color:rgba(34,211,238,.3);box-shadow:0 8px 24px var(--shadow-lg)}
.profile-table{width:100%;border-collapse:collapse;font-size:12.5px}
.profile-table td{padding:9px 12px;border-bottom:1px solid var(--border);transition:border-color .3s}
.profile-table td:first-child{width:130px;font-weight:700;color:var(--muted);font-size:10.5px;text-transform:uppercase;letter-spacing:.4px}
.profile-table td:last-child{color:var(--text);font-weight:500}
.profile-table tr:last-child td{border-bottom:none}
.profile-table tr:hover td{background:var(--table-zebra)}

/* ═══════════════════════════════════════════════════════
   SERVICES GRID
═══════════════════════════════════════════════════════ */
.services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-bottom:28px}
.service-card{background:var(--card);border:1px solid var(--border);border-radius:11px;padding:16px 12px;
  text-align:center;transition:.2s;position:relative;overflow:hidden}
.service-card:hover{border-color:rgba(34,211,238,.3);transform:translateY(-2px);box-shadow:0 8px 24px var(--shadow-lg)}
.service-card:hover .service-icon i{transform:scale(1.15);filter:drop-shadow(0 0 6px var(--cyan))}
.service-icon{font-size:20px;color:var(--cyan);margin-bottom:9px;transition:.2s}
.service-name{font-size:11px;color:var(--text);font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.service-desc{font-size:10px;color:var(--muted);line-height:1.5;margin-top:3px}
.service-card:nth-child(even) .service-icon{color:var(--violet)}
.service-card:nth-child(even):hover{border-color:rgba(139,92,246,.3)}

/* ═══════════════════════════════════════════════════════
   TRAINING GRID
═══════════════════════════════════════════════════════ */
.training-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:12px}
.cert-card{background:var(--card);border:1px solid var(--border);border-radius:11px;padding:14px 16px;
  display:flex;align-items:flex-start;gap:12px;transition:.2s}
.cert-card:hover{border-color:rgba(34,211,238,.25);transform:translateY(-1px);box-shadow:0 6px 18px var(--shadow-lg)}
.cert-icon-wrap{width:36px;height:36px;min-width:36px;
  background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);
  border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--cyan);font-size:14px}
.cert-icon{color:var(--cyan);font-size:14px}
.cert-name{font-size:12.5px;font-weight:700;color:var(--text);margin-bottom:2px}
.cert-org{font-size:11px;color:var(--muted);margin-bottom:3px}
.cert-year{font-size:11px;color:var(--cyan)}

/* ═══════════════════════════════════════════════════════
   TAGS
═══════════════════════════════════════════════════════ */
.tag{display:inline-flex;padding:3px 10px;background:var(--tag-bg);border:1px solid var(--tag-border);
  border-radius:20px;font-size:11px;color:var(--tag-color);font-weight:600;transition:background .3s,border-color .3s,color .3s}
.tag-violet{background:rgba(139,92,246,.07);border-color:rgba(139,92,246,.2);color:var(--violet)}
.tag-url{background:rgba(139,92,246,.07);border-color:rgba(139,92,246,.2);color:var(--violet);
  text-decoration:none;display:inline-flex;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;transition:.2s}
.tag-url:hover{background:rgba(139,92,246,.14);text-decoration:none}
/* Violet accent tags for journal/online links */
.tag-violet-accent{display:inline-flex;padding:3px 10px;background:rgba(139,92,246,.07);border:1px solid rgba(139,92,246,.2);border-radius:20px;font-size:11px;color:var(--violet);font-weight:600;transition:background .3s,border-color .3s,color .3s}
.tag-violet-accent i{margin-right:4px}

/* ═══════════════════════════════════════════════════════
   SKILLS
═══════════════════════════════════════════════════════ */
.skill-row{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.skill-name{font-size:12.5px;color:var(--text);min-width:120px;max-width:180px;flex-shrink:0;font-weight:500}
.skill-bar{flex:1;background:var(--meta-bg);border-radius:6px;height:6px;overflow:hidden;transition:background .3s}
.skill-fill{height:100%;border-radius:6px;transition:width 1s ease;box-shadow:0 0 6px var(--cyan)}
.skill-pct{font-size:10.5px;color:var(--muted);width:32px;text-align:right;font-weight:600}

/* ═══════════════════════════════════════════════════════
   NEWS GRID
═══════════════════════════════════════════════════════ */
.news-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:12px}
.news-card{background:var(--card);border:1px solid var(--border);border-radius:11px;overflow:hidden;
  transition:.2s;cursor:pointer;text-decoration:none;display:block}
.news-card:hover{border-color:rgba(34,211,238,.3);transform:translateY(-2px);box-shadow:0 8px 24px var(--shadow-lg);text-decoration:none}
.news-img{width:100%;height:90px;object-fit:cover;display:block}
.news-thumb{height:90px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid var(--border);position:relative;overflow:hidden;background:var(--card-alt);transition:border-color .3s}
.news-thumb-icon{font-size:28px;filter:drop-shadow(0 0 8px currentColor);position:relative;z-index:1;color:var(--cyan)}
.news-body{padding:12px 14px}
.news-title{font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:5px;line-height:1.4}
.news-source{font-size:11px;color:var(--muted);font-weight:600}

/* ═══════════════════════════════════════════════════════
   PORTFOLIO
═══════════════════════════════════════════════════════ */
.port-item{background:var(--card);border:1px solid var(--border);border-radius:11px;overflow:hidden;
  display:block;transition:.2s;text-decoration:none}
.port-item:hover{border-color:rgba(34,211,238,.3);transform:translateY(-2px);text-decoration:none;box-shadow:0 8px 24px var(--shadow-lg)}
.port-item img{width:100%;height:130px;object-fit:cover}
.port-info{padding:10px 12px}
.port-info p{font-size:12.5px;font-weight:700;color:var(--text);margin-bottom:2px}
.port-info span{font-size:11px;color:var(--muted)}

/* ═══════════════════════════════════════════════════════
   AWARDS
═══════════════════════════════════════════════════════ */
.award-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px 26px;
  position:relative;overflow:hidden;transition:.2s}
.award-card.cyan{border-left:3px solid var(--cyan)}
.award-card.violet{border-left:3px solid var(--violet)}
.award-card:hover{border-color:var(--border);box-shadow:0 12px 32px var(--shadow-lg)}
.award-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:10.5px;font-weight:700;margin-bottom:12px}

/* ═══════════════════════════════════════════════════════
   VIDEO GRID
═══════════════════════════════════════════════════════ */
.video-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.video-card{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:.2s}
.video-card:hover{border-color:rgba(34,211,238,.3);transform:translateY(-2px)}

/* ═══════════════════════════════════════════════════════
   CONTACT
═══════════════════════════════════════════════════════ */
.contact-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:28px}
.contact-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;transition:background .3s,border-color .3s}
.contact-icon{width:40px;height:40px;background:rgba(34,211,238,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--cyan);font-size:16px;flex-shrink:0}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:12px;color:var(--muted);margin-bottom:5px;font-weight:600}
.form-group input,.form-group textarea{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:10px 14px;font-size:13px;outline:none;font-family:inherit;transition:border-color .2s,background .3s,color .3s}
.form-group input:focus,.form-group textarea:focus{border-color:var(--cyan)}
.form-group textarea{resize:vertical;min-height:100px}
.btn-send{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;background:var(--cyan);border:none;border-radius:8px;color:#08121e;font-size:13px;font-weight:700;cursor:pointer;transition:.2s}
.btn-send:hover{filter:brightness(1.1);transform:translateY(-1px)}

/* ═══════════════════════════════════════════════════════
   INTERESTS
═══════════════════════════════════════════════════════ */
.interests-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px}
.interest-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:16px;text-align:center;transition:background .3s,border-color .3s}
.interest-icon{font-size:22px;color:var(--cyan);margin-bottom:8px}
.interest-name{font-size:12px;color:var(--text);font-weight:600}

/* ═══════════════════════════════════════════════════════
   GALLERY
═══════════════════════════════════════════════════════ */
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px}
.gallery-item img{width:100%;height:120px;object-fit:cover;border-radius:8px;border:1px solid var(--border);cursor:pointer;transition:.2s}
.gallery-item img:hover{border-color:var(--cyan)}

/* ═══════════════════════════════════════════════════════
   LIGHTBOX
═══════════════════════════════════════════════════════ */
#lightbox{position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px}
#lightbox.open{display:flex}
#lightbox img{max-width:90vw;max-height:90vh;border-radius:12px;object-fit:contain}
#lightbox-close{position:absolute;top:20px;right:24px;color:#fff;font-size:24px;cursor:pointer;background:none;border:none;line-height:1}

/* ═══════════════════════════════════════════════════════
   BACK TO TOP
═══════════════════════════════════════════════════════ */
.back-top{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:150;
  background:var(--sidebar);border:1px solid var(--border);border-radius:40px;
  padding:9px 20px;color:var(--muted);font-size:12px;font-weight:700;text-transform:uppercase;
  letter-spacing:.5px;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:6px;
  opacity:0;pointer-events:none}
.back-top.visible{opacity:1;pointer-events:auto}
.back-top:hover{color:var(--cyan);border-color:rgba(34,211,238,.3)}

/* ═══════════════════════════════════════════════════════
   FOCUS & ACCESSIBILITY
═══════════════════════════════════════════════════════ */
:focus-visible{outline:3px solid rgba(34,211,238,.6);outline-offset:3px;border-radius:4px}
button:focus-visible,a:focus-visible{outline:3px solid rgba(34,211,238,.6);outline-offset:3px}

/* ═══════════════════════════════════════════════════════
   MOBILE NAV (HAMBURGER)
═══════════════════════════════════════════════════════ */
.hamburger{display:none;position:fixed;top:12px;left:12px;z-index:300;
  background:var(--sidebar);border:1px solid var(--border);border-radius:10px;
  padding:8px 10px;cursor:pointer;color:var(--text);font-size:16px;transition:background .3s,border-color .3s}

/* ═══════════════════════════════════════════════════════
   RESPONSIVE — TABLET (≤960px)
═══════════════════════════════════════════════════════ */
@media(max-width:960px){
  body{overflow:auto}
  .layout{flex-direction:column;height:auto;padding:12px;gap:12px}
  .sidebar{width:100%;min-width:0;border-radius:16px;flex-direction:row;flex-wrap:wrap;height:auto;max-height:none}
  .sidebar-profile{flex-direction:row;text-align:left;gap:16px;padding:20px;border-bottom:none;flex:1;justify-content:flex-start}
  .sidebar-copy,.sidebar-divider{display:none}
  .sidebar-actions{flex-direction:row;padding:12px 20px;border-top:1px solid var(--border);width:100%}
  .content{height:auto;border-radius:16px;padding:28px 24px;overflow:visible;max-height:none}
  .right-nav,.hamburger{display:none!important}
  .video-grid{grid-template-columns:1fr}
  .hero-avatar-box{width:120px;height:120px}
  .hero-avatar-box img{width:120px;height:120px}
}

/* ═══════════════════════════════════════════════════════
   RESPONSIVE — MOBILE (≤600px)
═══════════════════════════════════════════════════════ */
@media(max-width:600px){
  .hero-text h1{font-size:28px}
  .hero-avatar-box{width:110px;height:110px}
  .hero-avatar-box img{width:110px;height:110px}
  .hero-avatar-initials{font-size:36px}
  .services-grid{grid-template-columns:1fr 1fr}
  .content{padding:20px 16px}
  .section-title{font-size:22px}
  .hero-stats-row{grid-template-columns:repeat(2,1fr)}
  .profile-name{font-size:14px}
  .sidebar-profile{padding:16px}
  .skill-row{flex-wrap:wrap;gap:8px}
  .skill-name{min-width:0;max-width:none;width:auto;font-size:12px}
  .skill-bar{width:100%;flex:auto}
  .skill-pct{width:auto;font-size:10px}
}

/* ═══════════════════════════════════════════════════════
   GLOBAL SCROLLBAR
═══════════════════════════════════════════════════════ */
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:10px}

/* ═══════════════════════════════════════════════════════
   PRINT
═══════════════════════════════════════════════════════ */
@media print{
  body{background:#fff;color:#000;overflow:auto}
  body::before,body::after,.right-nav,.back-top,.hamburger,#loader,.sidebar-actions,.sidebar-copy,.avatar-dot,#theme-toggle{display:none!important}
  .layout{display:block;padding:0;height:auto}
  .sidebar{width:100%;border:none;border-radius:0;box-shadow:none;padding:16px 0}
  .content{height:auto;overflow:visible;border:none;border-radius:0;box-shadow:none;padding:0}
  .section-title{color:#000;font-size:18px}
  .section-underline{background:#000}
  a{color:#000}
  .card-dark,.cert-card,.award-card{border:1px solid #ddd;break-inside:avoid}
  img{max-width:300px}
}
</style>
</head>
<body>
<a href="#about" class="skip-link" style="position:absolute;top:-60px;left:12px;background:var(--cyan);color:#08121e;padding:8px 16px;border-radius:0 0 8px 8px;font-weight:700;font-size:13px;z-index:10000;transition:top .2s;text-decoration:none" onfocus="this.style.top='0'" onblur="this.style.top='-60px'">Skip to main content</a>
<div id="loader"><div class="loader-ring"></div><p style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:1px"><?=$name?></p></div>
<button id="theme-toggle" aria-label="Toggle dark/light mode" title="Toggle theme">
  <i class="fa fa-moon" aria-hidden="true"></i>
</button>
<button class="back-top" id="back-top" aria-label="Back to top" onclick="document.querySelector('.content').scrollTo({top:0,behavior:'smooth'})">
  <i class="fa fa-arrow-up" aria-hidden="true"></i> Top
</button>
<button class="hamburger" id="hamburger" aria-label="Toggle navigation menu" onclick="toggleSidebar()">&#9776;</button>

<div class="layout">
<aside class="sidebar" id="sidebar">
  <div class="sidebar-profile">
    <div class="avatar-wrap">
      <div class="avatar-glow"></div>
      <div class="avatar-ring"></div>
      <img src="<?=$avatar?>" alt="<?=$name?>" loading="eager" />
      <div class="avatar-dot"></div>
    </div>
    <?php $nameParts = explode(' ', $p['full_name']??$name); $fn = implode(' ', array_slice($nameParts,0,2)); $ln = implode(' ', array_slice($nameParts,2)); ?>
    <div class="profile-name"><?=h($fn)?><br><span><?=h($ln)?></span></div>
    <div class="profile-role"><?=$role?></div>
    <!-- Quick stats -->
    <div class="sidebar-stats">
      <div class="sidebar-stat">
        <div class="sidebar-stat-num"><?=$statYears?><small>+</small></div>
        <div class="sidebar-stat-lbl">Years</div>
      </div>
      <div class="sidebar-stat">
        <div class="sidebar-stat-num"><?=$statProjects?><small>+</small></div>
        <div class="sidebar-stat-lbl">Projects</div>
      </div>
      <div class="sidebar-stat">
        <div class="sidebar-stat-num"><small style="color:var(--violet)">🏆</small><?=$statAwards?></div>
        <div class="sidebar-stat-lbl">Awards</div>
      </div>
    </div>
    <div class="social-links">
      <?php if($p['facebook_url']??''): ?><a href="<?=h($p['facebook_url'])?>" target="_blank" rel="noopener noreferrer" aria-label="Follow on Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
      <?php if($p['tiktok_url']??''): ?><a href="<?=h($p['tiktok_url'])?>" target="_blank" rel="noopener noreferrer" aria-label="Follow on TikTok"><i class="fab fa-tiktok"></i></a><?php endif; ?>
      <?php if($p['whatsapp_url']??''): ?><a href="<?=h($p['whatsapp_url'])?>" target="_blank" rel="noopener noreferrer" aria-label="Message on WhatsApp"><i class="fab fa-whatsapp"></i></a><?php endif; ?>
      <?php if($p['linkedin_url']??''): ?><a href="<?=h($p['linkedin_url'])?>" target="_blank" rel="noopener noreferrer" aria-label="Connect on LinkedIn"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
      <?php if($p['youtube_url']??''): ?><a href="<?=h($p['youtube_url'])?>" target="_blank" rel="noopener noreferrer" aria-label="Subscribe on YouTube"><i class="fab fa-youtube"></i></a><?php endif; ?>
    </div>
  </div>
  <div class="sidebar-divider"></div>
  <div class="sidebar-actions">
    <a href="<?=$cvFile?>" download class="btn-cv"><i class="fa fa-download"></i> Download CV</a>
    <a href="#contact" class="btn-msg"><i class="fa fa-paper-plane"></i> Send Message</a>
  </div>
  <div class="sidebar-copy">&copy; <?=date('Y')?> <?=$name?>. All rights reserved.</div>
</aside>

<?php $navItems = [
  ['about','user','About'],['education','graduation-cap','Education'],['experience','briefcase','Work Experience'],
  ['training','certificate','Training'],['awards','trophy','Awards'],['research','book-open','Research'],
  ['news','newspaper','News'],['skills','chart-bar','Skills'],['services','handshake','Services'],['projects','laptop-code','Projects'],
  ['videos','play-circle','Videos'],['portfolio','image','Portfolio'],['interests','heart','Interests'],['contact','envelope','Contact']
]; ?>

<!-- RIGHT FLOATING ICON NAV -->
<nav class="right-nav" id="right-nav" aria-label="Section navigation">
  <?php foreach($navItems as $n): ?>
  <a href="#<?=$n[0]?>" data-section="<?=$n[0]?>" aria-label="<?=$n[2]?>">
    <i class="fa fa-<?=$n[1]?>" aria-hidden="true"></i>
    <span class="rnav-tip"><?=$n[2]?></span>
  </a>
  <?php endforeach; ?>
</nav>

<main class="content">

<!-- ABOUT -->
<section id="about">
  <div class="hero">
    <div class="hero-avatar-box">
      <?php if($avatar && strpos($avatar,'avatar') === false): ?>
        <img src="<?=$avatar?>" alt="<?=$name?>" />
      <?php else: ?>
        <div class="hero-avatar-initials"><?=strtoupper(substr($firstName,0,1).substr(explode(' ',$name)[count(explode(' ',$name))-1],0,1))?></div>
      <?php endif; ?>
      <div class="hero-corner"><?=htmlspecialchars($role ?: 'Portfolio')?></div>
    </div>
    <div class="hero-text">
      <div class="hero-eyebrow"><span class="hero-pulse"></span> Welcome to my portfolio</div>
      <h1>Hi, I'm <span><?=h($firstName)?>!</span></h1>
      <p class="hero-sub"><?=$title?></p>
      <div class="meta-row">
        <?php if($location): ?><div class="meta-item"><i class="fa fa-map-marker-alt"></i><?=$location?></div><?php endif; ?>
        <?php if($email): ?><div class="meta-item"><i class="fa fa-envelope"></i><a href="mailto:<?=$email?>"><?=$email?></a></div><?php endif; ?>
        <?php if($phone): ?><div class="meta-item"><i class="fab fa-whatsapp"></i><?=$phone?></div><?php endif; ?>
      </div>
      <!-- Hero stats bar -->
      <div class="hero-stats-row">
        <?php foreach($heroStats as $s): ?>
        <div class="hero-stat"><div class="hero-stat-num"><?=$s['num']?></div><div class="hero-stat-lbl"><?=htmlspecialchars($s['label'])?></div></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <p style="font-size:14px;color:var(--muted);line-height:1.85;margin-bottom:28px;max-width:660px"><?=$bio?></p>

  <?php if($servicesAbout): ?>
  <div class="sec-label">Services I Offer</div>
  <div class="services-grid">
    <?php foreach($servicesAbout as $s): ?>
    <div class="service-card">
      <div class="service-icon"><i class="fa fa-<?=h($s['icon'])?>"></i></div>
      <div class="service-name"><?=h($s['name'])?></div>
      <div class="service-desc"><?=h($s['description'])?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="sec-label">Profile Details</div>
  <div class="card-dark">
    <table class="profile-table">
      <tr><td>Full Name</td><td><?=$name?></td></tr>
      <tr><td>Email</td><td><a href="mailto:<?=$email?>"><?=$email?></a></td></tr>
      <tr><td>Company</td><td><a href="<?=$compUrl?>" target="_blank"><?=$company?></a></td></tr>
      <?php if($born): ?><tr><td>Born</td><td><?=$born?></td></tr><?php endif; ?>
      <tr><td>Phone</td><td><?=$phone?></td></tr>
      <tr><td>Location</td><td><?=$location?></td></tr>
      <tr><td>Role</td><td><?=$role?></td></tr>
    </table>
  </div>
</section>

<!-- EDUCATION -->
<section id="education">
  <h2 class="section-title">Education</h2><div class="section-underline"></div>
  <?php if($education): ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px">
    <?php foreach($education as $i=>$e): ?>
    <div class="card-dark" style="border-left:3px solid <?=$i===0?'#a78bfa':'var(--cyan)'?>">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:8px">
        <span class="tag"><?=h($e['degree_code'])?></span>
        <span style="font-size:11px;color:var(--muted);white-space:nowrap"><?=h($e['period'])?></span>
      </div>
      <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px"><?=h($e['degree_name'])?></div>
      <div style="font-size:12px;color:<?=$i===0?'#a78bfa':'var(--cyan)'?>;font-weight:600"><?=h($e['institution'])?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No education entries found.</p>
  <?php endif; ?>
</section>

<!-- WORK EXPERIENCE -->
<section id="experience">
  <h2 class="section-title">Work Experience</h2><div class="section-underline"></div>
  <?php if($experiences): ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px">
    <?php foreach($experiences as $exp): $isViolet = $exp['color']==='violet'; $isCurrent = stripos($exp['period'],'present')!==false; ?>
    <div class="card-dark" style="border-left:3px solid <?=$isViolet?'#a78bfa':'var(--cyan)'?>">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:6px">
        <div style="font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.3px"><?=h($exp['company'])?></div>
        <?php if($isCurrent): ?><span style="font-size:8px;background:rgba(34,211,238,.15);color:var(--cyan);padding:2px 8px;border-radius:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px">Current</span><?php endif; ?>
      </div>
      <div style="font-size:12px;color:<?=$isViolet?'#a78bfa':'var(--cyan)'?>;font-weight:600;margin-bottom:6px"><?=h($exp['role'])?></div>
      <div style="font-size:11px;color:var(--muted);margin-bottom:8px"><?=h($exp['period'])?></div>
      <p style="font-size:12px;color:var(--muted);line-height:1.6"><?=h($exp['description'])?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No experience entries found.</p>
  <?php endif; ?>
</section>

<!-- TRAINING -->
<section id="training">
  <h2 class="section-title">Training &amp; Certificates</h2><div class="section-underline"></div>
  <?php if($training): ?>
  <div class="training-grid">
    <?php foreach($training as $c): ?>
    <div class="cert-card">
      <div class="cert-icon-wrap"><i class="fa fa-<?=h($c['icon'])?>"></i></div>
      <div style="flex:1">
        <div class="cert-name"><?=h($c['name'])?></div>
        <div class="cert-org"><?=h($c['organizer'])?></div>
        <div class="cert-year"><?=h($c['year'])?></div>
        <?php if($c['certificate_url'] || $c['certificate_file']): ?>
        <div style="margin-top:6px">
          <?php $certHref = $c['certificate_url'] ?: $c['certificate_file']; ?>
          <a href="<?=h($certHref)?>" target="_blank" style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--cyan);text-decoration:none;background:rgba(34,211,238,.06);border:1px solid rgba(34,211,238,.2);padding:3px 9px;border-radius:20px;transition:.2s" onmouseover="this.style.background='rgba(34,211,238,.14)'" onmouseout="this.style.background='rgba(34,211,238,.06)'">
            <i class="fa fa-certificate" style="font-size:10px"></i> View Certificate
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No training entries found.</p>
  <?php endif; ?>
</section>

<!-- AWARDS -->
<section id="awards">
  <h2 class="section-title">Awards &amp; Nominations</h2><div class="section-underline"></div>
  <?php if($awards): ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px">
    <?php foreach($awards as $a): $isViolet=$a['color']==='violet'; ?>
    <div class="award-card <?=$isViolet?'violet':'cyan'?>">
      <div class="award-badge" style="background:rgba(<?=$isViolet?'139,92,246':'34,211,238'?>,.1);color:<?=$isViolet?'#a78bfa':'var(--cyan)'?>">
        <i class="fa <?=h($a['icon'])?>"></i> <?=h($a['year'])?>
      </div>
      <div style="font-size:17px;font-weight:900;color:var(--text);margin-bottom:4px;letter-spacing:-.2px"><?=h($a['title'])?></div>
      <div style="font-size:12px;color:<?=$isViolet?'#a78bfa':'var(--cyan)'?>;margin-bottom:10px;font-weight:600"><?=h($a['organization'])?></div>
      <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:14px"><?=h($a['description'])?></p>
      <?php if($a['image1'] || $a['image2']): ?>
      <div style="display:grid;grid-template-columns:<?=($a['image1']&&$a['image2'])?'1fr 1fr':'1fr'?>;gap:12px">
        <?php if($a['image1']): ?><img src="<?=h($a['image1'])?>" alt="<?=h($a['title'])?>" loading="lazy" style="width:100%;height:160px;object-fit:cover;border-radius:8px;border:1px solid var(--border)" /><?php endif; ?>
        <?php if($a['image2']): ?><img src="<?=h($a['image2'])?>" alt="" loading="lazy" style="width:100%;height:160px;object-fit:cover;border-radius:8px;border:1px solid var(--border)" /><?php endif; ?>
      </div>
      <?php endif; ?>
      <?php if($a['url']): ?><div style="margin-top:12px"><a href="<?=h($a['url'])?>" target="_blank" rel="noopener noreferrer" class="tag" style="font-size:11px">View Certificate ↗</a></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No awards found.</p>
  <?php endif; ?>
</section>

<!-- RESEARCH -->
<section id="research">
  <h2 class="section-title">Research Publications</h2><div class="section-underline"></div>
  <?php if($research): ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px">
    <?php foreach($research as $paper): ?>
    <div class="card-dark">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
        <div style="width:36px;height:36px;background:rgba(34,211,238,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--cyan)"><i class="fa fa-file-pdf"></i></div>
        <?php if($paper['year']): ?><span style="font-size:11px;color:var(--muted);background:var(--border);padding:3px 10px;border-radius:20px"><?=h($paper['year'])?></span><?php endif; ?>
      </div>
      <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;line-height:1.4"><?=h($paper['title'])?></div>
      <?php if($paper['journal']): ?><div style="font-size:11px;color:var(--cyan);margin-bottom:8px"><i class="fa fa-book" style="margin-right:4px"></i><?=h($paper['journal'])?></div><?php endif; ?>
      <p style="font-size:12px;color:var(--muted);line-height:1.65;margin-bottom:12px"><?=h($paper['description'])?></p>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        <?php if($paper['pdf_file']): ?><a href="<?=h($paper['pdf_file'])?>" target="_blank" class="tag" style="font-size:11px"><i class="fa fa-download" style="margin-right:4px"></i>PDF</a><?php endif; ?>
        <?php if($paper['url']): ?><a href="<?=h($paper['url'])?>" target="_blank" class="tag tag-violet-accent"><i class="fa fa-globe"></i>Online ↗</a><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No research publications found.</p>
  <?php endif; ?>
</section>

<!-- NEWS -->
<section id="news">
  <h2 class="section-title">News &amp; Online Publications</h2><div class="section-underline"></div>
  <?php if($newsItems): ?>
  <div class="news-grid">
    <?php foreach($newsItems as $n):
      $href = $n['url'] ?: ($n['pdf_file'] ?: '#'); ?>
    <a href="<?=h($href)?>" target="_blank" rel="noopener noreferrer" class="news-card" style="text-decoration:none">
      <?php if($n['image']): ?>
        <img src="<?=h($n['image'])?>" alt="<?=h($n['title'])?>" class="news-img" loading="lazy" />
      <?php else: ?>
        <div class="news-thumb"><i class="news-thumb-icon fa fa-newspaper"></i></div>
      <?php endif; ?>
      <div class="news-body">
        <div class="news-title"><?=h($n['title'])?></div>
        <div class="news-source"><?=h($n['source'])?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No news items found.</p>
  <?php endif; ?>
</section>

<!-- SKILLS -->
<section id="skills">
  <h2 class="section-title">Skills</h2><div class="section-underline"></div>
  <?php if($skillGroups): ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px">
    <?php foreach($skillGroups as $cat => $items): ?>
    <div class="card-dark">
      <div style="font-size:11px;color:var(--cyan);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;display:flex;align-items:center;gap:6px"><span style="width:20px;height:20px;border-radius:5px;background:rgba(34,211,238,.1);border:1px solid rgba(34,211,238,.2);display:inline-flex;align-items:center;justify-content:center;font-size:10px"><i class="fa fa-star"></i></span><?=h($cat)?> Skills</div>
      <?php foreach($items as $s): ?>
      <div class="skill-row">
        <div class="skill-name"><?=h($s['name'])?></div>
        <div class="skill-bar">
          <div class="skill-fill" style="width:0%;background:linear-gradient(90deg,var(--cyan),#06b6d4)" data-level="<?=(int)$s['level']?>"
               role="progressbar" aria-valuenow="<?=(int)$s['level']?>" aria-valuemin="0" aria-valuemax="100"
               aria-label="<?=h($s['name'])?>: <?=(int)$s['level']?>%"></div>
        </div>
        <div class="skill-pct"><?=(int)$s['level']?>%</div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No skills found.</p>
  <?php endif; ?>
</section>

<!-- DIGITAL SERVICES -->
<section id="services">
  <h2 class="section-title">Digital Services</h2><div class="section-underline"></div>
  <p style="font-size:13px;color:var(--muted);margin-bottom:20px;max-width:600px;line-height:1.7">
    Need a professional website, reliable web hosting, or custom email hosting? I provide end-to-end digital solutions tailored for businesses, cooperatives, and startups in Nepal.
  </p>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:24px">
    <!-- Website Development -->
    <div class="card-dark" style="border-left:3px solid var(--cyan)">
      <div style="font-size:26px;margin-bottom:12px">🌐</div>
      <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:8px">Website Development</div>
      <div style="font-size:24px;font-weight:900;color:var(--cyan);margin-bottom:4px">From NPR 15,000</div>
      <div style="font-size:11px;color:var(--muted);margin-bottom:14px">One-time • Custom design included</div>
      <ul style="font-size:12px;color:var(--muted);line-height:1.9;padding-left:16px;margin-bottom:16px">
        <li>Responsive & mobile-friendly design</li>
        <li>SEO optimized structure</li>
        <li>Contact forms & analytics</li>
        <li>Free 30-day support</li>
        <li>Portfolio, corporate, cooperative sites</li>
      </ul>
      <a href="#contact" class="tag" style="display:inline-block;text-align:center;width:100%;background:rgba(34,211,238,.1);border-color:rgba(34,211,238,.2)">Request a Quote →</a>
    </div>
    <!-- Web Hosting -->
    <div class="card-dark" style="border-left:3px solid var(--violet)">
      <div style="font-size:26px;margin-bottom:12px">☁️</div>
      <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:8px">Web Hosting</div>
      <div style="font-size:24px;font-weight:900;color:var(--violet);margin-bottom:4px">From NPR 3,000<span style="font-size:11px;font-weight:400;color:var(--muted)"> /year</span></div>
      <div style="font-size:11px;color:var(--muted);margin-bottom:14px">Annual plans • Nepal-based servers</div>
      <ul style="font-size:12px;color:var(--muted);line-height:1.9;padding-left:16px;margin-bottom:16px">
        <li>99.9% uptime guarantee</li>
        <li>Free SSL certificate</li>
        <li>Daily backups</li>
        <li>cPanel / managed options</li>
        <li>Email accounts included</li>
      </ul>
      <a href="#contact" class="tag" style="display:inline-block;text-align:center;width:100%;background:rgba(139,92,246,.1);border-color:rgba(139,92,246,.2);color:var(--violet)">Get Hosting →</a>
    </div>
    <!-- Email Hosting -->
    <div class="card-dark" style="border-left:3px solid var(--yellow)">
      <div style="font-size:26px;margin-bottom:12px">📧</div>
      <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:8px">Professional Email Hosting</div>
      <div style="font-size:24px;font-weight:900;color:var(--yellow);margin-bottom:4px">From NPR 1,500<span style="font-size:11px;font-weight:400;color:var(--muted)"> /year</span></div>
      <div style="font-size:11px;color:var(--muted);margin-bottom:14px">Annual plans • Your domain @you.com</div>
      <ul style="font-size:12px;color:var(--muted);line-height:1.9;padding-left:16px;margin-bottom:16px">
        <li>yourname@yourdomain.com</li>
        <li>5 GB storage per mailbox</li>
        <li>Webmail + IMAP/SMTP access</li>
        <li>Spam & virus protection</li>
        <li>Calendar & contacts sync</li>
      </ul>
      <a href="#contact" class="tag" style="display:inline-block;text-align:center;width:100%;background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.2);color:var(--yellow)">Setup Email →</a>
    </div>
    <!-- Cyber Security Training -->
    <div class="card-dark" style="border-left:3px solid var(--red)">
      <div style="font-size:26px;margin-bottom:12px">🔒</div>
      <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:8px">Cyber Security Training</div>
      <div style="font-size:24px;font-weight:900;color:var(--red);margin-bottom:4px">NPR 20,000</div>
      <div style="font-size:11px;color:var(--muted);margin-bottom:14px">Per person • Group discounts available</div>
      <ul style="font-size:12px;color:var(--muted);line-height:1.9;padding-left:16px;margin-bottom:16px">
        <li>Network & endpoint security</li>
        <li>Phishing & social engineering awareness</li>
        <li>Data privacy & compliance</li>
        <li>Incident response basics</li>
        <li>For businesses, IT teams & individuals</li>
      </ul>
      <a href="#contact" class="tag" style="display:inline-block;text-align:center;width:100%;background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.2);color:var(--red)">Enroll Now →</a>
    </div>
  </div>
  <div style="background:rgba(34,211,238,.04);border:1px solid rgba(34,211,238,.12);border-radius:12px;padding:16px 20px;font-size:13px;color:var(--muted);line-height:1.7;max-width:700px">
    💡 All packages include <strong style="color:var(--text)">free consultation</strong>. For cooperative organizations, NGOs, and educational institutions — special discounted rates available. Contact me to discuss your project requirements.
  </div>
</section>

<!-- PROJECTS -->
<section id="projects">
  <h2 class="section-title">Projects</h2><div class="section-underline"></div>
  <?php if($projects): ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px">
  <?php foreach($projects as $proj): $tags = array_filter(array_map('trim', explode(',', $proj['tags']??''))); ?>
  <div class="card-dark">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:10px">
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--text)"><?=h($proj['title'])?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px"><?=h($proj['subtitle'])?></div>
      </div>
      <?php if($proj['url']): ?><a href="<?=h($proj['url'])?>" target="_blank" rel="noopener noreferrer" class="tag" style="font-size:11px;white-space:nowrap">Live Site ↗</a><?php endif; ?>
    </div>
    <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:12px"><?=h($proj['description'])?></p>
    <?php if($tags): ?><div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px"><?php foreach($tags as $t): ?><span class="tag"><?=h($t)?></span><?php endforeach; ?></div><?php endif; ?>
    <?php if($proj['image1'] || $proj['image2']): ?>
    <div style="display:grid;grid-template-columns:<?=($proj['image1']&&$proj['image2'])?'1fr 1fr':'1fr'?>;gap:10px">
      <?php if($proj['image1']): ?><img src="<?=h($proj['image1'])?>" alt="<?=h($proj['title'])?>" loading="lazy" style="width:100%;height:<?=($proj['image1']&&$proj['image2'])?'160':'200'?>px;object-fit:cover;border-radius:8px;border:1px solid var(--border)" /><?php endif; ?>
      <?php if($proj['image2']): ?><img src="<?=h($proj['image2'])?>" alt="" loading="lazy" style="width:100%;height:160px;object-fit:cover;border-radius:8px;border:1px solid var(--border)" /><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No projects found.</p>
  <?php endif; ?>
</section>

<!-- VIDEOS -->
<section id="videos">
  <h2 class="section-title">Videos</h2><div class="section-underline"></div>
  <div class="video-grid">
    <?php if($p['youtube_url']??''): ?>
    <a href="<?=h($p['youtube_url'])?>" target="_blank" rel="noopener noreferrer" class="video-card" style="text-decoration:none">
      <div style="background:linear-gradient(135deg,#ff0000,#cc0000);padding:40px;text-align:center">
        <i class="fab fa-youtube" style="font-size:52px;color:var(--text)"></i>
      </div>
      <div style="padding:20px">
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px">YouTube Channel</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:12px">Digital transformation, cooperative management, and FinTech insights from Nepal.</div>
        <span class="tag" style="background:rgba(255,0,0,.1);color:var(--red);border-color:rgba(255,0,0,.2)">@aakashpame ↗</span>
      </div>
    </a>
    <?php endif; ?>
    <?php if($p['tiktok_url']??''): ?>
    <a href="<?=h($p['tiktok_url'])?>" target="_blank" rel="noopener noreferrer" class="video-card" style="text-decoration:none">
      <div style="background:linear-gradient(135deg,#010101,#2d2d2d);padding:40px;text-align:center">
        <i class="fab fa-tiktok" style="font-size:52px;color:var(--text)"></i>
      </div>
      <div style="padding:20px">
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px">TikTok Profile</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:12px">Short-form content on Nepal's digital and cooperative sector transformation.</div>
        <span class="tag" style="background:var(--meta-bg);color:var(--muted);border-color:var(--border)">@tankaadhikari34 ↗</span>
      </div>
    </a>
    <?php endif; ?>
  </div>
</section>

<!-- PORTFOLIO -->
<section id="portfolio">
  <h2 class="section-title">Portfolio</h2><div class="section-underline"></div>
  <?php if($portfolioSites): ?>
  <h3 style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px">Websites Developed</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:28px">
    <?php foreach($portfolioSites as $s): ?>
    <?php if($s['url']): ?>
    <a href="<?=h($s['url'])?>" target="_blank" rel="noopener noreferrer" class="port-item">
      <img src="<?=h($s['image'])?>" alt="<?=h($s['title'])?>" loading="lazy" />
      <div class="port-info">
        <p><?=h($s['title'])?></p>
        <span><?=h($s['subtitle'])?></span>
      </div>
    </a>
    <?php else: ?>
    <div class="port-item">
      <img src="<?=h($s['image'])?>" alt="<?=h($s['title'])?>" loading="lazy" />
      <div class="port-info">
        <p><?=h($s['title'])?></p>
        <span><?=h($s['subtitle'])?></span>
      </div>
    </div>
    <?php endif; endforeach; ?>
  </div>
  <?php endif; ?>
  <h3 style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px">Photo Gallery</h3>
  <div class="gallery-grid">
    <?php
    $gallery = ['img/powerlist.jpg','img/powerlist2.jpg','img/ict.jpg','img/ict2.jpg','img/aakashdms1.jpg','img/aakashdms2.jpg','img/avatar.jpg'];
    foreach($gallery as $g): ?>
    <div class="gallery-item" onclick="openLightbox('<?=h($g)?>')" role="button" tabindex="0" aria-label="View photo" onkeydown="if(event.key==='Enter'||event.key===' ')openLightbox('<?=h($g)?>')"><img src="<?=h($g)?>" alt="Gallery photo" loading="lazy" /></div>
    <?php endforeach; ?>
  </div>
</section>

<!-- INTERESTS -->
<section id="interests">
  <h2 class="section-title">Interests</h2><div class="section-underline"></div>
  <?php if($interests): ?>
  <div class="interests-grid">
    <?php foreach($interests as $i): ?>
    <div class="interest-card">
      <div class="interest-icon"><i class="fa fa-<?=h($i['icon'])?>"></i></div>
      <div class="interest-name"><?=h($i['name'])?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p style="color:var(--muted);font-size:13px">No interests found.</p>
  <?php endif; ?>
</section>

<!-- CONTACT -->
<section id="contact">
  <h2 class="section-title">Contact</h2><div class="section-underline"></div>
  <div class="contact-grid">
    <?php if($email): ?><div class="contact-card"><div class="contact-icon"><i class="fa fa-envelope"></i></div><div><div style="font-size:11px;color:var(--muted);font-weight:600">Email</div><a href="mailto:<?=$email?>" style="font-size:13px"><?=$email?></a></div></div><?php endif; ?>
    <?php if($phone): ?><div class="contact-card"><div class="contact-icon"><i class="fa fa-phone"></i></div><div><div style="font-size:11px;color:var(--muted);font-weight:600">Phone</div><div style="font-size:13px;color:var(--text)"><?=$phone?></div></div></div><?php endif; ?>
    <?php if($location): ?><div class="contact-card"><div class="contact-icon"><i class="fa fa-map-marker-alt"></i></div><div><div style="font-size:11px;color:var(--muted);font-weight:600">Location</div><div style="font-size:13px;color:var(--text)"><?=$location?></div></div></div><?php endif; ?>
    <?php if($company): ?><div class="contact-card"><div class="contact-icon"><i class="fa fa-building"></i></div><div><div style="font-size:11px;color:var(--muted);font-weight:600">Company</div><a href="<?=$compUrl?>" target="_blank" rel="noopener noreferrer" style="font-size:13px"><?=$company?></a></div></div><?php endif; ?>
  </div>
  <div class="card-dark">
    <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:16px">Send a Message</div>
    <div id="form-msg" role="alert" aria-live="polite" style="display:none"></div>
    <form id="contact-form" onsubmit="sendMessage(event)" novalidate>
      <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden" aria-hidden="true">
        <label for="website-url">Website (leave blank)</label>
        <input type="text" id="website-url" name="website" tabindex="-1" autocomplete="off" />
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
          <label for="contact-name">Your Name <span aria-label="required">*</span></label>
          <input type="text" id="contact-name" name="name" required autocomplete="name" />
        </div>
        <div class="form-group">
          <label for="contact-email">Email <span aria-label="required">*</span></label>
          <input type="email" id="contact-email" name="email" required autocomplete="email" />
        </div>
      </div>
      <div class="form-group">
        <label for="contact-subject">Subject</label>
        <input type="text" id="contact-subject" name="subject" autocomplete="off" />
      </div>
      <div class="form-group">
        <label for="contact-message">Message <span aria-label="required">*</span></label>
        <textarea id="contact-message" name="message" rows="5" required maxlength="3000"></textarea>
      </div>
      <button class="btn-send" type="submit" id="send-btn"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send Message</button>
    </form>
  </div>
</section>

</main>
</div>

<div id="lightbox" onclick="closeLightbox()" role="dialog" aria-modal="true" aria-label="Photo viewer" aria-hidden="true">
  <button id="lightbox-close" onclick="closeLightbox()" aria-label="Close photo viewer">✕</button>
  <img id="lightbox-img" src="" alt="Gallery photo — enlarged view" onclick="event.stopPropagation()" />
</div>

<script>
// ── Loader ──────────────────────────────────────────────────────────────────
window.addEventListener('load', () => {
  setTimeout(() => {
    const l = document.getElementById('loader');
    l.style.opacity = '0';
    setTimeout(() => l.style.display = 'none', 400);
  }, 400);
  // Scroll to hash on load — using content card scroll, not window
  const hash = location.hash.slice(1);
  if (hash) {
    const el = document.getElementById(hash);
    const cc = document.querySelector('.content');
    if (el && cc) setTimeout(() => cc.scrollTo({ top: el.offsetTop - 20, behavior: 'smooth' }), 600);
  }
});

// ── Dark / Light Theme Toggle ────────────────────────────────────────────────
const toggleBtn = document.getElementById('theme-toggle');
const htmlEl   = document.documentElement;
const toggleIcon = toggleBtn?.querySelector('i');

function applyTheme(theme) {
  htmlEl.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
  if (toggleIcon) {
    toggleIcon.className = theme === 'dark' ? 'fa fa-moon' : 'fa fa-sun';
  }
}

// Init: saved preference or default dark
const saved = localStorage.getItem('theme');
applyTheme(saved || 'dark');

toggleBtn?.addEventListener('click', () => {
  const current = htmlEl.getAttribute('data-theme');
  applyTheme(current === 'dark' ? 'light' : 'dark');
});

// ── Back-to-top visibility ───────────────────────────────────────────────────
document.querySelector('.content')?.addEventListener('scroll', function() {
  const btn = document.getElementById('back-top');
  if (btn) btn.classList.toggle('visible', this.scrollTop > 300);
}, { passive: true });

// ── Sidebar toggle (mobile) ──────────────────────────────────────────────────
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); }

// ── Right-nav + content scroll on nav click ──────────────────────────────────
const contentEl = document.querySelector('.content');

document.querySelectorAll('.right-nav a[data-section]').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const id = link.getAttribute('data-section');
    const target = document.getElementById(id);
    if (target && contentEl) {
      const offset = target.offsetTop - 20;
      contentEl.scrollTo({ top: offset, behavior: 'smooth' });
      history.pushState(null, '', '#' + id);
    }
  });
});

// ── "Send Message" button scrolls to contact ────────────────────────────────
document.querySelector('.btn-msg')?.addEventListener('click', (e) => {
  e.preventDefault();
  const target = document.getElementById('contact');
  if (target && contentEl) contentEl.scrollTo({ top: target.offsetTop - 20, behavior: 'smooth' });
});

// ── Scroll Spy via right-nav ─────────────────────────────────────────────────
const sections = document.querySelectorAll('main section[id]');
const rnavLinks = document.querySelectorAll('.right-nav a[data-section]');

function setActive(id) {
  rnavLinks.forEach(a => {
    const isActive = a.getAttribute('data-section') === id;
    a.classList.toggle('active', isActive);
    if (isActive) a.setAttribute('aria-current', 'page');
    else a.removeAttribute('aria-current');
  });
  history.replaceState(null, '', '#' + id);
}

setActive('about');

const spyObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => { if (entry.isIntersecting) setActive(entry.target.id); });
}, {
  root: contentEl,
  rootMargin: '-25% 0px -65% 0px',
  threshold: 0
});
sections.forEach(sec => spyObserver.observe(sec));

// ── Skill bar animation on scroll ───────────────────────────────────────────
function animateSkills() {
  document.querySelectorAll('.skill-fill').forEach(bar => {
    if (!bar.dataset.animated) {
      bar.style.width = bar.dataset.level + '%';
      bar.dataset.animated = '1';
    }
  });
}
const skillsObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => { if (entry.isIntersecting) animateSkills(); });
}, { threshold: 0.15 });
const skillsSection = document.getElementById('skills');
if (skillsSection) skillsObserver.observe(skillsSection);

async function sendMessage(e) {
  e.preventDefault();
  const btn = document.getElementById('send-btn');
  const form = document.getElementById('contact-form');
  const msg = document.getElementById('form-msg');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
  const data = {
    name: form.querySelector('[name="name"]').value,
    email: form.querySelector('[name="email"]').value,
    subject: form.querySelector('[name="subject"]').value,
    message: form.querySelector('[name="message"]').value,
    website: form.querySelector('[name="website"]').value
  };
  try {
    const res = await fetch('contact.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data) });
    const json = await res.json();
    if (json.response === 'success') {
      msg.style.cssText='display:block;background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);color:var(--cyan);border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:14px';
      msg.textContent = '✅ Message sent! Thank you, we will get back to you soon.';
      form.reset();
    } else { throw new Error(json.error || 'Something went wrong. Please try again.'); }
  } catch(err) {
    msg.style.cssText='display:block;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:var(--red);border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:14px';
    msg.textContent = '❌ ' + err.message;
  }
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Message';
}

function openLightbox(src) {
  const lb = document.getElementById('lightbox');
  document.getElementById('lightbox-img').src = src;
  lb.classList.add('open');
  lb.setAttribute('aria-hidden', 'false');
  document.getElementById('lightbox-close').focus();
}
function closeLightbox() {
  const lb = document.getElementById('lightbox');
  lb.classList.remove('open');
  lb.setAttribute('aria-hidden', 'true');
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeLightbox(); });
</script>
</body>
</html>
