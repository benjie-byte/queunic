<?php

require_once "../includes/auth_check.php";
require_role(['Admin']);

require_once "../config/database.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$logModel = new ActivityLog($conn);
$logs = $logModel->getLogs();

$base = "../";
$active = "activity_logs";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Activity Logs</h1>
            <p class="page-subtitle">Recent system activity (last 200 entries).</p>
        </div>
    </div>

    <div class="system-card">
        <div class="table-container">
            <table class="system-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["log_id"]) ?></td>
                            <td><?= htmlspecialchars($row["user_name"]) ?></td>
                            <td><?= htmlspecialchars($row["action"]) ?></td>
                            <td><?= htmlspecialchars($row["timestamp"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="no-records">No activity recorded yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
