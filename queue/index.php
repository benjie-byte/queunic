<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Queue.php";

$database = new Database();
$conn = $database->connect();
$queueModel = new QueueManager($conn);
$queueEntries = $queueModel->getQueue();

$base = "../";
$active = "queue";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Queue</h1>
            <p class="page-subtitle">Today's walk-in and appointment queue.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ Add to Queue</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">Queue updated.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not remove the queue entry.', 'update_failed' => 'Could not update the queue entry.'];
            echo htmlspecialchars($errs[$_GET['error']] ?? 'Something went wrong.');
            ?>
        </div>
    <?php endif; ?>

    <div class="system-card">
        <div class="table-container">
            <table class="system-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Time In</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($queueEntries)): ?>
                    <?php foreach ($queueEntries as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["queue_number"]) ?></td>
                            <td><?= htmlspecialchars($row["patient_name"]) ?></td>
                            <td><?= htmlspecialchars($row["service_name"]) ?></td>
                            <td><?= htmlspecialchars($row["time_in"]) ?></td>
                            <td>
                                <?php $statusClass = "status-" . strtolower($row["status"]); ?>
                                <span class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($row["status"]) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($row['status'] === 'Waiting'): ?>
                                        <a href="update_status.php?id=<?= $row['queue_id'] ?>&status=Called" class="btn-edit">Call</a>
                                    <?php elseif ($row['status'] === 'Called'): ?>
                                        <a href="update_status.php?id=<?= $row['queue_id'] ?>&status=Done" class="btn-edit">Complete</a>
                                    <?php endif; ?>
                                    <a href="delete.php?id=<?= $row['queue_id'] ?>" class="btn-delete"
                                       onclick="return confirm('Remove this entry from the queue?');">Remove</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="no-records">Queue is empty.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
