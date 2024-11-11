<?php
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = "UPDATE maintenance SET status = ? WHERE maintenance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        header("Location: ../views/admin/schedule_maintain.php?message=Status updated successfully");
    } else {
        header("Location: ../views/admin/schedule_maintain.php?message=Failed to update status");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
