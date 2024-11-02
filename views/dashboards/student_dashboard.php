<?php
session_start(); 
include '../../config/config.php'; 

date_default_timezone_set('Asia/Colombo');

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['student_batch'])) {
    die("Student batch is not set in the session.");
}

$student_batch = $_SESSION['student_batch']; 
echo "<script>console.log('Student batch: " . addslashes($student_batch) . "');</script>";

$today = date("Y-m-d");
$current_time = date("Y-m-d H:i:s"); 

echo "<script>console.log('Current time: " . addslashes($current_time) . "');</script>";


$schedules = [];

$start_of_day = $today . ' 00:00:00';
$end_of_day = $today . ' 23:59:59';

$stmt = $conn->prepare("SELECT batch, topic, start_time, end_time FROM lab_schedule WHERE start_time BETWEEN ? AND ? AND batch = ?");
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

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
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

        /* Heading Styles */
        .schedule-heading {
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
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
        <h2>Student Dashboard</h2>
        <a href="view_attendance.php">View Attendance</a>
        <a href="input_attendance.php">Input Attendance</a>
        <a href="check_complaints.php">Check Complaints</a>
        <a href="view_lab_schedule.php">View Lab Schedule</a>
        <a href="report_issue.php">Report Issue</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <div class="navbar">
            <div>
                <h4>Welcome, [Student Name]</h4>
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

        <!-- Dashboard Content -->
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
                <!-- Display the schedules -->
                <?php foreach ($schedules as $schedule): ?>
                    <div class="col-lg-6">
                        <div class="card shadow-sm <?php echo ($schedule['can_attend'] ? 'bg-success text-white' : 'bg-secondary text-white'); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $schedule['topic']; ?></h5>
                                <p class="card-text"><strong>Batch:</strong> <?php echo $schedule['batch']; ?></p>
                                <p class="card-text"><strong>Time:</strong> <?php echo date("g:i A", strtotime($schedule['start_time'])); ?> - <?php echo date("g:i A", strtotime($schedule['end_time'])); ?></p>
                                <button class="btn btn-light <?php echo ($schedule['can_attend'] ? '' : 'disabled'); ?>">
                                    <?php echo $schedule['status']; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>