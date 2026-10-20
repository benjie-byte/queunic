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

$patients = $patientModel->getPatients();
$services = $serviceModel->getServices();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $appointment_date = $_POST["appointment_date"] ?? "";
    $appointment_time = $_POST["appointment_time"] ?? "";
    $status = $_POST["status"] ?? "";
    $patient_id = (int) ($_POST["patient_id"] ?? 0);
    $service_id = (int) ($_POST["service_id"] ?? 0);

    $allowedStatuses = ["Pending", "Confirmed", "Completed", "Cancelled"];

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointment_date)) {
        $error = "Please enter a valid appointment date.";
    } elseif (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $appointment_time)) {
        $error = "Please enter a valid appointment time.";
    } elseif (!in_array($status, $allowedStatuses, true)) {
        $error = "Please select a valid status.";
    } elseif ($patient_id <= 0 || $service_id <= 0) {
        $error = "Please select a valid patient and service.";
    } elseif ($appointment->addAppointment($appointment_date, $appointment_time, $status, $patient_id, $service_id)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Added a new appointment");
        header("Location: index.php?success=added");
        exit;
    } else {
        $error = "Unable to add appointment.";
    }
}

$base = "../";
$active = "appointments";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Appointment | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New Appointment</h1>
            <p class="page-subtitle">Create a new patient appointment.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Appointment Date</label>
                <input type="date" name="appointment_date" required>
            </div>

            <div class="form-group">
                <label>Appointment Time</label>
                <input type="time" name="appointment_time" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Patient</label>
                <select name="patient_id" required>
                    <option value="" selected disabled>Select a patient</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['patient_id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($patients)): ?>
                    <small style="color:#b42318;">No patients yet — <a href="../patients/add.php">add one first</a>.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Service</label>
                <select name="service_id" required>
                    <option value="" selected disabled>Select a service</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s['service_id'] ?>"><?= htmlspecialchars($s['service_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($services)): ?>
                    <small style="color:#b42318;">No services yet — <a href="../services/add.php">add one first</a>.</small>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Save Appointment</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
