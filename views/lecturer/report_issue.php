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
    font-family: 'Poppins', Arial, sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 0;
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

h2 {
    font-weight: bold;
    color: #4a4a4a;
}

.form-select, .form-control {
    border-radius: 6px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.btn-primary {
    background-color: #007bff;
    border: none;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.toast {
    max-width: 350px;
    width: 100%;
    border-radius: 8px;
}

.toast-body {
    font-size: 1rem;
    padding: 10px;
}

.toast-header {
    font-weight: bold;
}

.position-fixed {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
}

    </style>
</head>
<body>
<?php
if (isset($_SESSION['toast'])) {
    $toast = $_SESSION['toast'];
    $toastType = $toast['type'] === 'success' ? 'bg-success text-white' : 'bg-danger text-white';
    echo <<<HTML
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div class="toast $toastType" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {$toast['message']}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastElement = document.querySelector('.toast');
        var toast = new bootstrap.Toast(toastElement);
        toast.show();
    });
</script>
HTML;
    unset($_SESSION['toast']);
}
?>

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
                <input type="text" class="form-control" id="computerID" name="computer_id" placeholder="e.g. 12" required>
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
