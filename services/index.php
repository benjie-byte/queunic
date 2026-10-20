<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Service.php";

$database = new Database();
$conn = $database->connect();
$serviceModel = new Service($conn);
$services = $serviceModel->getServices();

$base = "../";
$active = "services";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Services</h1>
            <p class="page-subtitle">Manage the clinic's available services.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New Service</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'Service added.', 'updated' => 'Service updated.', 'deleted' => 'Service deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not delete the service.'];
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
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Avg. Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["service_id"]) ?></td>
                            <td><?= htmlspecialchars($row["service_name"]) ?></td>
                            <td><?= htmlspecialchars($row["description"]) ?></td>
                            <td><?= htmlspecialchars($row["average_duration"]) ?> min</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row["service_id"] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row["service_id"] ?>" class="btn-delete"
                                       onclick="return confirm('Delete this service?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="no-records">No services found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
