<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
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
        <a href="../lecturer/add_students.php">Add Students</a>
        <a href="../lecturer/request_lab.php">Schedule Lab Sessions</a>
        <a href="../lecturer/lab_schedule.php">View Lab Schedule</a>
        <a href="report_issue.php">Report Issue</a>
        <a href="../lecturer/view_attendance.php">View Attendance Report</a>
        <a href="monitor_sessions.php">Monitor User Sessions</a>
        <a href="view_lab_status.php">View Lab Status</a>
        <a href="respond_complaints.php">Respond to Complaints</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="navbar">
            <div>
                <h4>Welcome, [Lecturer Name]</h4>
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
        $lab_query = "SELECT * FROM lab_schedule WHERE start_time > NOW() ORDER BY start_time LIMIT 1";
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
                        <strong>Next Scheduled Session:</strong> <?php echo date("d M Y, H:i", strtotime($lab_schedule['start_time'])); ?><br>
                        <strong>Topic:</strong> <?php echo $lab_schedule['lecture_topic']; ?><br>
                        <strong>Batch:</strong> <?php echo $lab_schedule['batch']; ?>
                    </p>
                    <span class="badge bg-danger">Status: Scheduled</span>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>