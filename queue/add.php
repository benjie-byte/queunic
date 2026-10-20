<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Queue.php";
require_once "../classes/Patient.php";
require_once "../classes/Service.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();

$queueModel = new QueueManager($conn);
$patientModel = new Patient($conn);
$serviceModel = new Service($conn);

$patients = $patientModel->getPatients();
$services = $serviceModel->getServices();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = (int) ($_POST["patient_id"] ?? 0);
    $service_id = (int) ($_POST["service_id"] ?? 0);

    if ($patient_id <= 0 || $service_id <= 0) {
        $error = "Please select a valid patient and service.";
    } elseif ($queueModel->addToQueue($patient_id, $service_id)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Added a patient to the queue");
        header("Location: index.php?success=added");
        exit;
    } else {
        $error = "Unable to add to queue.";
    }
}

$base = "../";
$active = "queue";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Queue | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Add to Queue</h1>
            <p class="page-subtitle">Check in a patient.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Patient</label>
                <select name="patient_id" required>
                    <option value="" selected disabled>Select a patient</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['patient_id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Service</label>
                <select name="service_id" required>
                    <option value="" selected disabled>Select a service</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s['service_id'] ?>"><?= htmlspecialchars($s['service_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Add to Queue</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
