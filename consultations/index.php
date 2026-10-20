<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Consultation.php";

$database = new Database();
$conn = $database->connect();
$consultationModel = new Consultation($conn);
$consultations = $consultationModel->getConsultations();

$base = "../";
$active = "consultations";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultations | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Consultations</h1>
            <p class="page-subtitle">Diagnosis and prescription records.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New Consultation</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'Consultation recorded.', 'updated' => 'Consultation updated.', 'deleted' => 'Consultation deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not delete the consultation.'];
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
                        <th>Diagnosis</th>
                        <th>Date</th>
                        <th>Handled By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($consultations)): ?>
                    <?php foreach ($consultations as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["consultation_id"]) ?></td>
                            <td><?= htmlspecialchars($row["patient_name"]) ?></td>
                            <td><?= htmlspecialchars($row["diagnosis"]) ?></td>
                            <td><?= htmlspecialchars($row["date"]) ?></td>
                            <td><?= htmlspecialchars($row["staff_name"]) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row["consultation_id"] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row["consultation_id"] ?>" class="btn-delete"
                                       onclick="return confirm('Delete this consultation record?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="no-records">No consultations recorded.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
