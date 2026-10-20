<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Notification.php";

$database = new Database();
$conn = $database->connect();
$notificationModel = new Notification($conn);
$notifications = $notificationModel->getNotifications();

$base = "../";
$active = "notifications";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Messages sent to patients.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New Notification</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'Notification sent.', 'read' => 'Marked as read.', 'deleted' => 'Notification deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['delete_failed' => 'Could not delete the notification.', 'update_failed' => 'Could not update the notification.'];
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
                        <th>Message</th>
                        <th>Date Sent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["notification_id"]) ?></td>
                            <td><?= htmlspecialchars($row["patient_name"]) ?></td>
                            <td><?= htmlspecialchars($row["message"]) ?></td>
                            <td><?= htmlspecialchars($row["date_sent"]) ?></td>
                            <td>
                                <?php $statusClass = "status-" . strtolower($row["status"]); ?>
                                <span class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($row["status"]) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($row['status'] === 'Unread'): ?>
                                        <a href="mark_read.php?id=<?= $row['notification_id'] ?>" class="btn-edit">Mark Read</a>
                                    <?php endif; ?>
                                    <a href="delete.php?id=<?= $row['notification_id'] ?>" class="btn-delete"
                                       onclick="return confirm('Delete this notification?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="no-records">No notifications found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
