<?php
session_start();
include '../config/config.php';

// Check if batch and date range parameters are provided
$batch = $_GET['batch'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Set up the headers to download the file as a CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=attendance_report.csv');

// Open the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, ['Student ID', 'Name', 'Session Topic', 'Date', 'Status']);

// Build the query to fetch attendance data
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

// Fetch and write each row to the CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
fclose($output);
exit;
?>
