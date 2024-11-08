<?php
session_start();
include '../../config/config.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch the admin's name from the database
$adminId = $_SESSION['admin_id']; // Assuming admin's ID is stored in session
$adminName = '';

// Query to get admin name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ? AND role = 'admin'");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$stmt->bind_result($adminName);
$stmt->fetch();
$stmt->close();

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <style>
        /* Custom Styles */
        body {
            font-family: 'poppins', Arial, sans-serif;
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

        /* Card Styles */
        .card {
            transition: transform 0.3s;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* Chart Section Styles */
        .chart-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        /* Wrapper Container Styles */
        .wrapper-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Styling for the chart */
        #attendanceChart {
            height: 400px;
            width: 100%;
        }

        /* Select box styles */
        #lecturerSelect {
            width: 100%;
            max-width: 400px;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2em;
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
        <h2>Admin Dashboard</h2>
        <a href="../admin/view_lab_request.php"><i class="fas fa-check-circle"></i> Approve Lab Requests</a>
        <a href="../admin/view_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a>
        <a href="../admin/view_lectureres.php"><i class="fas fa-chalkboard-teacher"></i> Manage Lecturers</a>
        <a href="../admin/view_attendance_report.php"><i class="fas fa-file-alt"></i> View Attendance Report</a>
        <a href="../admin/schedule_maintain.php"><i class="fas fa-calendar-alt"></i> Schedule Maintenance</a>
        <a href="../admin/review_issues.php"><i class="fas fa-comment-dots"></i> Feedback for Complaints</a>
    </div>


    <!-- Main Content -->
    <div class="main-content">

        <!-- Navbar -->
        <div class="navbar">
            <div>
                <h4>Welcome, <?php echo htmlspecialchars($adminName); ?></h4>
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

        <!-- Wrapper Container for Lecturer Selection and Attendance Chart -->
        <div class="wrapper-container mt-4">
            <h5>Attendance Chart by Lecturer</h5>
            <label for="lecturerSelect" class="form-label">Select Lecturer:</label>
            <select id="lecturerSelect" class="form-select" aria-label="Select Lecturer">
                <option value="">Choose a lecturer</option>
                <!-- Populate with lecturer options dynamically -->
            </select>
            <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to populate lecturers dropdown
            fetch('../admin/get_lecturers.php') // Adjust path to your lecturers fetching endpoint
                .then(response => response.json())
                .then(lecturers => {
                    const lecturerSelect = document.getElementById("lecturerSelect");
                    lecturers.forEach(lecturer => {
                        const option = document.createElement("option");
                        option.value = lecturer.id;
                        option.text = lecturer.name;
                        lecturerSelect.add(option);
                    });
                });

            const attendanceChart = new Chart(document.getElementById("attendanceChart").getContext("2d"), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Attendance Count',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Fetch and display attendance data based on selected lecturer
            document.getElementById("lecturerSelect").addEventListener("change", function() {
                const lecturerId = this.value;
                if (!lecturerId) return; // No lecturer selected

                fetch(`../../controllers/admin_fetch_attendance_by_lecturer.php?lecturer_id=${lecturerId}`)
                    .then(response => response.json())
                    .then(data => {
                        const sessionIds = data.map(item => item.session_id);
                        const attendanceCounts = data.map(item => item.attendance_count);

                        // Update chart data
                        attendanceChart.data.labels = sessionIds;
                        attendanceChart.data.datasets[0].data = attendanceCounts;
                        attendanceChart.update();
                    })
                    .catch(error => console.error('Error fetching attendance data:', error));
            });
        });
    </script>
</body>

</html>