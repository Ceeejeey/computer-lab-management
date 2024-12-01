<?php
session_start();
include '../config/config.php';

// Replace with the logged-in user's ID
$user_id = $_SESSION['lecturer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update notifications to mark as read
    $stmt = $conn->prepare("UPDATE lecturer_notifications SET is_read = 1 WHERE lecturer_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notifications marked as read.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark notifications as read.']);
    }

    $stmt->close();
    $conn->close();
}
?>
