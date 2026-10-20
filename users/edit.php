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
$record = $userModel->getUser($id);

if (!$record) {
    die("User not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $role = $_POST["role"] ?? "";
    $contact_number = trim($_POST["contact_number"] ?? "");

    $allowedRoles = ['Admin', 'Clinic Staff', 'Employee', 'Patient'];

    if ($full_name === "" || $username === "" || $role === "" || $contact_number === "") {
        $error = "Please fill in all fields.";
    } elseif (!in_array($role, $allowedRoles, true)) {
        $error = "Invalid role selected.";
    } elseif ($userModel->usernameTakenByOther($username, $id)) {
        $error = "That username is already taken.";
        $record = array_merge($record, $_POST);
    } elseif ($userModel->updateUser($id, $full_name, $username, $role, $contact_number)) {
        (new ActivityLog($conn))->log($_SESSION['user_id'], "Updated user #$id");
        header("Location: index.php?success=updated");
        exit;
    } else {
        $error = "Failed to update user.";
        $record = array_merge($record, $_POST);
    }
}

$base = "../";
$active = "users";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="page-subtitle">Update user #<?= htmlspecialchars((string) $id) ?>.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($record['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($record['username']) ?>" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <?php foreach (['Admin', 'Clinic Staff', 'Employee', 'Patient'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $record['role'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="tel" name="contact_number" value="<?= htmlspecialchars($record['contact_number']) ?>" required>
            </div>

            <p style="color:#718096; font-size:13px;">
                To reset this user's password, use a dedicated "reset password" flow — not implemented here to avoid handling raw passwords insecurely from a shared edit form.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Update User</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
