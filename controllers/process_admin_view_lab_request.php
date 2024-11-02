<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    $status = $action === 'approve' ? 'approved' : 'rejected';

    // Update lab request status
    $stmt = $conn->prepare("UPDATE lab_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);

    if ($stmt->execute()) {
        // Check if the request was approved
        if ($status === 'approved') {
            // Retrieve the lab request details for the approved request
            $selectStmt = $conn->prepare("SELECT batch, topic, start_time, end_time, lecturer_id FROM lab_requests WHERE id = ?");
            $selectStmt->bind_param("i", $request_id);
            $selectStmt->execute();
            $selectStmt->bind_result($batch, $topic, $start_time, $end_time, $lecturer_id);
            $selectStmt->fetch();
            $selectStmt->close();

            // Insert approved request into lab_schedule
            $insertStmt = $conn->prepare("INSERT INTO lab_schedule (batch, topic, start_time, end_time, lecturer_id) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->bind_param("ssssi", $batch, $topic, $start_time, $end_time, $lecturer_id);
            
            if ($insertStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Request approved and added to lab schedule']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add to lab schedule']);
            }
            
            $insertStmt->close();
        } else {
            
            echo json_encode(['success' => true, 'message' => 'Request rejected']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update request status']);
    }

    $stmt->close();
    $conn->close();
}
?>
