<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Consultation.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$consultationModel = new Consultation($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];
$record = $consultationModel->getConsultation($id);

if (!$record) {
    die("Consultation not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $diagnosis = trim($_POST["diagnosis"]);
    $prescription = trim($_POST["prescription"]);
    $notes = trim($_POST["notes"]);
    $date = $_POST["date"];

    if ($consultationModel->updateConsultation($id, $diagnosis, $prescription, $notes, $date)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Updated consultation #$id");
        header("Location: index.php?success=updated");
        exit;
    } else {
        $error = "Failed to update consultation.";
        $record = array_merge($record, $_POST);
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
    <title>Edit Consultation | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Edit Consultation</h1>
            <p class="page-subtitle">Update consultation #<?= $id ?>.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?= htmlspecialchars($record['date']) ?>" required>
            </div>

            <div class="form-group">
                <label>Diagnosis</label>
                <textarea name="diagnosis" rows="3" required><?= htmlspecialchars($record['diagnosis']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Prescription</label>
                <textarea name="prescription" rows="3"><?= htmlspecialchars($record['prescription']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3"><?= htmlspecialchars($record['notes']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Update Consultation</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
