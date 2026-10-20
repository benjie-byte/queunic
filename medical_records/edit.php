<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/MedicalRecord.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$recordModel = new MedicalRecord($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];
$record = $recordModel->getRecord($id);

if (!$record) {
    die("Medical record not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $medical_history = trim($_POST["medical_history"]);
    $allergies = trim($_POST["allergies"]);
    $notes = trim($_POST["notes"]);

    if ($recordModel->updateRecord($id, $medical_history, $allergies, $notes)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Updated medical record #$id");
        header("Location: index.php?success=updated");
        exit;
    } else {
        $error = "Failed to update medical record.";
        $record = array_merge($record, $_POST);
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
    <title>Edit Medical Record | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Edit Medical Record</h1>
            <p class="page-subtitle">Update record #<?= $id ?>.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Medical History</label>
                <textarea name="medical_history" rows="3"><?= htmlspecialchars($record['medical_history']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Allergies</label>
                <textarea name="allergies" rows="2"><?= htmlspecialchars($record['allergies']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3"><?= htmlspecialchars($record['notes']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Update Record</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
