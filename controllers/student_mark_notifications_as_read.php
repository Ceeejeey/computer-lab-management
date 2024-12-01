<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$studentId = $_SESSION['student_id'];

// Update notifications as read
$updateQuery = "UPDATE student_notifications SET is_read = 1 WHERE student_id = ? AND is_read = 0";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>
