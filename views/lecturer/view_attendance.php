<?php
session_start();
include '../../config/config.php';

// Fetch lab sessions for the dropdown
$labSessions = [];
$stmt = $conn->prepare("SELECT id, topic, batch, start_time, end_time FROM lab_schedule ORDER BY start_time ASC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $labSessions[] = $row;
}
$stmt->close();

// Initialize selected session details
$selectedSessionDetails = null;
$attendanceData = [];

// Fetch attendance data and session details based on selected session
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];

    // Get session details for selected session
    $stmt = $conn->prepare("SELECT topic, batch, start_time, end_time FROM lab_schedule WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedSessionDetails = $result->fetch_assoc();
    $stmt->close();

    // Fetch attendance data for the selected session
    $query = "SELECT attendance.student_id, students.name, attendance.date, attendance.status
              FROM attendance
              JOIN students ON attendance.student_id = students.id
              WHERE attendance.lab_session_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
    $stmt->close();
    $conn->close();
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
            font-family: Arial, sans-serif;
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
            font-weight: 600;
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
        <form method="GET" class="mb-4">
            <label for="session_id">Lab Session:</label>
            <select name="session_id" id="session_id" class="form-select" onchange="this.form.submit()">
                <option value="">Select Session</option>
                <?php foreach ($labSessions as $session): ?>
                    <option value="<?php echo htmlspecialchars($session['id']); ?>" <?php echo isset($session_id) && $session_id == $session['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($session['topic'] . " (Batch " . $session['batch'] . ")"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($selectedSessionDetails): ?>
            <h5>Session Details:</h5>
            <p>Topic: <?php echo htmlspecialchars($selectedSessionDetails['topic']); ?></p>
            <p>Batch: <?php echo htmlspecialchars($selectedSessionDetails['batch']); ?></p>
            <p>Date and Time: <?php echo date('Y-m-d H:i', strtotime($selectedSessionDetails['start_time'])) . ' - ' . date('H:i', strtotime($selectedSessionDetails['end_time'])); ?></p>
        <?php endif; ?>

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
                <?php if (!empty($attendanceData)): ?>
                    <?php foreach ($attendanceData as $data): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($data['name']); ?></td>
                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                            <td><?php echo htmlspecialchars($data['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No attendance records found for this session.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>