<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/MedicalRecord.php";

$database = new Database();
$conn = $database->connect();
$recordModel = new MedicalRecord($conn);
$records = $recordModel->getRecords();

$base = "../";
$active = "medical_records";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Medical Records</h1>
            <p class="page-subtitle">Patient history and allergy records.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New Record</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'Record added.', 'updated' => 'Record updated.', 'deleted' => 'Record deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not delete the record.'];
            echo htmlspecialchars($errs[$_GET['error']] ?? 'Something went wrong.');
            ?>
        </div>
    <?php endif; ?>

    <div class="system-card">
        <div class="table-container">
            <table class="system-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Allergies</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["record_id"]) ?></td>
                            <td><?= htmlspecialchars($row["patient_name"]) ?></td>
                            <td><?= htmlspecialchars($row["allergies"]) ?></td>
                            <td><?= htmlspecialchars($row["date_updated"]) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row["record_id"] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row["record_id"] ?>" class="btn-delete"
                                       onclick="return confirm('Delete this medical record?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="no-records">No medical records found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
