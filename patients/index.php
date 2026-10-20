<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Patient.php";

$database = new Database();
$conn = $database->connect();
$patientModel = new Patient($conn);

if (isset($_GET["search"]) && trim($_GET["search"]) !== "") {
    $patients = $patientModel->searchPatients(trim($_GET["search"]));
} else {
    $patients = $patientModel->getPatients();
}

$base = "../";
$active = "patients";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Patients</h1>
            <p class="page-subtitle">Manage patient records.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New Patient</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'Patient added.', 'updated' => 'Patient updated.', 'deleted' => 'Patient deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not delete the patient.'];
            echo htmlspecialchars($errs[$_GET['error']] ?? 'Something went wrong.');
            ?>
        </div>
    <?php endif; ?>

    <div class="system-card">

        <div class="search-area">
            <form method="GET" class="search-form">
                <input
                    type="text"
                    name="search"
                    placeholder="Search patients..."
                    value="<?= isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : "" ?>"
                >
                <button type="submit" class="search-button">Search</button>
            </form>
            <?php if (isset($_GET["search"])): ?>
                <a href="index.php" class="btn-cancel">Clear</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table class="system-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>ID Number</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["patient_id"]) ?></td>
                            <td><?= htmlspecialchars($row["full_name"]) ?></td>
                            <td><?= htmlspecialchars($row["age"]) ?></td>
                            <td><?= htmlspecialchars($row["gender"]) ?></td>
                            <td><?= htmlspecialchars($row["id_number"]) ?></td>
                            <td><?= htmlspecialchars($row["contact_number"]) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row["patient_id"] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row["patient_id"] ?>" class="btn-delete"
                                       onclick="return confirm('Delete this patient? This also removes their appointments, queue entries, and records.');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-records">No patients found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>
