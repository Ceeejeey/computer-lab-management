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
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .attendance-card {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
            padding: 20px;
        }
        .attendance-table {
            border-radius: 8px;
            overflow: hidden;
        }
        .attendance-table th, .attendance-table td {
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
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">My Attendance Records</h2>
    <div class="attendance-card">
        <?php if ($result->num_rows > 0): ?>
            <table class="table attendance-table">
                <thead class="table-dark">
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
                        <td class="<?php echo $row['status'] == 1 ? 'status-present' : 'status-absent'; ?>">
                            <?php echo $row['status'] == 'present' ? "Present" : "Absent"; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['marked_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">No attendance records found.</p>
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
