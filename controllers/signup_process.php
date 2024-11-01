<?php

session_start();
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Get the role from the form
    
    $adminExists = false;

    // Check if an admin already exists
    $query = "SELECT * FROM users WHERE role = 'admin'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $adminExists = true;
    }
    
    // Ensure all required fields are provided
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        die("All fields are required!");
    }
    
    // Ensure passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Restrict role selection to "lecturer" if admin already exists
    if ($role == 'admin' && $adminExists) {
        die("An admin already exists. Please select another role.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Redirect to the sign-in page
        header('Location: ../views/auth/signin.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
