<?php
include '../config/config.php';

// Get the lecturer ID from the request
$lecturer_id = $_GET['lecturer_id'];

// Query to get attendance counts for each session of the selected lecturer
$sql = "SELECT attendance.lab_session_id AS session_id, COUNT(attendance.attendance_id) AS attendance_count 
        FROM attendance 
        JOIN lab_schedule ON attendance.lab_session_id = lab_schedule.id 
        WHERE lab_schedule.lecturer_id = ? 
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
