<?php
// Include database configuration file
include '../../config/config.php';

// Fetch lecturers' data
$lecturerQuery = "SELECT id, name, email  FROM users WHERE role = 'lecturer'"; // assuming 'users' table contains lecturers
$lecturerResult = $conn->query($lecturerQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lecturers</title>
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
            <h4>Lecturer List</h4>
        </div>
        <div class="card-body">
            <!-- Lecturer Table -->
            <div class="table-wrapper">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display lecturers
                        if ($lecturerResult->num_rows > 0) {
                            while ($row = $lecturerResult->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['email']}</td>
                                    
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center'>No lecturers found</td></tr>";
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
