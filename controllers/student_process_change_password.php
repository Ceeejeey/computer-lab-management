<?php
session_start();
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['student_id']; // Assuming student is logged in
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    $sql = "SELECT password FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($current_password, $stored_password)) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE students SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_password_hash, $student_id);

        if ($update_stmt->execute()) {
            header("Location: ../views/student/change_password.php?message=Password updated successfully.");
        } else {
            header("Location: ../views/student/change_password.php?message=Error updating password.");
        }
        $update_stmt->close();
    } else {
        header("Location: ../views/student/change_password.php?message=Incorrect current password.");
    }

    exit;
}

$conn->close();
?>
