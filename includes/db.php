<?php
// includes/db.php

$dbDir = __DIR__ . '/../database';
if (!file_exists($dbDir)) {
    mkdir($dbDir, 0755, true);
}

$dbPath = $dbDir . '/app.db';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            total_files INTEGER,
            input_rows INTEGER,
            output_rows INTEGER,
            output_file TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS feedbacks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            rating INTEGER,
            comment TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Seed a default admin user if none exists (username: admin, password: password123)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $defaultUser = 'admin';
        $defaultPass = password_hash('password123', PASSWORD_DEFAULT);
        $seedStmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $seedStmt->execute([$defaultUser, $defaultPass]);
    }

} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}