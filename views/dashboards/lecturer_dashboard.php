<?php
session_start();
include '../../config/config.php';

// Check if the lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: ../../login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch the lecturer's name from the database
$lecturerId = $_SESSION['lecturer_id']; // Assuming lecturer's ID is stored in session
$lecturerName = '';

// Query to get lecturer name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ? AND role = 'lecturer'");
$stmt->bind_param("i", $lecturerId);
$stmt->execute();
$stmt->bind_result($lecturerName);
$stmt->fetch();
$stmt->close();
$notifications = [];
$notificationCount = 0;
$hasNotifications = false;



$notificationQuery = "SELECT title, message, created_at, is_read 
                      FROM lecturer_notifications 
                      WHERE lecturer_id = ? AND is_read = 0
                      ORDER BY created_at DESC";

$stmt = $conn->prepare($notificationQuery);
$stmt->bind_param("i", $lecturerId);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
// Check if there are any unread notifications
if (!empty($notifications)) {
    $hasNotifications = true;
    $notificationCount = count($notifications); // Count the unread notifications
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Custom Styles */
        body {
            font-family: 'poppins', Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }

        /* Sidebar Styles */
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

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2em;
        }


        .sidebar a:hover {
            background-color: #495057;
            color: white;
            border-radius: 5px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        /* Navbar Styles */
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

        .card {
            margin-top: 20px;
            border-radius: 8px;
        }

        .card h5 {
            font-weight: bold;
        }

        .icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        /* Chart Container */
        .chart-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-height: 500px;
            /* Set a maximum height */
            overflow-y: auto;
            /* Add vertical scroll if content exceeds max height */
        }

        /* Chart Styling */
        #attendanceChart {
            max-width: 100%;
            height: 400px;
        }

        .dropdown-menu {
            max-width: 300px;
            max-height: 400px;
            overflow-y: auto;
        }

        .dropdown-menu .dropdown-item {
            white-space: normal;
            padding: 10px 15px;
        }

        .dropdown-menu .dropdown-item strong {
            font-weight: 600;
        }


        /* Responsive */
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

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Lecturer Dashboard</h2>
        <a href="../lecturer/add_students.php"><i class="fas fa-user-plus"></i> Add Students</a>
        <a href="../lecturer/modify_students.php"><i class="fas fa-users"></i> Student Management</a>
        <a href="../lecturer/request_lab.php"><i class="fas fa-calendar-plus"></i> Schedule Lab Sessions</a>
        <a href="../lecturer/lab_schedule.php"><i class="fas fa-calendar-alt"></i> View Lab Schedule</a>
        <a href="../lecturer/view_attendance.php"><i class="fas fa-file-alt"></i> View Attendance Report</a>
        <a href="../lecturer/report_issue.php"><i class="fas fa-exclamation-circle"></i> Report Issue</a>
        <a href="../lecturer/check_complaints.php"><i class="fas fa-question-circle"></i> Check Issue Status</a>

    </div>


    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="navbar">
            <div>
                <h4>Welcome, <?php echo htmlspecialchars($lecturerName); ?></h4>
            </div>
            <div class="d-flex align-items-center">
                <!-- Notification Bell -->
                <div class="dropdown me-3">
                    <button
                        class="btn btn-outline-secondary position-relative"
                        type="button"
                        id="notificationBell"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        onclick="markNotificationsAsRead()">
                        <i class="fas fa-bell"></i>
                        <!-- Display the badge for new notifications -->
                        <?php if ($hasNotifications): ?>
                            <span
                                id="notificationBadge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $notificationCount; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationBell">
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <li class="dropdown-item">
                                    <strong><?php echo htmlspecialchars($notification['title']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($notification['message']); ?></small><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($notification['date']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="dropdown-item text-center text-muted">No Notifications</li>
                        <?php endif; ?>
                    </ul>
                </div>


                <!-- Profile Dropdown -->
                <div class="profile-dropdown dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Profile
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="../../includes/change_password.php">Change Password</a></li>
                        <li><a class="dropdown-item" href="../../controllers/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Maintenance Notification Card -->
        <?php
        // Database connection
        include_once '../../config/config.php';

        // Query to check for any upcoming maintenance
        $maintenance_query = "SELECT * FROM maintenance WHERE status = 'Scheduled' ORDER BY start_time LIMIT 1";
        $maintenance_result = mysqli_query($conn, $maintenance_query);

        if (mysqli_num_rows($maintenance_result) > 0) {
            $maintenance = mysqli_fetch_assoc($maintenance_result);
        ?>
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning"><i class="icon bi bi-tools"></i>Maintenance Scheduled</h5>
                    <p class="card-text">
                        <strong>Description:</strong> <?php echo $maintenance['description']; ?><br>
                        <strong>Start Time:</strong> <?php echo date("d M Y, H:i", strtotime($maintenance['start_time'])); ?><br>
                        <strong>End Time:</strong> <?php echo date("d M Y, H:i", strtotime($maintenance['end_time'])); ?>
                    </p>
                    <span class="badge bg-warning text-dark">Status: <?php echo $maintenance['status']; ?></span>
                </div>
            </div>
        <?php
        }
        ?>
        <!-- Lab Availability Card -->
        <?php
        // Query to check if labs are available
        $lab_query = "SELECT * 
                FROM lab_schedule 
                WHERE NOW() BETWEEN start_time AND end_time 
                ORDER BY start_time 
                LIMIT 1;
                ";
        $lab_result = mysqli_query($conn, $lab_query);

        if (mysqli_num_rows($lab_result) == 0) {
        ?>
            <div class="card shadow-sm border-success mt-4">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="icon bi bi-check-circle"></i>Lab Available</h5>
                    <p class="card-text">Lab is available for scheduling.</p>
                </div>
            </div>
        <?php
        } else {
            $lab_schedule = mysqli_fetch_assoc($lab_result);
        ?>
            <div class="card shadow-sm border-danger mt-4">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="icon bi bi-exclamation-circle"></i>Lab Unavailable</h5>
                    <p class="card-text">
                        <strong>Session Start time:</strong> <?php echo date("d M Y, H:i", strtotime($lab_schedule['start_time'])); ?><br>
                        <strong>End Time:</strong> <?php echo date("d M Y, H:i", strtotime($lab_schedule['end_time'])); ?><br>
                        <strong>Session Name:</strong> <?php echo $lab_schedule['topic']; ?><br>
                        <strong>Batch:</strong> <?php echo $lab_schedule['batch']; ?>
                    </p>
                    <span class="badge bg-danger">Status: Scheduled</span>
                </div>
            </div>
        <?php
        }
        ?>
        <!-- Attendance Chart -->
        <div class="chart-container">
            <h3>Attendance Count by Session</h3>
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('../../controllers/process_get_attendance_data.php')
                .then(response => response.json())
                .then(data => {
                    const sessionIds = data.map(item => item.session_id);
                    const attendanceCounts = data.map(item => item.attendance_count);

                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: sessionIds,
                            datasets: [{
                                label: 'Attendance Count',
                                data: attendanceCounts,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Attendance Count'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Session ID'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                })
                .catch(error => console.error('Error fetching attendance data:', error));
        });
    </script>
    <script>
        function markNotificationsAsRead() {
            // Fetch unread notifications count and update the server
            fetch('../../controllers/lecturer_mark_notifications_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide the badge
                        const badge = document.getElementById('notificationBadge');
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