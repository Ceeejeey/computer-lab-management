<?php
session_start();
include '../../config/config.php';

// Fetch all lecturers for the filter
$lecturers = [];
$stmt = $conn->prepare("SELECT id, name FROM users WHERE role = 'lecturer'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $lecturers[] = $row;
}
$stmt->close();

// Fetch batches for filter based on selected lecturer
$batches = [];
$selectedLecturerId = isset($_GET['lecturer_id']) ? $_GET['lecturer_id'] : '';
if ($selectedLecturerId) {
    $stmt = $conn->prepare("SELECT DISTINCT batch FROM lab_schedule WHERE lecturer_id = ?");
    $stmt->bind_param("i", $selectedLecturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row['batch'];
    }
    $stmt->close();
}

// Fetch sessions for the filter based on selected batch
$sessions = [];
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
if ($selectedBatch) {
    $stmt = $conn->prepare("SELECT id, topic, start_time, end_time FROM lab_schedule WHERE batch = ? AND lecturer_id = ?");
    $stmt->bind_param("si", $selectedBatch, $selectedLecturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
    $stmt->close();
}

// Initialize selected filters and attendance data
$attendanceData = [];
$selectedSessionId = isset($_GET['session_id']) ? $_GET['session_id'] : '';

// Fetch attendance data based on selected filters
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $selectedSessionId) {
    $query = "SELECT attendance.student_id, students.name, attendance.date, attendance.status
              FROM attendance
              JOIN students ON attendance.student_id = students.id
              WHERE attendance.lab_session_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selectedSessionId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
    $stmt->close();
    $conn->close();
}

// Function to download CSV
if (isset($_GET['download'])) {
    if (!empty($attendanceData)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance_report.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student ID', 'Name', 'Date', 'Status']);
        foreach ($attendanceData as $data) {
            fputcsv($output, $data);
        }
        fclose($output);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family:'poppins', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            color: #343a40;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-select {
            width: 100%;
            max-width: 300px;
            margin: 10px 0;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            color: white;
        }

        .table {
            margin-top: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #343a40;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            text-align: center;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .table td,
        .table th {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        .table td {
            color: #495057;
        }

        .table-striped tbody tr:hover {
            background-color: #e9ecef;
        }

        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }

            .table th,
            .table td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Attendance Report</h2>
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="lecturer_id">Lecturer:</label>
                    <select name="lecturer_id" id="lecturer_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Select Lecturer</option>
                        <?php foreach ($lecturers as $lecturer): ?>
                            <option value="<?php echo htmlspecialchars($lecturer['id']); ?>" <?php echo isset($selectedLecturerId) && $selectedLecturerId == $lecturer['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lecturer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="batch">Batch:</label>
                    <select name="batch" id="batch" class="form-select" onchange="this.form.submit()">
                        <option value="">Select Batch</option>
                        <?php foreach ($batches as $batch): ?>
                            <option value="<?php echo htmlspecialchars($batch); ?>" <?php echo isset($selectedBatch) && $selectedBatch == $batch ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($batch); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="session_id">Session:</label>
                    <select name="session_id" id="session_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Select Session</option>
                        <?php foreach ($sessions as $session): ?>
                            <option value="<?php echo htmlspecialchars($session['id']); ?>" <?php echo isset($selectedSessionId) && $selectedSessionId == $session['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($session['topic'] . " (" . date('Y-m-d H:i', strtotime($session['start_time'])) . " - " . date('H:i', strtotime($session['end_time'])) . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <?php if (!empty($attendanceData)): ?>
            <a href="?lecturer_id=<?php echo htmlspecialchars($selectedLecturerId); ?>&batch=<?php echo htmlspecialchars($selectedBatch); ?>&session_id=<?php echo htmlspecialchars($selectedSessionId); ?>&download=true" class="btn btn-primary mt-3">Download Attendance Report</a>

            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceData as $data): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($data['name']); ?></td>
                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                            <td><?php echo htmlspecialchars($data['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
