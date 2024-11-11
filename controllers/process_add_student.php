<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $batch = trim($_POST['batch']);
    $reg_no = trim($_POST['registration_number']);
    
    // Set a default password (same for all students)
    $password = password_hash('1234', PASSWORD_DEFAULT); // Change this to your preferred default password

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO students (name,reg_no, email, password, batch) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $reg_no,  $email, $password, $batch);

    // Execute the statement
    if ($stmt->execute()) {
        // Success
        echo "<script>alert('Student added successfully!'); window.location.href='../views/lecturer/add_students.php';</script>";
    } else {
        // Failure
        echo "<script>alert('Error adding student: " . $stmt->error . "'); window.location.href='../views/lecturer/add_students.php';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the add student page if accessed incorrectly
    header("Location: ../views/lecturer/add_students.php");
    exit();
}
