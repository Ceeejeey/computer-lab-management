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

    // Validate that start_time is earlier than end_time
    if (strtotime($start_time) >= strtotime($end_time)) {
        echo "Start time must be earlier than end time.";
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert maintenance record
        $stmt = $conn->prepare("
            INSERT INTO maintenance (start_time, end_time, description, status, admin_id) 
            VALUES (?, ?, ?, 'Scheduled', ?)
        ");
        $stmt->bind_param("sssi", $start_time, $end_time, $description, $admin_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to schedule maintenance. Error: " . $stmt->error);
        }

        // Notification details
        $notification_title = "Scheduled Maintenance";
        $notification_message = "Maintenance is scheduled from " . date("d M Y, H:i", strtotime($start_time)) . 
                                 " to " . date("d M Y, H:i", strtotime($end_time)) . 
                                 ". Description: $description";

        // Insert notifications for all lecturers
        $notification_query = "
            INSERT INTO lecturer_notifications (lecturer_id, title, message) 
            SELECT id, ?, ? 
            FROM users 
            WHERE role = 'lecturer'
        ";
        $notification_stmt = $conn->prepare($notification_query);
        $notification_stmt->bind_param("ss", $notification_title, $notification_message);

        if (!$notification_stmt->execute()) {
            throw new Exception("Failed to send notifications to lecturers. Error: " . $notification_stmt->error);
        }

        // Insert notifications for all students
        $student_notification_query = "
            INSERT INTO student_notifications (student_id, title, message) 
            SELECT id, ?, ? 
            FROM students
        ";
        $student_notification_stmt = $conn->prepare($student_notification_query);
        $student_notification_stmt->bind_param("ss", $notification_title, $notification_message);

        if (!$student_notification_stmt->execute()) {
            throw new Exception("Failed to send notifications to students. Error: " . $student_notification_stmt->error);
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the form page with a success parameter
        header("Location: ../views/admin/schedule_maintain.php?success=true");
        exit();
    } catch (Exception $e) {
        // Roll back the transaction in case of error
        $conn->rollback();
        echo $e->getMessage();
    } finally {
        // Close the prepared statements and the connection
        if (isset($stmt)) $stmt->close();
        if (isset($notification_stmt)) $notification_stmt->close();
        if (isset($student_notification_stmt)) $student_notification_stmt->close();
        $conn->close();
    }
}
?>
