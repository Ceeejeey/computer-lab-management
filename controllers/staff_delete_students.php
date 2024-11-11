<?php
// Connect to database and start the session
include '../config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // SQL query to delete the student
    $sql = "DELETE FROM students WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        
        header("Location: ../views/lecturer/modify_students.php?delete_success=true"); // Redirect back to the student list
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
