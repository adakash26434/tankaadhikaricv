<?php
require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            // Log internally, never expose DB credentials/structure to the browser
            error_log('[Portfolio DB Error] ' . $e->getMessage());
            http_response_code(503);
            die('<div style="font-family:monospace;padding:40px;color:#f87171;background:#0f1420;min-height:100vh">
                <h2 style="color:#fff">Database Connection Failed</h2>
                <p>Please update your database credentials in <strong>config.php</strong> and ensure the database is running.</p>
                <p style="color:#64748b;font-size:12px">Check cPanel &rarr; MySQL Databases and verify DB_HOST, DB_NAME, DB_USER, DB_PASS.</p>
                </div>');
        }
    }
    return $pdo;
}

function dbRow(string $sql, array $params = []): ?array {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function dbRows(string $sql, array $params = []): array {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function dbExec(string $sql, array $params = []): int {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return (int) getDB()->lastInsertId();
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
