<?php
// includes/security_tokens.php
require_once __DIR__ . '/db.php';

function generate_secure_download_token($filename, $userId = null) {
    global $pdo;
    $token = bin2hex(random_bytes(32));
    // Token expires in 24 hours
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Ensure download_tokens table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS download_tokens (
        token TEXT PRIMARY KEY,
        filename TEXT NOT NULL,
        user_id INTEGER,
        expires_at DATETIME NOT NULL
    )");

    $stmt = $pdo->prepare("INSERT INTO download_tokens (token, filename, user_id, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$token, $filename, $userId, $expiresAt]);

    return $token;
}

function get_filename_from_token($token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT filename, expires_at FROM download_tokens WHERE token = ?");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;

    // Check if token has expired
    if (strtotime($row['expires_at']) < time()) {
        return null; // Expired
    }

    return $row['filename'];
}