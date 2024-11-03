<?php
// report_attendance.php

session_start();
include '../config/config.php'; 

// Assuming lecturer is filtering by batch and/or session date
$batch = $_GET['batch'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build a base query
$query = "SELECT students.student_id, students.name, lab_schedule.topic, attendance.attendance_date, attendance.status 
          FROM attendance 
          JOIN students ON attendance.student_id = students.student_id 
          JOIN lab_schedule ON attendance.session_id = lab_schedule.id 
          WHERE students.batch = ?";

// Add date filters if provided
if ($date_from && $date_to) {
    $query .= " AND attendance.attendance_date BETWEEN ? AND ?";
}

$stmt = $conn->prepare($query);

if ($date_from && $date_to) {
    $stmt->bind_param("sss", $batch, $date_from, $date_to);
} else {
    $stmt->bind_param("s", $batch);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch data into an array for display
$attendanceData = [];
while ($row = $result->fetch_assoc()) {
    $attendanceData[] = $row;
}
$stmt->close();
$conn->close();
?>
