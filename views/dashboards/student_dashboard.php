<?php
session_start();
include '../../config/config.php';

date_default_timezone_set('Asia/Colombo');

// For development, you can keep error reporting, but disable in production
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

// Debug output
echo "<script>console.log('Student Name: " . addslashes($studentName) . "');</script>";
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
        $endTime = new DateTime($row['end_time']);
        $currentTime = new DateTime($current_time);

        // Assigning status and can_attend
        if ($currentTime < $startTime) {
            $row['status'] = 'Not Yet Started';
            $row['can_attend'] = false;
        } elseif ($currentTime > $endTime) {
            $row['status'] = 'Time Passed';
            $row['can_attend'] = false;
        } else {
            $row['status'] = 'Attend';
            $row['can_attend'] = true;
        }

        // Add the row to the schedules array
        $schedules[] = $row;

        // Log the details for debugging
        echo "<script>console.log('Current Time: " . addslashes($current_time) . "');</script>";
        echo "<script>console.log('Start Time: " . addslashes($row['start_time']) . "');</script>";
        echo "<script>console.log('Can Attend: " . ($row['can_attend'] ? 'Yes' : 'No') . "');</script>";
    }
} else {
    die("Failed to execute statement: " . $stmt->error);
}

$notifications = [];
$notificationCount = 0;
$hasNotifications = false;

// Query to fetch unread notifications
$notificationQuery = "SELECT title, message, created_at, is_read 
                      FROM student_notifications 
                      WHERE student_id = ? AND is_read = 0
                      ORDER BY created_at DESC";

$stmt = $conn->prepare($notificationQuery);
$stmt->bind_param("i", $student_id); // Use $student_id instead of $studentId
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Update notification count and flag
if (!empty($notifications)) {
    $notificationCount = count($notifications);
    $hasNotifications = true;
}

// To hide the notification badge if no new notifications
$hasNotifications = $notificationCount > 0;

$stmt->close(); // Close statement after all queries
$conn->close(); // Close connection
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
            font-family: 'poppins', Arial, sans-serif;
            background-color: #f4f6f9;
            background-image: url('../../images/new1.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
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
            font-weight: 500;
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

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2em;
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
        <a href="../student/view_attendance.php"><i class="fas fa-calendar-check"></i> View Attendance</a>
        <a href="../student/report_issue.php"><i class="fas fa-exclamation-triangle"></i> Report Issue</a>
        <a href="../student/check_complaints.php"><i class="fas fa-question-circle"></i> Check Issue Status</a>
    </div>


    <div class="main-content">
        <div class="navbar">
            <div>
                <h4>Welcome, <?php echo htmlspecialchars($studentName); ?></h4>
            </div>
            <!-- Student Notification Bell -->
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <button
                        class="btn btn-outline-secondary position-relative"
                        type="button"
                        id="studentNotificationBell"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        onclick="markStudentNotificationsAsRead()">
                        <i class="fas fa-bell"></i>
                        <!-- Display the badge for new notifications -->
                        <?php if ($hasNotifications): ?>
                            <span
                                id="studentNotificationBadge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $notificationCount; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="studentNotificationBell">
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <li class="dropdown-item">
                                    <strong><?php echo htmlspecialchars($notification['title']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($notification['message']); ?></small><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($notification['created_at']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="dropdown-item text-center text-muted">No Notifications</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="profile-dropdown dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Profile
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="../student/change_password.php">Change Password</a></li>
                        <li><a class="dropdown-item" href="../../controllers/logout.php">Logout</a></li>
                    </ul>
                </div>
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
                        <?php $canAttend = isset($schedule['can_attend']) ? $schedule['can_attend'] : false; ?>
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
    <script>
        function markStudentNotificationsAsRead() {
            fetch('../../controllers/student_mark_notifications_as_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide the badge
                        const badge = document.getElementById('studentNotificationBadge');
                        if (badge) {
                            badge.style.display = 'none';
                        }
                    } else {
                        console.error('Failed to mark notifications as read.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>