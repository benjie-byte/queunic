<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Patient.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$patientModel = new Patient($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];
$record = $patientModel->getPatient($id);

if (!$record) {
    die("Patient not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"]);
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $id_number = trim($_POST["id_number"]);
    $contact_number = trim($_POST["contact_number"]);
    $address = trim($_POST["address"]);

    if ($patientModel->updatePatient($id, $full_name, $age, $gender, $id_number, $contact_number, $address)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Updated patient #$id");
        header("Location: index.php?success=updated");
        exit;
    } else {
        $error = "Failed to update patient.";
        $record = array_merge($record, $_POST);
    }
}

$base = "../";
$active = "patients";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Edit Patient</h1>
            <p class="page-subtitle">Update patient #<?= $id ?>.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($record['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" min="0" max="120" value="<?= htmlspecialchars($record['age']) ?>" required>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <?php foreach (['Male', 'Female', 'Other'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $record['gender'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_number" value="<?= htmlspecialchars($record['id_number']) ?>">
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="tel" name="contact_number" value="<?= htmlspecialchars($record['contact_number']) ?>" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3"><?= htmlspecialchars($record['address']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Update Patient</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
