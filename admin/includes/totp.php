<?php
/**
 * Pure-PHP TOTP (Time-based One-Time Password) implementation.
 * Compatible with Google Authenticator, Authy, Microsoft Authenticator, etc.
 * No external libraries required — works on any PHP 7+ hosting.
 */

/**
 * Generate a random Base32-encoded secret for TOTP.
 * @return string 16-char Base32 secret
 */
function totpGenerateSecret(): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < 16; $i++) {
        $secret .= $chars[random_int(0, 31)];
    }
    return $secret;
}

/**
 * Base32 decode a string.
 * @param string $encoded
 * @return string binary
 */
function totpBase32Decode(string $encoded): string {
    $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';
    $encoded = strtoupper(trim($encoded));
    $encoded = str_replace([' ', '-'], '', $encoded);
    $decoded = '';
    $buffer = 0;
    $bitsLeft = 0;
    for ($i = 0; $i < strlen($encoded); $i++) {
        $char = $encoded[$i];
        if ($char === '=') break;
        $val = strpos($base32chars, $char);
        if ($val === false) continue;
        $buffer = ($buffer << 5) | $val;
        $bitsLeft += 5;
        if ($bitsLeft >= 8) {
            $bitsLeft -= 8;
            $decoded .= chr(($buffer >> $bitsLeft) & 0xFF);
        }
    }
    return $decoded;
}

/**
 * Base32 encode binary string.
 * @param string $data
 * @return string
 */
function totpBase32Encode(string $data): string {
    $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $encoded = '';
    $buffer = 0;
    $bitsLeft = 0;
    for ($i = 0; $i < strlen($data); $i++) {
        $buffer = ($buffer << 8) | ord($data[$i]);
        $bitsLeft += 8;
        while ($bitsLeft >= 5) {
            $bitsLeft -= 5;
            $encoded .= $base32chars[($buffer >> $bitsLeft) & 0x1F];
        }
    }
    if ($bitsLeft > 0) {
        $buffer <<= (5 - $bitsLeft);
        $encoded .= $base32chars[$buffer & 0x1F];
    }
    return $encoded;
}

/**
 * Generate the current TOTP code for a given secret.
 * @param string $secret  Base32-encoded secret
 * @param int|null $timeSlice  Time slice (defaults to current 30-sec window)
 * @return string 6-digit TOTP code
 */
function totpGenerateCode(string $secret, ?int $timeSlice = null): string {
    if ($timeSlice === null) {
        $timeSlice = (int)(time() / 30);
    }
    $secretBin = totpBase32Decode($secret);
    $timeBin = pack('N*', 0) . pack('N*', $timeSlice);
    $hash = hash_hmac('sha1', $timeBin, $secretBin, true);
    $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
    $code = (
        ((ord($hash[$offset + 0]) & 0x7F) << 24) |
        ((ord($hash[$offset + 1]) & 0xFF) << 16) |
        ((ord($hash[$offset + 2]) & 0xFF) << 8) |
        ((ord($hash[$offset + 3]) & 0xFF))
    );
    $code = ($code % 1000000);
    return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
}

/**
 * Verify a TOTP code against a secret. Allows 1 step tolerance (30 sec before/after).
 * @param string $secret Base32-encoded secret
 * @param string $code 6-digit code entered by user
 * @return bool
 */
function totpVerifyCode(string $secret, string $code): bool {
    $code = trim($code);
    if (!preg_match('/^\d{6}$/', $code)) return false;
    $current = (int)(time() / 30);
    for ($i = -1; $i <= 1; $i++) {
        if (hash_equals(totpGenerateCode($secret, $current + $i), $code)) {
            return true;
        }
    }
    return false;
}

/**
 * Get the otpauth:// URI for QR code generation.
 */
function totpGetOtpAuthUri(string $secret, string $issuer, string $account): string {
    $params = [
        'secret' => $secret,
        'issuer' => $issuer,
        'algorithm' => 'SHA1',
        'digits' => 6,
        'period' => 30,
    ];
    $accountPart = rawurlencode($account);
    $issuerPart = rawurlencode($issuer);
    $query = http_build_query($params);
    return "otpauth://totp/{$issuerPart}:{$accountPart}?{$query}";
}

/**
 * Get a Google Charts QR code image URL for a given otpauth URI.
 */
function totpGetQrCodeUrl(string $otpAuthUri, int $size = 250): string {
    $chl = rawurlencode($otpAuthUri);
    return "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$chl}&choe=UTF-8";
}

/**
 * Save TOTP secret to superadmin.php file.
 * @param string $secret Base32 secret
 * @return bool
 */
function totpSaveSecret(string $secret): bool {
    $file = dirname(__DIR__) . '/../superadmin.php';
    if (!is_writable($file)) return false;
    $content = file_get_contents($file);
    // Remove any existing TOTP_SECRET
    $content = preg_replace(
        "/define\s*\(\s*['\"]TOTP_SECRET['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)\s*;?\n?/",
        "\n",
        $content
    );
    $content = rtrim($content) . "\ndefine('TOTP_SECRET', '" . trim($secret) . "');\n";
    return file_put_contents($file, $content) !== false;
}

/**
 * Get the TOTP secret from superadmin.php.
 * @return string|null
 */
function totpGetSecret(): ?string {
    if (defined('TOTP_SECRET') && TOTP_SECRET) {
        return TOTP_SECRET;
    }
    return null;
}
