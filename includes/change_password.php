<?php
session_start();
include '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assuming role is stored in session (e.g., 'admin' or 'lecturer')

// Initialize variables
$error = '';
$success = '';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if the new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        $error = 'New password and confirm password do not match.';
    } else {
        // Fetch current password from database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            $error = 'Current password is incorrect.';
        } else {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $user_id);
            if ($stmt->execute()) {
                $success = 'Password changed successfully.';
            } else {
                $error = 'An error occurred while changing the password.';
            }
            $stmt->close();
        }
    }
}

// Redirect to the appropriate dashboard based on role
if ($user_role == 'admin') {
    $dashboardUrl = '../views/dashboards/admin_dashboard.php';
} else if ($user_role == 'lecturer') {
    $dashboardUrl = '../views/dashboards/lecturer_dashboard.php';
} else {
    // Default fallback
    $dashboardUrl = '../index.php';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your custom CSS -->
</head>
    <style>
        /* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f7fc;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Container Styles */
.container {
    max-width: 600px;
    margin: 50px auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Heading */
h2 {
    text-align: center;
    color: #4e73df;
    font-size: 28px;
    margin-bottom: 30px;
}

/* Form Styles */
.form-label {
    font-weight: bold;
    color: #555;
}

.form-control {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 20px;
    width: 100%;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #4e73df;
    outline: none;
}

.btn {
    background-color: #4e73df;
    color: #fff;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #2e5bdb;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-size: 16px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .container {
        margin: 20px;
        padding: 20px;
    }
    
    h2 {
        font-size: 24px;
    }
}

    </style>
<body>
    <div class="container">
        <h2>Change Password</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Change Password</button>
        </form>

        <a href="<?php echo $dashboardUrl; ?>" class="btn btn-secondary mt-3">Go Back to Dashboard</a>
    </div>
</body>

</html>
