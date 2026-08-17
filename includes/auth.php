<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}