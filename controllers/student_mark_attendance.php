<?php
session_start();
include '../config/config.php'; // Adjust this path as needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assuming the student ID is stored in the session
    if (!isset($_SESSION['student_id'])) {
        echo "Student ID not found in session.";
        exit;
    }
    
    $student_id = $_SESSION['student_id'];
    $lab_session_id = $_POST['session_id']; // Change session_id to lab_session_id

    // Check if attendance has already been marked for this session
    $checkQuery = "SELECT * FROM attendance WHERE student_id = ? AND lab_session_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $student_id, $lab_session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Mark attendance if not already marked
        $insertQuery = "INSERT INTO attendance (student_id, lab_session_id, date, status, marked_at) VALUES (?, ?, NOW(), 'Present', NOW())";
        $stmt->close(); // Close previous statement
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $student_id, $lab_session_id);
        
        if ($stmt->execute()) {
            echo "Attendance marked successfully!";
        } else {
            echo "Failed to mark attendance. Try again.";
        }
    } else {
        echo "Attendance already marked for this session.";
    }

    $stmt->close(); // Close statement after execution
} else {
    echo "Invalid request method.";
}

$conn->close(); // Close database connection
?>
