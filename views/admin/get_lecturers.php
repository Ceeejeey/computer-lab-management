<?php
include '../../config/config.php';

$sql = "SELECT id, name FROM users WHERE role = 'lecturer'"; 
$result = $conn->query($sql);

$lecturers = [];
while ($row = $result->fetch_assoc()) {
    $lecturers[] = ['id' => $row['id'], 'name' => $row['name']];
}

header('Content-Type: application/json');
echo json_encode($lecturers);

$conn->close();
?>
