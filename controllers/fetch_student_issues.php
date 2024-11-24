<?php
session_start();
include '../config/config.php'; // Adjust to your DB connection file

$student_id = $_SESSION['student_id']; // Assumes student is logged in

// Query to fetch issues along with the action taken by the admin from the ongoing_issue table
$sql = "
   SELECT 
        issues.issue_id, 
        issues.computer_id, 
        issues.issue_type, 
        issues.description, 
        issues.priority, 
        issues.created_at, 
        issues.status, 
        ongoing_issues.action_taken 
    FROM issues 
    LEFT JOIN ongoing_issues
        ON issues.issue_id = ongoing_issues.issue_id 
    WHERE issues.student_id = ? 
    ORDER BY issues.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$issues = [];
while ($row = $result->fetch_assoc()) {
    $issues[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($issues);
