<?php
session_start();
include '../config/config.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "Unauthorized access. Please log in as an admin.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input data
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = $_POST['description'];
    $admin_id = $_SESSION['admin_id']; // Get the admin ID from session

    // Check if start_time is before end_time
    if (strtotime($start_time) >= strtotime($end_time)) {
        echo "Start time must be earlier than end time.";
        exit();
    }

    // Insert maintenance record
    $stmt = $conn->prepare("
        INSERT INTO maintenance (start_time, end_time, description, status, admin_id) 
        VALUES (?, ?, ?, 'Scheduled', ?)
    ");
    $stmt->bind_param("sssi", $start_time, $end_time, $description, $admin_id);

    if ($stmt->execute()) {
        // Redirect to the form page with a success parameter
        header("Location: ../views/admin/schedule_maintain.php?success=true");
        exit();
    } else {
        echo "Failed to schedule maintenance. Error: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
?>
