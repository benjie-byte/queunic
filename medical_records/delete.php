<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/MedicalRecord.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$recordModel = new MedicalRecord($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

if ($recordModel->deleteRecord($id)) {
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Deleted medical record #$id");
    header("Location: index.php?success=deleted");
    exit;
} else {
    header("Location: index.php?error=delete_failed");
    exit;
}
