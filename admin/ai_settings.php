<?php
define('ADMIN_PAGE', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$pageTitle = 'AI Chat Settings';
$pageSubtitle = 'Configure AI assistant (ChatGPT, Gemini, DeepSeek) for the public portfolio chat widget.';

$msg = '';
$msgType = 'success';

try {
    // Ensure table exists
    getDB()->exec("CREATE TABLE IF NOT EXISTS ai_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        provider VARCHAR(20) DEFAULT 'openai',
        openai_key VARCHAR(255) DEFAULT '',
        gemini_key VARCHAR(255) DEFAULT '',
        deepseek_key VARCHAR(255) DEFAULT '',
        system_prompt TEXT,
        enabled TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $settings = dbRow("SELECT * FROM ai_settings LIMIT 1");
    if (!$settings) {
        getDB()->exec("INSERT INTO ai_settings (id) VALUES (1)");
        $settings = dbRow("SELECT * FROM ai_settings LIMIT 1");
    }
} catch (Exception $e) {
    $settings = ['provider' => 'openai', 'openai_key' => '', 'gemini_key' => '', 'deepseek_key' => '', 'system_prompt' => '', 'enabled' => 0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $provider    = $_POST['provider'] ?? 'openai';
    $openaiKey   = trim($_POST['openai_key'] ?? '');
    $geminiKey   = trim($_POST['gemini_key'] ?? '');
    $deepseekKey = trim($_POST['deepseek_key'] ?? '');
    $prompt      = trim($_POST['system_prompt'] ?? '');
    $enabled     = isset($_POST['enabled']) ? 1 : 0;

    // Validate provider
    if (!in_array($provider, ['openai', 'gemini', 'deepseek'])) {
        $msg = 'Invalid AI provider selected.';
        $msgType = 'error';
    } else {
        // Check that selected provider has a key
        $hasKey = ($provider === 'openai' && $openaiKey) ||
                  ($provider === 'gemini' && $geminiKey) ||
                  ($provider === 'deepseek' && $deepseekKey);
        if (!$hasKey) {
            $msg = 'Please enter the API key for the selected provider.';
            $msgType = 'error';
        } else {
            $stmt = getDB()->prepare("UPDATE ai_settings SET provider=?, openai_key=?, gemini_key=?, deepseek_key=?, system_prompt=?, enabled=? WHERE id=1");
            $stmt->execute([$provider, $openaiKey, $geminiKey, $deepseekKey, $prompt, $enabled]);
            $msg = 'AI settings saved successfully!';
            $msgType = 'success';
            // Reload
            $settings = dbRow("SELECT * FROM ai_settings WHERE id=1");
        }
    }
}

include __DIR__ . '/header.php';
?>

<?php if ($msg): ?>
<div class="alert <?=$msgType === 'error' ? 'alert-error' : 'alert-success'?>"><?=h($msg)?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <div class="card-header-title">🤖 AI Chat Configuration</div>
  </div>

  <div class="alert alert-warning" style="margin-bottom:20px">
    <div class="alert-icon">⚠️</div>
    <div>
      <strong>Security:</strong> Only PUBLIC portfolio data (name, bio, experience, skills, projects, etc.) 
      is sent to the AI. Admin credentials, passwords, messages, and private data are NEVER sent.
    </div>
  </div>

  <form method="POST">
    <?=csrfField()?>

    <div class="form-group">
      <label>
        <input type="checkbox" name="enabled" value="1" <?=(!empty($settings['enabled']))?'checked':''?> style="width:auto;margin-right:8px" />
        Enable AI Chat Widget on public site
      </label>
    </div>

    <div class="divider"></div>

    <div class="form-group">
      <label>AI Provider</label>
      <select name="provider" id="providerSelect">
        <option value="openai"   <?=($settings['provider']??'')==='openai'?'selected':''?>>OpenAI (ChatGPT)</option>
        <option value="gemini"    <?=($settings['provider']??'')==='gemini'?'selected':''?>>Google Gemini</option>
        <option value="deepseek" <?=($settings['provider']??'')==='deepseek'?'selected':''?>>DeepSeek</option>
      </select>
    </div>

    <div class="form-group">
      <label>OpenAI API Key <span style="color:#ef4444">*</span></label>
      <input type="password" name="openai_key" id="openaiKey" value="<?=h($settings['openai_key'] ?? '')?>" placeholder="sk-..." autocomplete="new-password" />
      <div style="font-size:11px;color:#64748b;margin-top:4px">Required if OpenAI (ChatGPT) is selected as provider.</div>
    </div>

    <div class="form-group">
      <label>Google Gemini API Key <span style="color:#ef4444">*</span></label>
      <input type="password" name="gemini_key" id="geminiKey" value="<?=h($settings['gemini_key'] ?? '')?>" placeholder="AIza..." autocomplete="new-password" />
      <div style="font-size:11px;color:#64748b;margin-top:4px">Get your key at <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">Google AI Studio</a>. Required if Gemini is selected.</div>
    </div>

    <div class="form-group">
      <label>DeepSeek API Key <span style="color:#ef4444">*</span></label>
      <input type="password" name="deepseek_key" id="deepseekKey" value="<?=h($settings['deepseek_key'] ?? '')?>" placeholder="sk-..." autocomplete="new-password" />
      <div style="font-size:11px;color:#64748b;margin-top:4px">Get your key at <a href="https://platform.deepseek.com/api_key" target="_blank" rel="noopener">DeepSeek Platform</a>. Required if DeepSeek is selected.</div>
    </div>

    <div class="form-group">
      <label>System Prompt (Optional)</label>
      <textarea name="system_prompt" rows="4" placeholder="Customize how the AI assistant behaves. Leave blank for default behavior."><?=h($settings['system_prompt'] ?? '')?></textarea>
      <div style="font-size:11px;color:#64748b;margin-top:4px">Default: "You are a helpful AI assistant for a portfolio website. Answer questions based ONLY on the provided portfolio data..."</div>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary">💾 Save AI Settings</button>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-header-title">🔒 Data Privacy</div>
  </div>
  <div style="font-size:13px;color:#c9d1e3;line-height:1.7">
    <p style="margin-bottom:10px">The AI chat widget uses <strong>ONLY</strong> public portfolio information:</p>
    <ul style="margin-left:20px;color:#8892a4;margin-bottom:10px">
      <li>Profile name, title, bio, role, company, location</li>
      <li>Work experience and education history</li>
      <li>Skills and proficiency levels</li>
      <li>Projects and descriptions</li>
      <li>Awards and certifications</li>
      <li>Research publications</li>
      <li>Services offered and interests</li>
    </ul>
    <p style="color:#f87171;font-size:12px">The following are NEVER sent to AI: admin passwords, messages, API keys, IP addresses, or any private data.</p>
  </div>
</div>

<script>
// Auto-fill hint: show which key field is needed
function updateKeyHint() {
  const provider = document.getElementById('providerSelect').value;
  document.getElementById('openaiKey').parentElement.style.opacity = provider === 'openai' ? '1' : '0.4';
  document.getElementById('geminiKey').parentElement.style.opacity = provider === 'gemini' ? '1' : '0.4';
  document.getElementById('deepseekKey').parentElement.style.opacity = provider === 'deepseek' ? '1' : '0.4';
}
document.getElementById('providerSelect')?.addEventListener('change', updateKeyHint);
updateKeyHint();
</script>

<?php include __DIR__ . '/footer.php'; ?>
