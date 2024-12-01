<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    $status = $action === 'approve' ? 'approved' : 'rejected';

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Update lab request status
        $stmt = $conn->prepare("UPDATE lab_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update request status.");
        }

        // If approved, insert into lab_schedule
        if ($status === 'approved') {
            // Retrieve lab request details
            $selectStmt = $conn->prepare("SELECT batch, topic, start_time, end_time, lecturer_id FROM lab_requests WHERE id = ?");
            $selectStmt->bind_param("i", $request_id);
            $selectStmt->execute();
            $selectStmt->bind_result($batch, $topic, $start_time, $end_time, $lecturer_id);
            
            if (!$selectStmt->fetch()) {
                throw new Exception("Lab request details not found.");
            }
            $selectStmt->close();

            // Insert approved request into lab_schedule
            $insertStmt = $conn->prepare("INSERT INTO lab_schedule (batch, topic, start_time, end_time, lecturer_id) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->bind_param("ssssi", $batch, $topic, $start_time, $end_time, $lecturer_id);

            if (!$insertStmt->execute()) {
                throw new Exception("Failed to add request to lab schedule.");
            }
            $insertStmt->close();

            // Add notification for the lecturer
            $notification_title = "Lab Request Approved";
            $notification_message = "Your lab request for the topic '$topic' has been approved and scheduled.";
        } else {
            // Add notification for the lecturer when rejected
            $selectStmt = $conn->prepare("SELECT topic, lecturer_id FROM lab_requests WHERE id = ?");
            $selectStmt->bind_param("i", $request_id);
            $selectStmt->execute();
            $selectStmt->bind_result($topic, $lecturer_id);

            if (!$selectStmt->fetch()) {
                throw new Exception("Lab request details not found.");
            }
            $selectStmt->close();

            $notification_title = "Lab Request Rejected";
            $notification_message = "Your lab request for the topic '$topic' has been rejected.";
        }

        // Insert notification into notifications table
        $insertNotificationStmt = $conn->prepare("INSERT INTO lecturer_notifications (lecturer_id, title, message, is_read) VALUES (?, ?, ?, 0)");
        $insertNotificationStmt->bind_param("iss", $lecturer_id, $notification_title, $notification_message);

        if (!$insertNotificationStmt->execute()) {
            throw new Exception("Failed to insert notification.");
        }

        $insertNotificationStmt->close();

        // Commit the transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => ucfirst($status) . ' successfully.']);
    } catch (Exception $e) {
        // Rollback the transaction on failure
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } finally {
        // Close prepared statements
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
