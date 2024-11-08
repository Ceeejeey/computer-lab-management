<?php
session_start();
include '../../config/config.php';

date_default_timezone_set('Asia/Colombo');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['student_batch']) || !isset($_SESSION['student_id'])) {
    die("Student batch or ID is not set in the session.");
}

$student_id = $_SESSION['student_id'];
$student_batch = $_SESSION['student_batch'];
echo "<script>console.log('Student batch: " . addslashes($student_batch) . "');</script>";
$studentName = '';

// Query to get student name
$stmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($studentName);
$stmt->fetch();
$stmt->close();
$today = date("Y-m-d");
$current_time = date("Y-m-d H:i:s");

echo "<script>console.log('Current time: " . addslashes($current_time) . "');</script>";

$schedules = [];

$start_of_day = $today . ' 00:00:00';
$end_of_day = $today . ' 23:59:59';

$stmt = $conn->prepare("SELECT id, batch, topic, start_time, end_time FROM lab_schedule WHERE start_time BETWEEN ? AND ? AND batch = ?");
$stmt->bind_param("sss", $start_of_day, $end_of_day, $student_batch);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result === false) {
        die("Database query failed: " . $stmt->error);
    }

    while ($row = $result->fetch_assoc()) {
        $startTime = new DateTime($row['start_time']);
        // Removed redundant assignment
        $endTime = new DateTime($row['end_time']);
        $currentTime = new DateTime($current_time);

        if ($currentTime < $startTime) {
            $row['status'] = 'Not Yet Started';
            $row['can_attend'] = false;
        } elseif ($currentTime > $endTime) {
            $row['status'] = 'Time Passed';
        } else {
            $row['status'] = 'Attend';
            $row['can_attend'] = true;
        }

        $schedules[] = $row;
    }
} else {
    die("Failed to execute statement: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
        }

        .sidebar h2 {
            text-align: center;
            font-weight: bold;
            color: #ffffff;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: white;
            border-radius: 5px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }

        .profile-dropdown {
            position: relative;
        }

        .dropdown-menu {
            right: 0;
            left: auto;
        }

        .schedule-heading {
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Student Dashboard</h2>
        <a href="../student/view_attendance.php">View Attendance</a>
        <a href="view_lab_schedule.php">View Lab Schedule</a>
        <a href="../student/report_issue.php">Report Issue</a>
        <a href="check_complaints.php">Check Complaints</a>
    </div>

    <div class="main-content">
        <div class="navbar">
            <div>
                <h4>Welcome, <?php echo htmlspecialchars($studentName); ?></h4>
            </div>
            <div class="profile-dropdown dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Profile
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.php">Go to Profile</a></li>
                    <li><a class="dropdown-item" href="../../controllers/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>

        <div class="container mt-4">
            <div class="schedule-heading">
                <i class="fas fa-calendar-alt"></i> Today's Lab Schedule
            </div>
            <div class="row g-4">
                <?php if (empty($schedules)): ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-warning" role="alert">
                            No lab schedules available for your batch today.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <?php $canAttend = isset($schedule['can_attend']) ? true : false; ?>
                        <div class="col-lg-6">
                            <div class="card shadow-sm <?php echo $canAttend ? 'bg-primary text-white' : 'bg-secondary text-white'; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($schedule['topic']); ?></h5>
                                    <p class="card-text"><strong>Batch:</strong> <?php echo htmlspecialchars($schedule['batch']); ?></p>
                                    <p class="card-text"><strong>Time:</strong> <?php echo date("g:i A", strtotime($schedule['start_time'])); ?> - <?php echo date("g:i A", strtotime($schedule['end_time'])); ?></p>
                                    <button class="btn btn-light <?php echo $canAttend ? '' : 'disabled'; ?>"
                                        <?php if ($canAttend): ?>
                                        onclick="markAttendance(<?php echo $schedule['id']; ?>)"
                                        <?php else: ?>
                                        disabled
                                        <?php endif; ?>>
                                        <?php echo $canAttend ? 'Attend' : 'Cannot Attend'; ?>
                                    </button>



                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markAttendance(sessionId) {
            if (confirm("Are you sure you want to mark attendance for this session?")) {
                fetch('../../controllers/student_mark_attendance.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `session_id=${sessionId}&student_id=<?php echo $student_id; ?>`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data); // Display the response (success or error message)
                        document.querySelector(`button[onclick="markAttendance(${sessionId})"]`).disabled = true;
                        document.querySelector(`button[onclick="markAttendance(${sessionId})"]`).textContent = 'Marked';
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>

</html>