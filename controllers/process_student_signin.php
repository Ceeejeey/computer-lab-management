<?php
session_start();
include '../config/config.php'; // Database connection file

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Input sanitization
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Prepare SQL statement to find the user in the students table
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    if ($student && password_verify($password, $student['password'])) {
        // Successful sign-in: Set session variables and redirect to dashboard
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['name'];
        $_SESSION['student_batch'] = $student['batch']; // Set batch for future use
        
        // Redirect to the student dashboard
        // Close the prepared statement and the database connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        header("Location: ../views/dashboards/student_dashboard.php");
        exit; // Ensure no further code is executed after redirect
    } else {
        // Invalid credentials: set a toast notification flag
        $_SESSION['showInvalidPasswordToast'] = true;

        // Close the prepared statement and the database connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header('Location: ../views/auth/student_signin.php');
        exit;
    }
} else {
    // Direct access without form submission: Redirect back to sign-in
    header("Location: student_signin.php");
    exit;
}
?>
