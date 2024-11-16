<?php
// Include database configuration file
include '../../config/config.php';

// Fetch batch list for the filter dropdown
$batchQuery = "SELECT DISTINCT batch FROM students ORDER BY batch";
$batchResult = $conn->query($batchQuery);

// Fetch students based on selected batch
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
$studentQuery = "SELECT reg_no, name, email, batch, created_at FROM students";
if ($selectedBatch) {
    $studentQuery .= " WHERE batch = '$selectedBatch'";
}
$studentResult = $conn->query($studentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family:'poppins', Arial, sans-serif;
            background-color: #f8fafc;
        }
        
        .container {
            margin-top: 50px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .table thead {
            background-color: #000;
            color: #ffffff;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }
        
        .table-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4>Student List</h4>
        </div>
        <div class="card-body">
            <!-- Batch Filter Form -->
            <form method="GET" class="mb-3 d-flex align-items-center">
                <label for="batch" class="me-2">Filter by Batch:</label>
                <select name="batch" id="batch" class="form-select w-auto me-2">
                    <option value="">All Batches</option>
                    <?php
                    // Populate batch dropdown options
                    if ($batchResult->num_rows > 0) {
                        while ($batchRow = $batchResult->fetch_assoc()) {
                            $selected = ($batchRow['batch'] == $selectedBatch) ? 'selected' : '';
                            echo "<option value='{$batchRow['batch']}' $selected>{$batchRow['batch']}</option>";
                        }
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <!-- Student Table -->
            <div class="table-wrapper">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Batch</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display students in the selected batch
                        if ($studentResult->num_rows > 0) {
                            while ($row = $studentResult->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['reg_no']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['batch']}</td>
                                    <td>" . date("d-m-Y", strtotime($row['created_at'])) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No students found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>

        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
