<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = $_POST['description'];

    // Insert maintenance record
    $stmt = $conn->prepare("INSERT INTO maintenance (start_time, end_time, description, status) VALUES (?, ?, ?, 'Scheduled')");
    $stmt->bind_param("sss", $start_time, $end_time, $description);

    if ($stmt->execute()) {
        echo "Maintenance scheduled successfully!";
    } else {
        echo "Failed to schedule maintenance. Try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
