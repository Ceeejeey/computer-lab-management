<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
        <h2>Student Dashboard</h2>
        <a href="view_attendance.php">View Attendance</a>
        <a href="input_attendance.php">Input Attendance</a>
        <a href="check_complaints.php">Check Complaints</a>
        <a href="view_lab_schedule.php">View Lab Schedule</a>
        <a href="report_issue.php">Report Issue</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
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
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="container mt-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">View Attendance</h5>
                            <p class="card-text">Check your attendance records for each lab session.</p>
                            <a href="view_attendance.php" class="btn btn-primary">View Attendance</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Input Attendance</h5>
                            <p class="card-text">Mark your attendance for today’s lab session.</p>
                            <a href="input_attendance.php" class="btn btn-primary">Input Attendance</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Check Complaints</h5>
                            <p class="card-text">View any complaints you’ve raised regarding lab issues.</p>
                            <a href="check_complaints.php" class="btn btn-primary">Check Complaints</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">View Lab Schedule</h5>
                            <p class="card-text">See upcoming lab sessions and exams scheduled.</p>
                            <a href="view_lab_schedule.php" class="btn btn-primary">View Lab Schedule</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Report Issue</h5>
                            <p class="card-text">Submit any issues with lab resources for timely resolution.</p>
                            <a href="report_issue.php" class="btn btn-primary">Report Issue</a>
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
