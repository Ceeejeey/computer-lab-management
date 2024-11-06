<?php
session_start();
include '../config/config.php';


// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $student_id = $_SESSION['student_id'];
    $issue_type = mysqli_real_escape_string($conn, $_POST['issue_type']);
    $computer_id = mysqli_real_escape_string($conn, $_POST['computer_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $created_at = date("Y-m-d H:i:s");

    // Prepare the SQL statement
    $sql = "INSERT INTO issues (student_id, computer_id, issue_type, description, priority, created_at, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $student_id, $computer_id, $issue_type, $description, $priority, $created_at);

    // Execute and provide feedback to the user
    if ($stmt->execute()) {
        // Redirect back to the form page with a success message
        $_SESSION['message'] = "Issue reported successfully!";
        header("Location: ../views/dashboards/student_dashboard.php");
        exit;
    } else {
        // Redirect back with an error message
        $_SESSION['error'] = "Error reporting the issue. Please try again.";
        header("Location: ./views/student/report_issue.php");
        exit;
    }

} else {
    // Redirect to report issue page if accessed directly
    header("Location: ./views/student/report_issue.php");
    exit;
}
?>
