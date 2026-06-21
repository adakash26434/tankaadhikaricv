<?php if(!defined('ADMIN_PAGE')) die('Direct access not allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?=$pageTitle??'Admin'?> — Portfolio Admin</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#0f1420;color:#c9d1e3;min-height:100vh;display:flex;flex-direction:column}
a{color:#22d3ee;text-decoration:none}
.topbar{background:#161b27;border-bottom:1px solid #1e2638;display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:56px;position:sticky;top:0;z-index:100;flex-wrap:wrap;gap:0}
.topbar-brand{font-weight:700;color:#fff;font-size:15px;display:flex;align-items:center;gap:8px;white-space:nowrap}
.topbar-brand span{color:#22d3ee}
.topbar-nav{display:flex;align-items:center;gap:2px;flex-wrap:wrap}
.nav-link{padding:5px 10px;border-radius:6px;font-size:12px;color:#8892a4;transition:.15s;white-space:nowrap}
.nav-link:hover,.nav-link.active{background:#1e2638;color:#c9d1e3}
.topbar-right{display:flex;align-items:center;gap:10px}
.btn-logout{padding:6px 14px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s}
.btn-logout:hover{background:rgba(239,68,68,.2)}
.main{padding:28px 20px;max-width:1100px;margin:0 auto;width:100%}
.page-title{font-size:22px;font-weight:700;color:#fff;margin-bottom:6px}
.page-sub{font-size:13px;color:#64748b;margin-bottom:24px}
.card{background:#161b27;border:1px solid #1e2638;border-radius:12px;padding:24px;margin-bottom:20px}
label{display:block;font-size:12px;color:#8892a4;margin-bottom:5px;font-weight:500;margin-top:14px}
label:first-child{margin-top:0}
input[type=text],input[type=email],input[type=url],input[type=number],textarea,select{width:100%;background:#0f1420;border:1px solid #1e2638;color:#c9d1e3;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;transition:border-color .2s;font-family:inherit}
input:focus,textarea:focus,select:focus{border-color:#22d3ee}
textarea{resize:vertical;min-height:80px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:.15s}
.btn-primary{background:#22d3ee;color:#0f1420}
.btn-primary:hover{background:#06b6d4}
.btn-danger{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171}
.btn-danger:hover{background:rgba(239,68,68,.25)}
.btn-secondary{background:#1e2638;color:#c9d1e3}
.btn-secondary:hover{background:#263048}
.alert-success{background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);color:#67e8f9;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:18px}
.alert-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#f87171;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:18px}
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;padding:8px 12px;color:#64748b;border-bottom:1px solid #1e2638;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
td{padding:10px 12px;border-bottom:1px solid #1e263880;vertical-align:top}
tr:last-child td{border-bottom:none}
tr:hover td{background:#1e263840}
.badge{display:inline-flex;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:rgba(34,211,238,.1);color:#22d3ee}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
@media(max-width:768px){.grid-2,.grid-3{grid-template-columns:1fr}.topbar-nav{display:none}}
</style>
</head>
<body>
<div class="topbar">
  <div class="topbar-brand">⚡ <span>Tanka</span> Admin</div>
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
    <a href="upload.php" class="nav-link <?=$cur=='upload.php'?'active':''?>" style="color:#22d3ee">📷 Upload</a>
    <a href="changepassword.php" class="nav-link <?=$cur=='changepassword.php'?'active':''?>" style="color:#a78bfa">🔑 Password</a>
  </nav>
  <div class="topbar-right">
    <a href="../index.php" target="_blank" style="font-size:12px;color:#64748b">View Site ↗</a>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</div>
<div class="main">
<div class="page-title"><?=$pageTitle??'Dashboard'?></div>
<div class="page-sub"><?=$pageSubtitle??''?></div>
