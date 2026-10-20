<?php

require_once "../includes/auth_check.php";
require_role(['Admin']);

require_once "../config/database.php";
require_once "../classes/User.php";

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);
$users = $userModel->getUsers();

$base = "../";
$active = "users";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | Queunic</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">Users</h1>
            <p class="page-subtitle">Manage system accounts.</p>
        </div>
        <a href="add.php" class="btn-primary-custom">+ New User</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success-custom">
            <?php
            $msgs = ['added' => 'User added.', 'updated' => 'User updated.', 'deleted' => 'User deleted.'];
            echo htmlspecialchars($msgs[$_GET['success']] ?? 'Success.');
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error-custom">
            <?php
            $errs = ['cannot_delete_self' => 'You cannot delete your own account.', 'delete_failed' => 'Could not delete the user.'];
            echo htmlspecialchars($errs[$_GET['error']] ?? 'Something went wrong.');
            ?>
        </div>
    <?php endif; ?>

    <div class="system-card">
        <div class="table-container">
            <table class="system-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row["user_id"]) ?></td>
                            <td><?= htmlspecialchars($row["full_name"]) ?></td>
                            <td><?= htmlspecialchars($row["username"]) ?></td>
                            <td><?= htmlspecialchars($row["role"]) ?></td>
                            <td><?= htmlspecialchars($row["contact_number"]) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row["user_id"] ?>" class="btn-edit">Edit</a>
                                    <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                                        <a href="delete.php?id=<?= $row["user_id"] ?>" class="btn-delete"
                                           onclick="return confirm('Delete this user account?');">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="no-records">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
