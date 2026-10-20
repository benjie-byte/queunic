<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Consultation.php";
require_once "../classes/Queue.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();

$consultationModel = new Consultation($conn);
$queueModel = new QueueManager($conn);
$calledQueue = $queueModel->getCalledQueue();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $diagnosis = trim($_POST["diagnosis"] ?? "");
    $prescription = trim($_POST["prescription"] ?? "");
    $notes = trim($_POST["notes"] ?? "");
    $date = $_POST["date"] ?? "";
    $queue_id = (int) ($_POST["queue_id"] ?? 0);
    $user_id = $_SESSION['user_id'] ?? null;

    if ($diagnosis === "" || $date === "" || $queue_id <= 0 || $user_id === null) {
        $error = "Please complete all required fields.";
    } elseif ($consultationModel->addConsultation($diagnosis, $prescription, $notes, $date, $user_id, $queue_id)) {
        if (!$queueModel->updateStatus($queue_id, 'Done')) {
            error_log("Consultation saved but queue #$queue_id could not be marked Done.");
        }
        (new ActivityLog($conn))->log($user_id, "Recorded a consultation for queue #$queue_id");
        header("Location: index.php?success=added");
        exit;
    } else {
        $error = "Unable to save consultation.";
    }
}

$base = "../";
$active = "consultations";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Consultation | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New Consultation</h1>
            <p class="page-subtitle">Record a diagnosis for a called patient.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Patient (currently called)</label>
                <select name="queue_id" required>
                    <option value="" selected disabled>Select a patient</option>
                    <?php foreach ($calledQueue as $q): ?>
                        <option value="<?= $q['queue_id'] ?>">
                            <?= htmlspecialchars($q['patient_name']) ?> — <?= htmlspecialchars($q['service_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($calledQueue)): ?>
                    <small style="color:#b42318;">No patients are currently called. Call a patient from the <a href="../queue/index.php">Queue</a> first.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Diagnosis</label>
                <textarea name="diagnosis" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Prescription</label>
                <textarea name="prescription" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Save Consultation</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
