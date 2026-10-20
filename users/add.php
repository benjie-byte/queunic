<?php

require_once "../includes/auth_check.php";
require_role(['Admin']);

require_once "../config/database.php";
require_once "../classes/User.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";
    $contact_number = trim($_POST["contact_number"] ?? "");

    $allowedRoles = ['Admin', 'Clinic Staff', 'Employee', 'Patient'];

    if ($full_name === "" || $username === "" || $password === "" || $role === "" || $contact_number === "") {
        $error = "Please fill in all fields.";
    } elseif (!in_array($role, $allowedRoles, true)) {
        $error = "Invalid role selected.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $result = $userModel->register($full_name, $username, $password, $role, $contact_number);

        if ($result === "taken") {
            $error = "That username is already taken.";
        } elseif ($result) {
            (new ActivityLog($conn))->log($_SESSION['user_id'], "Created a new user account: $username");
            header("Location: index.php?success=added");
            exit;
        } else {
            $error = "Unable to create user.";
        }
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
    <title>Add User | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New User</h1>
            <p class="page-subtitle">Create a system account.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="system-form">
        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="" selected disabled>Select role</option>
                    <option value="Admin">Admin</option>
                    <option value="Clinic Staff">Clinic Staff</option>
                    <option value="Employee">Employee</option>
                    <option value="Patient">Patient</option>
                </select>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="tel" name="contact_number" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-custom">Create User</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
