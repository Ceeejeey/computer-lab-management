<?php
// Include database configuration file
include '../config/config.php';

// Get the selected batch from the query string
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';

// Fetch students based on the selected batch
$studentQuery = "SELECT reg_no, name, email, batch, created_at FROM students";
if ($selectedBatch) {
    $studentQuery .= " WHERE batch = '$selectedBatch'";
}
$studentResult = $conn->query($studentQuery);

// Check if there are any students
if ($studentResult->num_rows > 0) {
    // Set headers to force download as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_report.csv"');

    // Open the output stream
    $output = fopen('php://output', 'w');

    // Add the header row to the CSV file
    fputcsv($output, ['Reg No:', 'Name', 'Email', 'Batch', 'Joined Date']);

    // Add data rows to the CSV file
    while ($row = $studentResult->fetch_assoc()) {
        fputcsv($output, [
            $row['reg_no'],
            $row['name'],
            $row['email'],
            $row['batch'],
            date("d-m-Y", strtotime($row['created_at']))
        ]);
    }

    // Close the output stream
    fclose($output);

    // Close the database connection
    $conn->close();

    
    exit();
} else {
    echo "No student data available to download.";
    exit();
}
?>
