<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? null;
}

function requireRole($roles = []) {
    if (!isLoggedIn() || !in_array(getUserRole(), $roles)) {
        header('HTTP/1.1 403 Forbidden');
        exit('Access denied.');
    }
}
?>
