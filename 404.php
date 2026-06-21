<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>404 — Page Not Found | Tanka Prasad Adhikari</title>
<meta name="robots" content="noindex, follow">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#0d1117;color:#c9d1e3;min-height:100vh;display:flex;align-items:center;justify-content:center;
  background-image:radial-gradient(circle,rgba(34,211,238,.05) 1px,transparent 1px);background-size:28px 28px}
.wrap{text-align:center;max-width:480px;padding:40px 20px}
.code{font-size:120px;font-weight:900;color:transparent;background:linear-gradient(135deg,#22d3ee,#8b5cf6);-webkit-background-clip:text;background-clip:text;line-height:1;margin-bottom:8px}
h1{font-size:24px;font-weight:700;color:#fff;margin-bottom:12px}
p{font-size:14px;color:#64748b;line-height:1.6;margin-bottom:32px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#22d3ee;border-radius:10px;color:#0d1117;font-weight:700;font-size:14px;text-decoration:none;transition:.2s}
.btn:hover{background:#06b6d4}
.btn-ghost{background:transparent;border:1px solid #1e2638;color:#8892a4;margin-left:10px}
.btn-ghost:hover{background:#161b27;color:#c9d1e3}
</style>
</head>
<body>
<div class="wrap">
  <div class="code">404</div>
  <h1>Page Not Found</h1>
  <p>The page you are looking for doesn't exist or has been moved.<br>Let's get you back on track.</p>
  <a href="/" class="btn">← Go to Homepage</a>
  <a href="javascript:history.back()" class="btn btn-ghost">Go Back</a>
</div>
</body>
</html>
