<?php
// Include database configuration file
include '../config/config.php';

// Fetch lecturers' data
$lecturerQuery = "SELECT id, name, email FROM users WHERE role = 'lecturer'"; // assuming 'users' table contains lecturers
$lecturerResult = $conn->query($lecturerQuery);

// Check if there are any lecturers
if ($lecturerResult->num_rows > 0) {
    // Set headers to force download as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="lecturer_report.csv"');

    // Open the output stream
    $output = fopen('php://output', 'w');

    // Add the header row to the CSV file
    fputcsv($output, ['ID', 'Name', 'Email']);

    // Add data rows to the CSV file
    while ($row = $lecturerResult->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['name'], $row['email']]);
    }

    // Close the output stream
    fclose($output);

    // Close the database connection
    $conn->close();
    exit();
} else {
    echo "No lecturer data available to download.";
    exit();
}
?>
