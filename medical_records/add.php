<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/MedicalRecord.php";
require_once "../classes/Patient.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();

$recordModel = new MedicalRecord($conn);
$patientModel = new Patient($conn);
$patients = $patientModel->getPatients();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $medical_history = trim($_POST["medical_history"]);
    $allergies = trim($_POST["allergies"]);
    $notes = trim($_POST["notes"]);
    $patient_id = $_POST["patient_id"];

    if ($recordModel->addRecord($medical_history, $allergies, $notes, $patient_id)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Added a medical record");
        header("Location: index.php?success=added");
        exit;
    } else {
        $error = "Unable to add medical record.";
    }
}

$base = "../";
$active = "medical_records";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medical Record | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New Medical Record</h1>
            <p class="page-subtitle">Add a patient's medical history.</p>
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
                <label>Medical History</label>
                <textarea name="medical_history" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Allergies</label>
                <textarea name="allergies" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Save Record</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
