<?php
/**
 * Shared sidebar.
 * The including page must set $base and $active before requiring this file.
 *   $base   = "" for root-level pages, "../" for pages inside a module folder
 *   $active = the sidebar link key that should be highlighted (see below)
 */
$base = $base ?? "";
$active = $active ?? "";

function nav_class($key, $active)
{
    return $key === $active ? "active" : "";
}
?>
<div class="sidebar">

    <div class="sidebar-logo">QUEUNIC</div>

    <div class="sidebar-section-title">Main</div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?= $base ?>index.php" class="<?= nav_class('dashboard', $active) ?>">
                <span class="icon">▣</span> Dashboard
            </a>
        </li>
    </ul>

    <div class="sidebar-section-title">Patient Care</div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?= $base ?>patients/index.php" class="<?= nav_class('patients', $active) ?>">
                <span class="icon">♙</span> Patients
            </a>
        </li>
        <li>
            <a href="<?= $base ?>appointments/index.php" class="<?= nav_class('appointments', $active) ?>">
                <span class="icon">▣</span> Appointments
            </a>
        </li>
        <li>
            <a href="<?= $base ?>queue/index.php" class="<?= nav_class('queue', $active) ?>">
                <span class="icon">☷</span> Queue
            </a>
        </li>
        <li>
            <a href="<?= $base ?>consultations/index.php" class="<?= nav_class('consultations', $active) ?>">
                <span class="icon">✚</span> Consultations
            </a>
        </li>
        <li>
            <a href="<?= $base ?>medical_records/index.php" class="<?= nav_class('medical_records', $active) ?>">
                <span class="icon">▤</span> Medical Records
            </a>
        </li>
    </ul>

    <div class="sidebar-section-title">Clinic</div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?= $base ?>services/index.php" class="<?= nav_class('services', $active) ?>">
                <span class="icon">⚕</span> Services
            </a>
        </li>
        <li>
            <a href="<?= $base ?>notifications/index.php" class="<?= nav_class('notifications', $active) ?>">
                <span class="icon">●</span> Notifications
            </a>
        </li>
    </ul>

    <div class="sidebar-section-title">System</div>
    <ul class="sidebar-menu">
        <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
        <li>
            <a href="<?= $base ?>activity_logs/index.php" class="<?= nav_class('activity_logs', $active) ?>">
                <span class="icon">◷</span> Activity Logs
            </a>
        </li>
        <li>
            <a href="<?= $base ?>users/index.php" class="<?= nav_class('users', $active) ?>">
                <span class="icon">♙</span> Users
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="<?= $base ?>logout.php">
                <span class="icon">↪</span> Logout
            </a>
        </li>
    </ul>

</div>
