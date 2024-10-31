<?php

include '../config/config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        echo "All fields are required!";
    } 
    
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }
    
    else {
        // Password hashing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the database
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
}
?>