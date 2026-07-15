<?php
/**
 * AI Chat Endpoint
 *
 * Receives a question, builds a context from PUBLIC portfolio data,
 * and sends it to the configured AI provider (ChatGPT/Gemini/DeepSeek).
 *
 * SECURITY: Only public portfolio data is sent to AI.
 * Admin credentials, messages, passwords are NEVER sent.
 */

require_once __DIR__ . '/db.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

session_start();

// ── Rate limiting (simple) ────────────────────────────────────────────────────
$rateKey = 'ai_chat_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$now = time();
if (!isset($_SESSION[$rateKey])) $_SESSION[$rateKey] = ['count' => 0, 'reset' => $now + 60];
if ($now > $_SESSION[$rateKey]['reset']) {
    $_SESSION[$rateKey] = ['count' => 0, 'reset' => $now + 60];
}
if ($_SESSION[$rateKey]['count'] > 10) {
    http_response_code(429);
    die(json_encode(['error' => 'Too many requests. Please wait a minute.']));
}
$_SESSION[$rateKey]['count']++;

// ── Get settings ─────────────────────────────────────────────────────────────
try {
    $pdo = getDB();

    // Ensure table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS ai_settings (
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

    $stmt = $pdo->query("SELECT * FROM ai_settings LIMIT 1");
    $settings = $stmt->fetch() ?: [];

    // If no row, insert one
    if (!$settings) {
        $pdo->exec("INSERT INTO ai_settings (id) VALUES (1)");
        $stmt = $pdo->query("SELECT * FROM ai_settings LIMIT 1");
        $settings = $stmt->fetch() ?: [];
    }
} catch (Exception $e) {
    error_log('[AI Chat] DB error: ' . $e->getMessage());
    die(json_encode(['error' => 'Database unavailable.']));
}

// Check if AI is enabled
if (empty($settings['enabled'])) {
    die(json_encode(['error' => 'AI chat is currently disabled.']));
}

// ── Validate input ────────────────────────────────────────────────────────────
$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? '');
if (strlen($question) < 2 || strlen($question) > 500) {
    die(json_encode(['error' => 'Please ask a question between 2-500 characters.']));
}

// Block prompt injection attempts
if (preg_match('/(system|prompt injection|ignore previous|disregard)/i', $question)) {
    die(json_encode(['error' => 'Invalid question.']));
}

// ── Build PUBLIC portfolio context (SECURE — no admin data) ───────────────────
$contextParts = [];

try {
    // Profile — public info only (no admin credentials)
    $profile = dbRow("SELECT full_name, title, bio, email, phone, location, company, company_url, role FROM profile LIMIT 1");
    if ($profile) {
        $contextParts[] = "PORTFOLIO OWNER:\nName: {$profile['full_name']}\nTitle: {$profile['title']}\nBio: {$profile['bio']}\nRole: {$profile['role']}\nCompany: {$profile['company']}\nLocation: {$profile['location']}\nContact: {$profile['email']} | {$profile['phone']}";
    }

    // Experience
    $exp = dbRows("SELECT company, role, period, description FROM experience ORDER BY sort_order, id");
    if ($exp) {
        $expList = [];
        foreach ($exp as $e) {
            $expList[] = "- {$e['role']} at {$e['company']} ({$e['period']}): {$e['description']}";
        }
        $contextParts[] = "WORK EXPERIENCE:\n" . implode("\n", $expList);
    }

    // Education
    $edu = dbRows("SELECT degree_code, degree_name, institution, period FROM education ORDER BY sort_order, id");
    if ($edu) {
        $eduList = [];
        foreach ($edu as $e) {
            $code = $e['degree_code'] ? "{$e['degree_code']} — " : '';
            $eduList[] = "- {$code}{$e['degree_name']} from {$e['institution']} ({$e['period']})";
        }
        $contextParts[] = "EDUCATION:\n" . implode("\n", $eduList);
    }

    // Skills
    $skills = dbRows("SELECT name, level, category FROM skills ORDER BY sort_order, id");
    if ($skills) {
        $byCat = [];
        foreach ($skills as $s) { $byCat[$s['category']][] = "{$s['name']} ({$s['level']}%)"; }
        $skillParts = [];
        foreach ($byCat as $cat => $items) { $skillParts[] = "$cat: " . implode(', ', $items); }
        $contextParts[] = "SKILLS:\n" . implode("\n", $skillParts);
    }

    // Projects
    $projects = dbRows("SELECT title, subtitle, description, url, tags FROM projects ORDER BY sort_order, id");
    if ($projects) {
        $projList = [];
        foreach ($projects as $p) {
            $tags = $p['tags'] ? " [Tags: {$p['tags']}]" : "";
            $link = $p['url'] ? " — {$p['url']}" : "";
            $projList[] = "- {$p['title']}" . ($p['subtitle'] ? " ({$p['subtitle']})" : "") . ": {$p['description']}{$tags}{$link}";
        }
        $contextParts[] = "PROJECTS:\n" . implode("\n", $projList);
    }

    // Awards
    $awards = dbRows("SELECT title, organization, year, description FROM awards ORDER BY sort_order, id");
    if ($awards) {
        $awardList = [];
        foreach ($awards as $a) {
            $desc = $a['description'] ? " — {$a['description']}" : "";
            $awardList[] = "- {$a['title']} by {$a['organization']} ({$a['year']}){$desc}";
        }
        $contextParts[] = "AWARDS & RECOGNITION:\n" . implode("\n", $awardList);
    }

    // Training
    $training = dbRows("SELECT name, organizer, year, certificate_url FROM training ORDER BY sort_order, id");
    if ($training) {
        $trainList = [];
        foreach ($training as $t) {
            $org = $t['organizer'] ? " by {$t['organizer']}" : "";
            $trainList[] = "- {$t['name']}{$org} ({$t['year']})";
        }
        $contextParts[] = "TRAINING & CERTIFICATIONS:\n" . implode("\n", $trainList);
    }

    // Research
    $research = dbRows("SELECT title, journal, year, description FROM research ORDER BY sort_order, id");
    if ($research) {
        $resList = [];
        foreach ($research as $r) { $resList[] = "- \"{$r['title']}\" in {$r['journal']} ({$r['year']})"; }
        $contextParts[] = "RESEARCH PUBLICATIONS:\n" . implode("\n", $resList);
    }

    // Services (includes Digital Services with pricing)
    try {
        $services = dbRows("SELECT name, description, is_pricing, price, price_unit, features FROM services_about ORDER BY sort_order, id");
        if ($services) {
            $servList = [];
            foreach ($services as $s) {
                $pricing = (!empty($s['is_pricing']) && !empty($s['price'])) ? " — {$s['price']} {$s['price_unit']}" : "";
                $servList[] = "- {$s['name']}{$pricing}: {$s['description']}";
            }
            $contextParts[] = "SERVICES OFFERED:\n" . implode("\n", $servList);
        }
    } catch (Exception $e) {
        // Fallback: query just name/description if new columns don't exist yet
        try {
            $services = dbRows("SELECT name, description FROM services_about ORDER BY sort_order, id");
            if ($services) {
                $servList = [];
                foreach ($services as $s) { $servList[] = "- {$s['name']}: {$s['description']}"; }
                $contextParts[] = "SERVICES OFFERED:\n" . implode("\n", $servList);
            }
        } catch (Exception $e2) { /* table doesn't exist */ }
    }

    // Interests
    $interests = dbRows("SELECT name FROM interests ORDER BY sort_order, id");
    if ($interests) {
        $intList = array_map(fn($i) => $i['name'], $interests);
        $contextParts[] = "INTERESTS: " . implode(', ', $intList);
    }

} catch (Exception $e) {
    error_log('[AI Chat] Context build error: ' . $e->getMessage());
}

// ── Call AI Provider ─────────────────────────────────────────────────────────
$provider = $settings['provider'] ?? 'openai';
$answer = '';

if ($provider === 'gemini') {
    $apiKey = $settings['gemini_key'] ?? '';
    if ($apiKey) $answer = callGemini($apiKey, $settings['system_prompt'] ?? '', $contextParts, $question);
} elseif ($provider === 'deepseek') {
    $apiKey = $settings['deepseek_key'] ?? '';
    if ($apiKey) $answer = callDeepSeek($apiKey, $settings['system_prompt'] ?? '', $contextParts, $question);
} else {
    $apiKey = $settings['openai_key'] ?? '';
    if ($apiKey) $answer = callOpenAI($apiKey, $settings['system_prompt'] ?? '', $contextParts, $question);
}

// ── Return answer ─────────────────────────────────────────────────────────────
if ($answer) {
    $answer = preg_replace('/(password|secret|key|token)\s*[:=]\s*\S+/i', '[REDACTED]', $answer);
    die(json_encode(['answer' => trim($answer)]));
} else {
    die(json_encode(['error' => 'AI could not generate a response. Please try again.']));
}

// ── OpenAI / ChatGPT API ─────────────────────────────────────────────────────
function callOpenAI(string $apiKey, string $customPrompt, array $contextParts, string $question): string {
    $systemDefault = "You are a helpful AI assistant for a portfolio website. Answer questions based ONLY on the provided portfolio data. Be friendly, concise, and helpful. If you don't know something, say so. Do NOT make up information. Do NOT reveal any admin credentials, passwords, or internal data.";
    $system = $customPrompt ?: $systemDefault;
    $context = implode("\n\n", $contextParts);
    $fullSystem = $system . "\n\n[PORTFOLIO DATA]\n" . $context;

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $fullSystem],
                ['role' => 'user', 'content' => $question]
            ],
            'max_tokens' => 500,
            'temperature' => 0.7
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$res) return '';
    $data = json_decode($res, true);
    return $data['choices'][0]['message']['content'] ?? '';
}

// ── Google Gemini API ──────────────────────────────────────────────────────────
function callGemini(string $apiKey, string $customPrompt, array $contextParts, string $question): string {
    $systemDefault = "You are a helpful AI assistant for a portfolio website. Answer based ONLY on the portfolio data provided. Be friendly and concise.";
    $system = $customPrompt ?: $systemDefault;
    $context = implode("\n\n", $contextParts);
    $prompt = "$system\n\n[PORTFOLIO DATA]\n$context\n\nUser: $question";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['maxOutputTokens' => 500, 'temperature' => 0.7]
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$res) return '';
    $data = json_decode($res, true);
    return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
}

// ── DeepSeek API ─────────────────────────────────────────────────────────────
function callDeepSeek(string $apiKey, string $customPrompt, array $contextParts, string $question): string {
    $systemDefault = "You are a helpful AI assistant for a portfolio website. Answer questions based ONLY on the provided portfolio data. Be friendly, concise, and helpful.";
    $system = $customPrompt ?: $systemDefault;
    $context = implode("\n\n", $contextParts);
    $fullSystem = $system . "\n\n[PORTFOLIO DATA]\n" . $context;

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => $fullSystem],
                ['role' => 'user', 'content' => $question]
            ],
            'max_tokens' => 500,
            'temperature' => 0.7
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$res) return '';
    $data = json_decode($res, true);
    return $data['choices'][0]['message']['content'] ?? '';
}

