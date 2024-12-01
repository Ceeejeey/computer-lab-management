<?php
include '../config/config.php'; // Include the database connection

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=completed_maintenance_report.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Write the header of the CSV file
fputcsv($output, ['Description', 'Start Time', 'End Time', 'Status']);

// Fetch completed maintenance records from the database
$sql = "SELECT description, start_time, end_time, status FROM maintenance WHERE status = 'Completed'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Write each row to the CSV file
        fputcsv($output, [
            $row['description'],
            $row['start_time'],
            $row['end_time'],
            $row['status']
        ]);
    }
}

// Close the database connection
$conn->close();
fclose($output);
exit;
