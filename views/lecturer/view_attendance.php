<?php
session_start();
include '../../config/config.php';

// Initialize session variables
$labSessions = [];
$batches = [];
$selectedSessionDetails = null;
$attendanceData = [];
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

// Fetch batches for filter (done once, as batches are static for all sessions)
$batches = [];
$stmt = $conn->prepare("SELECT DISTINCT batch FROM students");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $batches[] = $row['batch'];
}
$stmt->close();

// Fetch lab sessions based on selected batch
if (!empty($selectedBatch)) {
    $stmt = $conn->prepare("SELECT id, topic, batch, start_time, end_time FROM lab_schedule WHERE batch = ? ORDER BY start_time ASC");
    $stmt->bind_param("s", $selectedBatch);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $labSessions[] = $row;
    }
    $stmt->close();
}

// Fetch attendance data and session details based on selected session
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $session_id && $selectedBatch) {
    // Get session details for selected session
    $stmt = $conn->prepare("SELECT topic, batch, start_time, end_time FROM lab_schedule WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedSessionDetails = $result->fetch_assoc();
    $stmt->close();

    // Fetch attendance data for the selected session and batch
    $query = "SELECT students.reg_no, students.name, attendance.date, attendance.status
              FROM attendance
              JOIN students ON attendance.student_id = students.id
              WHERE attendance.lab_session_id = ? AND students.batch = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $session_id, $selectedBatch);
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
        fputcsv($output, ['Reg No:', 'Name', 'Date', 'Status']);
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
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            color: #343a40;
            font-weight: 400;
            text-align: center;
            margin-bottom: 20px;
        }

        form label {
            font-weight: 500;
            color: #495057;
        }

        .form-select {
            width: 100%;
            max-width: 300px;
            margin: 10px 0;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        h5 {
            color: #495057;
            font-weight: 600;
            margin-top: 20px;
        }

        p {
            margin: 5px 0;
            color: #6c757d;
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

        .text-center {
            font-style: italic;
            color: #6c757d;
            padding: 20px;
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

        select:focus,
        input[type="text"]:focus,
        input[type="date"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
        }

        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }

            .table th,
            .table td {
                padding: 8px;
            }

            .form-select {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Attendance Report</h2>

        <!-- Batch Selection Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6">
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
            </div>
        </form>

        <?php if (!empty($selectedBatch)): ?>
            <!-- Lab Session Selection Form based on the selected batch -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <label for="session_id">Lab Session:</label>
                        <select name="session_id" id="session_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Select Session</option>
                            <?php foreach ($labSessions as $session): ?>
                                <option value="<?php echo htmlspecialchars($session['id']); ?>" <?php echo isset($session_id) && $session_id == $session['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($session['topic'] . " (Batch " . $session['batch'] . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="batch" value="<?php echo htmlspecialchars($selectedBatch); ?>" />
            </form>
        <?php endif; ?>

        <?php if ($selectedSessionDetails): ?>
            <h5>Session Details:</h5>
            <p>Topic: <?php echo htmlspecialchars($selectedSessionDetails['topic']); ?></p>
            <p>Batch: <?php echo htmlspecialchars($selectedSessionDetails['batch']); ?></p>
            <p>Date and Time: <?php echo date('Y-m-d H:i', strtotime($selectedSessionDetails['start_time'])) . ' - ' . date('H:i', strtotime($selectedSessionDetails['end_time'])); ?></p>
        <?php endif; ?>

        <!-- Attendance Table -->
        <?php if (!empty($attendanceData)): ?>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Reg No:</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceData as $data): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['reg_no']); ?></td>
                            <td><?php echo htmlspecialchars($data['name']); ?></td>
                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                            <td><?php echo htmlspecialchars($data['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Download Button -->
            <a href="?session_id=<?php echo htmlspecialchars($session_id); ?>&batch=<?php echo htmlspecialchars($selectedBatch); ?>&download=true" class="btn btn-primary mt-3">Download Attendance Report</a>
        <?php elseif (!empty($selectedBatch)): ?>
            <p class="text-center">No attendance records found for this session and batch.</p>
        <?php endif; ?>

        <a href="../dashboards/lecturer_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>
    </div>
</body>
</html>
