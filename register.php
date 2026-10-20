<?php

session_start();
require_once "config/database.php";
require_once "classes/User.php";

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname       = trim($_POST['fullname'] ?? '');
    $username       = trim($_POST['username'] ?? '');
    $password       = $_POST['password'] ?? '';
    $role           = $_POST['role'] ?? '';
    $contact_number = trim($_POST['contact_number'] ?? '');

    // Public sign-up must never create privileged accounts.
    $allowedRoles = ['Clinic Staff', 'Employee', 'Patient'];

    if (empty($fullname) || empty($username) || empty($password) || empty($role) || empty($contact_number)) {
        $error = "Please fill in all fields.";
    } elseif (!in_array($role, $allowedRoles, true)) {
        $error = "Invalid role selected.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {

        $result = $userModel->register($fullname, $username, $password, $role, $contact_number);

        if ($result === "taken") {
            $error = "That username is already taken. Please choose another.";
        } elseif ($result) {
            $success = "Account created successfully! You can now log in.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QUEUNIC SYSTEM</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <div class="main-container">

        <!-- LEFT SIDE -->
        <div class="welcome-section">

            <div class="logo">QUEUNIC</div>

            <h1>Welcome to <span>Queunic</span></h1>

            <p>
                A simple, secure, and modern platform designed to make
                your digital experience easier and more organized.
            </p>

            <div class="feature">
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <span>Easy to use</span>
                </div>
            </div>

        </div>


        <!-- RIGHT SIDE -->
        <div class="form-section">

            <h2>Create Account</h2>

            <p class="subtitle">Sign up to get started with Queunic.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    &mdash; <a href="login.php">Go to Login</a>
                </div>
            <?php endif; ?>

            <form action="" method="POST">

                <!-- FULL NAME -->
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input
                        type="text"
                        name="fullname"
                        class="form-control"
                        id="fullname"
                        placeholder="Enter your full name"
                        value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>"
                        required
                    >
                </div>

                <!-- USERNAME -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        id="username"
                        placeholder="Enter your username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        required
                    >
                </div>

                <!-- PASSWORD -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        id="password"
                        placeholder="Create a password (min. 6 characters)"
                        required
                    >
                </div>

                <!-- ROLE -->
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="" selected disabled>Select your role</option>
                        <option value="Clinic Staff">Clinic Staff</option>
                        <option value="Employee">Employee</option>
                        <option value="Patient">Patient</option>
                    </select>
                </div>

                <!-- CONTACT NUMBER -->
                <div class="mb-4">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input
                        type="tel"
                        name="contact_number"
                        class="form-control"
                        id="contact_number"
                        placeholder="Enter your contact number"
                        value="<?= isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : '' ?>"
                        required
                    >
                </div>

                <!-- CREATE ACCOUNT -->
                <button type="submit" class="btn-signup">Create Account</button>

            </form>

            <!-- LOGIN LINK -->
            <div class="login-text">
                Already have an account?
                <a href="login.php">Login</a>
            </div>

        </div>

    </div>

</body>
</html>
