<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 50px;
        }

        h2,
        h3 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            width: 100%;
        }

        .status-badge {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .badge-ongoing {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-completed {
            background-color: #28a745;
            color: #fff;
        }

        .badge-pending {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Form to Schedule Maintenance -->
        <h2>Schedule Maintenance</h2>
        <!-- Toast Container -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Maintenance scheduled successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <form action="../../controllers/process_schedule_maintain.php" method="POST">
            <div class="mb-4">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
            </div>
            <div class="mb-4">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
            </div>
            <div class="mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of maintenance"></textarea>
                <div class="form-text">Provide details if necessary.</div>
            </div>
            <button type="submit" class="btn btn-primary">Schedule Maintenance</button>
        </form>

        <!-- List of Scheduled Maintenance with Status Update Option -->
        <div class="mt-5">
            <h3>Maintenance Schedules</h3>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../../config/config.php';
                    $sql = "SELECT maintenance_id, description, start_time, end_time, status FROM maintenance";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                            echo "<td><span class='status-badge " .
                                ($row['status'] == 'Completed' ? 'badge-completed' : ($row['status'] == 'Ongoing' ? 'badge-ongoing' : 'badge-pending')) .
                                "'>" . $row['status'] . "</span></td>";
                            echo "<td>
                                <form action='../../controllers/admin_update_maintenance_status.php' method='POST' style='display: inline;'>
                                    <input type='hidden' name='id' value='" . $row['maintenance_id'] . "'>
                                    <select name='status' class='form-select form-select-sm mb-2' onchange='this.form.submit()'>
                                        <option value='Scheduled'" . ($row['status'] == 'Scheduled' ? ' selected' : '') . ">Scheduled</option>
                                        <option value='Ongoing'" . ($row['status'] == 'Ongoing' ? ' selected' : '') . ">Ongoing</option>
                                        <option value='Completed'" . ($row['status'] == 'Completed' ? ' selected' : '') . ">Completed</option>
                                    </select>
                                </form>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No scheduled maintenance</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
            <div class="mt-3">
                <a href="../../controllers/download_completed_report.php" class="btn btn-success">Download Completed Maintenance Report</a>
            </div>

            <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary mt-3">Go Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check for query parameter and show toast
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'true') {
            const toastElement = document.getElementById('successToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    </script>
</body>

</html>