<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Consultation.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$consultationModel = new Consultation($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

if ($consultationModel->deleteConsultation($id)) {
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Deleted consultation #$id");
    header("Location: index.php?success=deleted");
    exit;
} else {
    header("Location: index.php?error=delete_failed");
    exit;
}
