<?php
session_start();
include '../config/config.php'; // Include database connection file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lecturer_id = $_SESSION['lecturer_id']; // Assuming lecturer is logged in
    $batch = $_POST['batch'];
    $topic = $_POST['topic'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Check for lab availability in the specified time slot
    $availability_check_sql = "SELECT * FROM lab_requests 
                                WHERE status = 'approved' 
                                AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))";
    $stmt = mysqli_prepare($conn, $availability_check_sql);
    mysqli_stmt_bind_param($stmt, "ssss", $start_time, $start_time, $end_time, $end_time);
    mysqli_stmt_execute($stmt);
    $availability_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($availability_result) == 0) {
        // Lab is available, proceed to insert the new request
        $insert_sql = "INSERT INTO lab_requests (lecturer_id, batch, topic, start_time, end_time, status) 
                       VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "issss", $lecturer_id, $batch, $topic, $start_time, $end_time);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Lab request submitted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error submitting lab request.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Lab is not available
        $_SESSION['message'] = "Lab is not available for the requested time slot.";
        $_SESSION['message_type'] = "error";
    }

    // Redirect back to the request lab page
    header("Location: ../views/lecturer/request_lab.php");
    exit;
}

// Close the database connection
mysqli_close($conn);
?>
