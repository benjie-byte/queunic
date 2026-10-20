<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Queue.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$queueModel = new QueueManager($conn);

$id = (int) ($_GET["id"] ?? 0);
$status = $_GET["status"] ?? "";

$allowed = ["Waiting", "Called", "Done"];

// Enforce the forward-only flow: Waiting -> Called -> Done.
$validTransitions = [
    "Waiting" => ["Called"],
    "Called"  => ["Done"],
];

if ($id <= 0 || !in_array($status, $allowed, true)) {
    header("Location: index.php?error=update_failed");
    exit;
}

$current = $queueModel->getQueueEntry($id);
if (!$current || !isset($validTransitions[$current['status'] ?? '']) || !in_array($status, $validTransitions[$current['status']], true)) {
    header("Location: index.php?error=update_failed");
    exit;
}

if ($queueModel->updateStatus($id, $status)) {
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Marked queue #$id as $status");
    header("Location: index.php?success=updated");
} else {
    header("Location: index.php?error=update_failed");
}
exit;
