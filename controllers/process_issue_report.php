<?php
session_start();
include '../config/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$user_type = ''; 
$user_id = 0; 

// Check if the user is a student or a lecturer
if (isset($_SESSION['student_id'])) {
    $user_type = 'Student';
    $user_id = $_SESSION['student_id'];
} elseif (isset($_SESSION['lecturer_id'])) {
    $user_type = 'Lecturer';
    $user_id = $_SESSION['lecturer_id'];
} else {
    // Redirect to login if neither is set
    header("Location: login.php");
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $issue_type = mysqli_real_escape_string($conn, $_POST['issue_type']);
    $computer_id = mysqli_real_escape_string($conn, $_POST['computer_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $created_at = date("Y-m-d H:i:s");

    // SQL statement with placeholders for `student_id` or `lecturer_id`
    $sql = "INSERT INTO issues (student_id, lecturer_id, computer_id, issue_type, description, priority, created_at, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

    // Prepare and bind parameters with appropriate values
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        // Log error if SQL statement preparation fails
        error_log("SQL error: " . $conn->error);
        $_SESSION['error'] = "Failed to prepare the statement. Please contact support.";
        exit;
    }

    // Set `student_id` or `lecturer_id` as NULL when not used
    $student_id = ($user_type === 'Student') ? $user_id : NULL;
    $lecturer_id = ($user_type === 'Lecturer') ? $user_id : NULL;

    // Bind parameters and execute the statement
    $stmt->bind_param("iisssss", $student_id, $lecturer_id, $computer_id, $issue_type, $description, $priority, $created_at);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Issue reported successfully!";
        $redirect_page = ($user_type === 'Student') ? "../views/dashboards/student_dashboard.php" : "./views/dashboard/lecturer_dashboard.php";
        header("Location: $redirect_page");
        exit;
    } else {
        error_log("Execution error: " . $stmt->error);
        $_SESSION['error'] = "Error reporting the issue. Please try again.";
        $error_page = ($user_type === 'Student') ? "../views/student/report_issue.php" : "./views/lecturer/report_issue.php";
        header("Location: $error_page");
        exit;
    }
} else {
    // Redirect if accessed without form submission
    header("Location: ./views/student/report_issue.php");
    exit;
}
?>
