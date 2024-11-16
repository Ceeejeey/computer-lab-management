<?php
session_start();
include('../../config/config.php');

// Get unique batch values for filtering
$batch_sql = "SELECT DISTINCT batch FROM students";
$batch_result = mysqli_query($conn, $batch_sql);

// Default filter condition
$batch_filter = isset($_GET['batch']) ? $_GET['batch'] : '';

// SQL query to filter students by batch
$sql = "SELECT * FROM students";
if ($batch_filter) {
    $sql .= " WHERE batch = '" . mysqli_real_escape_string($conn, $batch_filter) . "'";
}

$result = mysqli_query($conn, $sql);

// Handle successful deletion
if (isset($_GET['delete_success']) && $_GET['delete_success'] == 'true') {
    $delete_message = "Student deleted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management | Computer Laboratory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
        }

        .container {
            margin-top: 50px;
            max-width: 1000px;
            padding: 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Student Management</h2>

        <!-- Toast Notification for Successful Deletion -->
        <?php if (isset($delete_message)): ?>
            <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $delete_message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Batch Filter Form -->
        <form method="GET" action="" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="batch" class="form-select" onchange="this.form.submit()">
                        <option value="">All Batches</option>
                        <?php while ($batch_row = mysqli_fetch_assoc($batch_result)): ?>
                            <option value="<?php echo $batch_row['batch']; ?>" <?php echo ($batch_filter == $batch_row['batch']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($batch_row['batch']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Reg No:</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Batch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>" . $row['reg_no'] . "</td>
                            <td>" . $row['name'] . "</td>
                            <td>" . $row['email'] . "</td>
                            <td>" . $row['batch'] . "</td>
                            <td>
                                <a href='../../controllers/staff_delete_students.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this student?\");'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No students found</td></tr>";
                }
                ?>
            </tbody>
        </table><br>
        <a href="../dashboards/lecturer_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
