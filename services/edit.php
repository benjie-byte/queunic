<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Service.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$serviceModel = new Service($conn);

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];
$record = $serviceModel->getService($id);

if (!$record) {
    die("Service not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $service_name = trim($_POST["service_name"]);
    $description = trim($_POST["description"]);
    $average_duration = $_POST["average_duration"];

    if ($serviceModel->updateService($id, $service_name, $description, $average_duration)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Updated service #$id");
        header("Location: index.php?success=updated");
        exit;
    } else {
        $error = "Failed to update service.";
        $record = array_merge($record, $_POST);
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
    <title>Edit Service | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Edit Service</h1>
            <p class="page-subtitle">Update service #<?= $id ?>.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Service Name</label>
                <input type="text" name="service_name" value="<?= htmlspecialchars($record['service_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($record['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Average Duration (minutes)</label>
                <input type="number" name="average_duration" min="1" value="<?= htmlspecialchars($record['average_duration']) ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Update Service</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
