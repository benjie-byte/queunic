<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Notification.php";
require_once "../classes/Patient.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();

$notificationModel = new Notification($conn);
$patientModel = new Patient($conn);
$patients = $patientModel->getPatients();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $message = trim($_POST["message"]);
    $patient_id = $_POST["patient_id"];

    if ($notificationModel->addNotification($message, $patient_id)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Sent a notification");
        header("Location: index.php?success=added");
        exit;
    } else {
        $error = "Unable to send notification.";
    }
}

$base = "../";
$active = "notifications";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Notification | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New Notification</h1>
            <p class="page-subtitle">Send a message to a patient.</p>
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
                <label>Message</label>
                <textarea name="message" rows="4" required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Send Notification</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
