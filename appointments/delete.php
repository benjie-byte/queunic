<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Appointment.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

if ($appointment->deleteAppointment($id)) {
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Deleted appointment #$id");
    header("Location: index.php?success=deleted");
    exit;
} else {
    header("Location: index.php?error=delete_failed");
    exit;
}
