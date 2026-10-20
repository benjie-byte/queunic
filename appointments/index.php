<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Appointment.php";

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);

if (isset($_GET["search"]) && trim($_GET["search"]) !== "") {
    $keyword = trim($_GET["search"]);
    $appointments = $appointment->searchAppointments($keyword);
} else {
    $appointments = $appointment->getAppointments();
}

$base = "../";
$active = "appointments";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Appointments</h1>
            <p class="page-subtitle">Manage and monitor patient appointments.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New Appointment</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'Appointment added.', 'updated' => 'Appointment updated.', 'deleted' => 'Appointment deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not delete the appointment.'];
            echo htmlspecialchars($errs[$_GET['error']] ?? 'Something went wrong.');
            ?>
        </div>
    <?php endif; ?>

    <div class="system-card">

        <div class="search-area">
            <form method="GET" class="search-form">
                <input
                    type="text"
                    name="search"
                    placeholder="Search appointments..."
                    value="<?= isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : "" ?>"
                >
                <button type="submit" class="search-button">Search</button>
            </form>

            <?php if (isset($_GET["search"])): ?>
                <a href="index.php" class="btn-cancel">Clear</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table class="system-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["appointment_id"]) ?></td>
                            <td><?= htmlspecialchars($row["patient_name"]) ?></td>
                            <td><?= htmlspecialchars($row["service_name"]) ?></td>
                            <td><?= htmlspecialchars($row["appointment_date"]) ?></td>
                            <td><?= htmlspecialchars($row["appointment_time"]) ?></td>
                            <td>
                                <?php $statusClass = "status-" . strtolower($row["status"]); ?>
                                <span class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($row["status"]) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row["appointment_id"] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row["appointment_id"] ?>" class="btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-records">No appointments found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>
