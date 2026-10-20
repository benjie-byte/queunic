<?php

require_once "../includes/auth_check.php";
require_role(['Admin']);

require_once "../config/database.php";
require_once "../classes/User.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

if ($id === (int) $_SESSION['user_id']) {
    header("Location: index.php?error=cannot_delete_self");
    exit;
}

if ($userModel->deleteUser($id)) {
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Deleted user #$id");
    header("Location: index.php?success=deleted");
    exit;
} else {
    header("Location: index.php?error=delete_failed");
    exit;
}
