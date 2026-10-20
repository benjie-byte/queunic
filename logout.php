<?php
session_start();

if (isset($_SESSION['user_id'])) {
    require_once "config/database.php";
    require_once "classes/ActivityLog.php";

    $database = new Database();
    $conn = $database->connect();
    (new ActivityLog($conn))->log($_SESSION['user_id'], "Logged out");
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: login.php");
exit;
