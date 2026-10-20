<?php

session_start();
require_once "config/database.php";
require_once "classes/User.php";
require_once "classes/ActivityLog.php";

// already logged in? go straight to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {

        $user = $userModel->login($username, $password);

        if ($user) {

            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            if (!empty($_POST['remember'])) {
                // 30-day remember-me cookie holding a lookup token would go here.
                // Kept out of scope for now - session-based login is active.
            }

            (new ActivityLog($conn))->log($user['user_id'], "Logged in");

            header("Location: index.php");
            exit;

        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Queunic</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Login CSS -->
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <div class="main-container">

        <!-- LEFT SIDE -->
        <div class="welcome-section">

            <div class="logo">QUEUNIC</div>

            <h1>Welcome Back<br>to <span>Queunic</span></h1>

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

            <h2>Login</h2>

            <p class="subtitle">Sign in to continue to Queunic.</p>

            <!-- ERROR MESSAGE -->
            <?php if (!empty($error)): ?>
                <div class="error-box"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">

                <!-- USERNAME -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        id="username"
                        placeholder="Enter your username"
                        required
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
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
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <!-- REMEMBER + FORGOT -->
                <div class="login-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <!-- LOGIN BUTTON -->
                <button type="submit" class="btn-login">Login</button>

            </form>

            <!-- SIGN UP -->
            <div class="signup-text">
                Don't have an account?
                <a href="register.php">Sign up</a>
            </div>

        </div>

    </div>

</body>
</html>
