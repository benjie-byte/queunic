<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Notification.php";

$database = new Database();
$conn = $database->connect();
$notificationModel = new Notification($conn);

$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

if ($notificationModel->markAsRead($id)) {
    header("Location: index.php?success=read");
} else {
    header("Location: index.php?error=update_failed");
}
exit;
