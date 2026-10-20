<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Patient.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$patientModel = new Patient($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

if ($patientModel->deletePatient($id)) {
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Deleted patient #$id");
    header("Location: index.php?success=deleted");
    exit;
} else {
    header("Location: index.php?error=delete_failed");
    exit;
}
