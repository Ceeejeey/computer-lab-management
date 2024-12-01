<?php
session_start();
include '../config/config.php'; // Database connection

// Ensure the session variable is correctly set
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$lecturer_id = $_SESSION['lecturer_id']; // Correct variable assignment

// Query to fetch issues along with action taken
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
    WHERE issues.lecturer_id = ? 
    ORDER BY issues.created_at DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit();
}

$stmt->bind_param("i", $lecturer_id);
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
