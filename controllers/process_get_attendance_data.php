<?php
include '../config/config.php';
session_start();

// Assume lecturer ID is stored in session after login
$lecturer_id = $_SESSION['lecturer_id'];

// Query to count attendance for each session for the logged-in lecturer within the last 30 days
$sql = "SELECT attendance.lab_session_id AS session_id, COUNT(attendance.attendance_id) AS attendance_count 
        FROM attendance 
        JOIN lab_schedule ON attendance.lab_session_id = lab_schedule.id 
        WHERE lab_schedule.lecturer_id = ? 
          AND attendance.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        GROUP BY attendance.lab_session_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

$attendanceData = [];
while ($row = $result->fetch_assoc()) {
    $attendanceData[] = [
        'session_id' => $row['session_id'],
        'attendance_count' => $row['attendance_count']
    ];
}

header('Content-Type: application/json');
echo json_encode($attendanceData);

$stmt->close();
$conn->close();
?>
