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
    header("Location: login.php");
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_type = mysqli_real_escape_string($conn, $_POST['issue_type']);
    $computer_id = mysqli_real_escape_string($conn, $_POST['computer_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO issues (student_id, lecturer_id, computer_id, issue_type, description, priority, created_at, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);

    $student_id = ($user_type === 'Student') ? $user_id : NULL;
    $lecturer_id = ($user_type === 'Lecturer') ? $user_id : NULL;

    if ($stmt && $stmt->bind_param("iisssss", $student_id, $lecturer_id, $computer_id, $issue_type, $description, $priority, $created_at) && $stmt->execute()) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Issue reported successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to report the issue. Please try again.'];
    }

    $stmt->close();

    // Redirect based on user type
    if ($user_type === 'Student') {
        header("Location: ../views/student/report_issue.php");
    } else {
        header("Location: ../views/lecturer/report_issue.php");
    }
    exit;
}
?>
