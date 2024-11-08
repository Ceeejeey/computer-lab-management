<?php
session_start();
include '../../config/config.php';

// Check if the lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit;
}

$lecturer_id = $_SESSION['lecturer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family:'poppins', Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 600px;
            margin-top: 40px;
        }
        .form-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Report an Issue</h2>
    <div class="form-card">
        <form action="../../controllers/process_issue_report.php" method="POST">
            <div class="mb-3">
                <label for="issueType" class="form-label">Issue Type</label>
                <select class="form-select" id="issueType" name="issue_type" required>
                    <option selected disabled>Select issue type</option>
                    <option value="hardware">Hardware</option>
                    <option value="software">Software</option>
                    <option value="network">Network</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="computerID" class="form-label">Computer ID</label>
                <input type="text" class="form-control" id="computerID" name="computer_id" placeholder="e.g., PC-12" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Issue Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the issue in detail" required></textarea>
            </div>
            <div class="mb-3">
                <label for="priority" class="form-label">Priority Level</label>
                <select class="form-select" id="priority" name="priority" required>
                    <option selected disabled>Select priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Report</button>
        </form>
        <a href="../dashboards/lecturer_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
