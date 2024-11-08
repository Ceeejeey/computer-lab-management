<?php
session_start();
include '../config/config.php'; // Adjust to your DB connection file

$student_id = $_SESSION['student_id']; // Assumes student is logged in

$sql = "SELECT issue_id, computer_id, issue_type, description, priority, created_at, status 
        FROM issues 
        WHERE student_id = ? 
        ORDER BY created_at DESC";

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
?>
