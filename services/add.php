<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Service.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$serviceModel = new Service($conn);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $service_name = trim($_POST["service_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $average_duration = filter_var($_POST["average_duration"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

    if ($service_name === "" || $average_duration === false || $average_duration === null) {
        $error = "Please enter a valid service name and a duration of at least 1 minute.";
    } elseif ($serviceModel->addService($service_name, $description, $average_duration)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Added new service: $service_name");
        header("Location: index.php?success=added");
        exit;
    } else {
        $error = "Unable to add service.";
    }
}

$base = "../";
$active = "services";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New Service</h1>
            <p class="page-subtitle">Add a service the clinic offers.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Service Name</label>
                <input type="text" name="service_name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Average Duration (minutes)</label>
                <input type="number" name="average_duration" min="1" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Save Service</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
