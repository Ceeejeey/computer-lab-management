<?php
session_start();
include '../config/config.php'; // Include database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lecturer_id = $_POST['lecturer_id'];

    // Check if the lecturer exists
    $check_query = "SELECT * FROM users WHERE id = ? AND role = 'lecturer'";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the lecturer
        $delete_query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $lecturer_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lecturer deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting lecturer.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Lecturer not found.";
        $_SESSION['message_type'] = "error";
    }

    // Redirect back to the view lecturers page
    header("Location: ../views/admin/view_lecturers.php");
    exit;
}

// Close the database connection
$conn->close();
?>
