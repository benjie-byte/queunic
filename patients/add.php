<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../classes/Patient.php";
require_once "../classes/ActivityLog.php";

$database = new Database();
$conn = $database->connect();
$patientModel = new Patient($conn);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $age = filter_var($_POST["age"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 120]]);
    $gender = $_POST["gender"] ?? "";
    $id_number = trim($_POST["id_number"] ?? "");
    $contact_number = trim($_POST["contact_number"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // Get the currently logged-in user's ID
    $user_id = $_SESSION['user_id'] ?? null;

    // Make sure a valid user ID exists
    if ($user_id === null) {

        $error = "Session expired or user ID is missing. Please log in again.";

    } elseif ($full_name === "" || $age === false || $age === null) {

        $error = "Please enter a valid name and an age between 0 and 120.";

    } elseif (!in_array($gender, ["Male", "Female", "Other"], true)) {

        $error = "Please select a valid gender.";

    } else {

        // Save patient with the logged-in user's ID
        if ($patientModel->addPatient(
            $full_name,
            $age,
            $gender,
            $id_number,
            $contact_number,
            $address,
            $user_id
        )) {

            // Record activity
            (new ActivityLog($conn))->log(
                $user_id,
                "Added new patient: $full_name"
            );

            header("Location: index.php?success=added");
            exit;

        } else {

            $error = "Unable to add patient.";

        }
    }
}

$base = "../";
$active = "patients";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Patient | Queunic</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="appointment-page">

<?php require_once "../includes/sidebar.php"; ?>

<div class="main-content">

    <div class="top-header">
        <div>
            <h1 class="page-title">New Patient</h1>
            <p class="page-subtitle">Register a new patient record.</p>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert-error-custom">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="system-form">

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input
                    type="text"
                    name="full_name"
                    required
                >
            </div>

            <div class="form-group">
                <label>Age</label>
                <input
                    type="number"
                    name="age"
                    min="0"
                    max="120"
                    required
                >
            </div>

            <div class="form-group">
                <label>Gender</label>

                <select name="gender" required>
                    <option value="" selected disabled>
                        Select gender
                    </option>

                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>ID Number</label>

                <input
                    type="text"
                    name="id_number"
                    placeholder="e.g. School/Employee/Gov't ID"
                >
            </div>

            <div class="form-group">
                <label>Contact Number</label>

                <input
                    type="tel"
                    name="contact_number"
                    required
                >
            </div>

            <div class="form-group">
                <label>Address</label>

                <textarea
                    name="address"
                    rows="3"
                ></textarea>
            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-primary-custom"
                >
                    Save Patient
                </button>

                <a
                    href="index.php"
                    class="btn-cancel"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>