<?php
require_once "includes/auth_check.php";
require_once "config/database.php";

$database = new Database();
$conn = $database->connect();

// live counts for the dashboard cards
$patientCount = (int) $conn->query("SELECT COUNT(*) c FROM patients")->fetch()['c'];
$todayAppointments = (int) $conn->query(
    "SELECT COUNT(*) c FROM appointments WHERE appointment_date = CURDATE()"
)->fetch()['c'];
$waitingInQueue = (int) $conn->query(
    "SELECT COUNT(*) c FROM queue WHERE status = 'Waiting'"
)->fetch()['c'];
$unreadNotifications = (int) $conn->query(
    "SELECT COUNT(*) c FROM notifications WHERE status = 'Unread'"
)->fetch()['c'];

$base = "";
$active = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Queunic</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="appointment-page">

<?php require_once "includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
            <p class="page-subtitle">Here's what's happening in your clinic today.</p>
        </div>
    </div>

    <div class="dashboard-grid">

        <div class="system-card stat-card">
            <div class="stat-number"><?= $patientCount ?></div>
            <div class="stat-label">Total Patients</div>
        </div>

        <div class="system-card stat-card">
            <div class="stat-number"><?= $todayAppointments ?></div>
            <div class="stat-label">Today's Appointments</div>
        </div>

        <div class="system-card stat-card">
            <div class="stat-number"><?= $waitingInQueue ?></div>
            <div class="stat-label">Waiting in Queue</div>
        </div>

        <div class="system-card stat-card">
            <div class="stat-number"><?= $unreadNotifications ?></div>
            <div class="stat-label">Unread Notifications</div>
        </div>

    </div>

    <div class="system-card" style="margin-top:20px;">
        <h2 class="section-title">Quick Actions</h2>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="patients/add.php" class="btn-primary-custom">+ New Patient</a>
            <a href="appointments/add.php" class="btn-primary-custom">+ New Appointment</a>
            <a href="queue/add.php" class="btn-primary-custom">+ Add to Queue</a>
        </div>
    </div>

</div>

</body>
</html>
