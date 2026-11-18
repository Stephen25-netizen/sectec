<?php

require_once __DIR__ . "/database.php";

// Start PHP session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Secure output
function clean($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Generate CSRF token
function csrfToken() {

    if (empty($_SESSION['csrf_token'])) {

        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );

    }

    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCsrf($token) {

    return isset($_SESSION['csrf_token']) &&
        is_string($token) &&
        hash_equals($_SESSION['csrf_token'], $token);

}

// Protect administrator pages
function requireAdmin() {

    if (
        empty($_SESSION['admin_id']) ||
        ($_SESSION['admin_role'] ?? '') !== 'admin'
    ) {

        header("Location: login.php");
        exit;

    }

}