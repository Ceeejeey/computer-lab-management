<?php
session_start();
include '../config/config.php';

date_default_timezone_set('Asia/Colombo'); // Set timezone to match server and database

// Get today's date and define time ranges
$today = date("Y-m-d");
$startOfToday = $today . " 00:00:00";
$endOfToday = $today . " 23:59:59";

$schedules = [
    'today' => [],
    'upcoming' => []
];

// Fetch today's schedules (within today's date range)
$stmt = $conn->prepare("SELECT batch, topic, start_time, end_time 
                        FROM lab_schedule 
                        WHERE DATE(start_time) = ?"); 
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $schedules['today'][] = $row;
}
$stmt->close();

// Fetch upcoming schedules (start_time greater than end of today)
$stmt = $conn->prepare("SELECT batch, topic, start_time, end_time 
                        FROM lab_schedule 
                        WHERE start_time > ?");
$stmt->bind_param("s", $endOfToday);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $schedules['upcoming'][] = $row;
}
$stmt->close();

$conn->close();

header('Content-Type: application/json');
echo json_encode($schedules);
?>
