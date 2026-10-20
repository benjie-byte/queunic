<?php
/**
 * Include this at the very top of every protected page
 * (before any HTML output) to require a logged-in session.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Build a login URL that works whether the app lives at the web
    // root or in a subfolder (e.g. /queunic_system/ on XAMPP).
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // $scriptDir is like "/queunic_system/patients" -> app root is its parent for module pages.
    $appRoot = preg_replace('#/(patients|appointments|queue|consultations|medical_records|services|notifications|activity_logs|users)$#', '', $scriptDir);
    $appRoot = rtrim($appRoot, '/');
    header("Location: " . $appRoot . "/login.php");
    exit;
}

/**
 * Restrict a page to specific roles.
 * Usage: require_role(['Admin']);
 */
function require_role(array $roles)
{
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        die("Access denied: you don't have permission to view this page.");
    }
}
