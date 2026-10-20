<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Appointment.php";
require_once "../classes/Patient.php";
require_once "../classes/Service.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();

$appointment = new Appointment($conn);
$patientModel = new Patient($conn);
$serviceModel = new Service($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];
$record = $appointment->getAppointment($id);

if (!$record) {
    die("Appointment not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $appointment_date = $_POST["appointment_date"];
    $appointment_time = $_POST["appointment_time"];
    $status = $_POST["status"];
    $patient_id = $_POST["patient_id"];
    $service_id = $_POST["service_id"];

    if ($appointment->updateAppointment($id, $appointment_date, $appointment_time, $status, $patient_id, $service_id)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Updated appointment #$id");
        header("Location: index.php?success=updated");
        exit;
    } else {
        $error = "Failed to update appointment.";
        $record = array_merge($record, $_POST);
    }
}

$patients = $patientModel->getPatients();
$services = $serviceModel->getServices();

$base = "../";
$active = "appointments";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Edit Appointment</h1>
            <p class="page-subtitle">Update appointment #<?= $id ?>.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Appointment Date</label>
                <input type="date" name="appointment_date" value="<?= htmlspecialchars($record['appointment_date']) ?>" required>
            </div>

            <div class="form-group">
                <label>Appointment Time</label>
                <input type="time" name="appointment_time" value="<?= htmlspecialchars($record['appointment_time']) ?>" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <?php foreach (['Pending', 'Confirmed', 'Completed', 'Cancelled'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $record['status'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Patient</label>
                <select name="patient_id" required>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['patient_id'] ?>" <?= $p['patient_id'] == $record['patient_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Service</label>
                <select name="service_id" required>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s['service_id'] ?>" <?= $s['service_id'] == $record['service_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['service_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Update Appointment</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
