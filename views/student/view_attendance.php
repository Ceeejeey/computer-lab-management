<?php
session_start();
include '../../config/config.php';

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch attendance records for the student
$sql = "SELECT 
            attendance.lab_session_id, 
            attendance.date, 
            attendance.status, 
            attendance.marked_at, 
            lab_schedule.topic 
        FROM attendance
        JOIN lab_schedule ON attendance.lab_session_id = lab_schedule.id
        WHERE attendance.student_id = ?
        ORDER BY attendance.date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            max-width: 1000px;
            padding: 20px;
            margin: 0 auto;
            margin-top: 20px;
        }
        .attendance-card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 30px;
        }
        .attendance-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #4a4a4a;
            text-align: center;
            margin-bottom: 20px;
        }
        .attendance-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .attendance-table th {
            background-color: #333;
            color: #fff;
            font-weight: 500;
        }
        .attendance-table td {
            vertical-align: middle;
        }
        .status-present {
            color: #28a745;
            font-weight: bold;
        }
        .status-absent {
            color: #dc3545;
            font-weight: bold;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-dashboard {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #3b82f6;
            color: #ffffff;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="attendance-card">
    <a href="../dashboards/student_dashboard.php" class="btn-dashboard">Go to Dashboard</a>
        <h2 class="attendance-title">My Attendance Records</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped table-hover attendance-table">
                <thead>
                <tr>
                    <th scope="col">Session ID</th>
                    <th scope="col">Topic</th>
                    <th scope="col">Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Marked At</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['lab_session_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['topic']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td class="<?php echo $row['status'] == 'present' ? 'status-present' : 'status-absent'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['marked_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No attendance records found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
