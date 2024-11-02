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
        <a href="view_lab_schedule.php">View Lab Schedule</a>
        <a href="report_issue.php">Report Issue</a>
        <a href="view_attendance_report.php">View Attendance Report</a>
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

        <!-- Dashboard Content -->
        <div class="container mt-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Add Students</h5>
                            <p class="card-text">Add students to the system with a default password.</p>
                            <a href="add_student.php" class="btn btn-primary">Add Students</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">View Lab Schedule</h5>
                            <p class="card-text">View upcoming lab sessions and exams.</p>
                            <a href="view_lab_schedule.php" class="btn btn-primary">View Schedule</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Report Issue</h5>
                            <p class="card-text">Report issues with lab resources.</p>
                            <a href="report_issue.php" class="btn btn-primary">Report Issue</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">View Attendance Report</h5>
                            <p class="card-text">Monitor attendance records for lab sessions.</p>
                            <a href="view_attendance_report.php" class="btn btn-primary">View Attendance</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Monitor User Sessions</h5>
                            <p class="card-text">Track active user sessions in labs.</p>
                            <a href="monitor_sessions.php" class="btn btn-primary">Monitor Sessions</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">View Lab Status</h5>
                            <p class="card-text">Check the current status of lab equipment and availability.</p>
                            <a href="view_lab_status.php" class="btn btn-primary">View Status</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Respond to Complaints</h5>
                            <p class="card-text">Review and respond to complaints from students.</p>
                            <a href="respond_complaints.php" class="btn btn-primary">Respond</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Schedule Lab Sessions</h5>
                            <p class="card-text">Schedule new lab sessions and exams.</p>
                            <a href="schedule_lab_sessions.php" class="btn btn-primary">Schedule Sessions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
