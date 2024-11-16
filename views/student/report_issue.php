<?php
session_start();
include '../../config/config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

// Capture toast message if exists
$toast = isset($_SESSION['toast']) ? $_SESSION['toast'] : null;
// Clear toast after capturing it
unset($_SESSION['toast']);
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

.wrapper {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.form-container {
    max-width: 700px;
    width: 100%;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-top: 20px;
}

.form-header {
    text-align: center;
    margin-bottom: 20px;
}

.form-header h2 {
    font-weight: bold;
    color: #4a4a4a;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.form-select, .form-control {
    border-radius: 6px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.btn-dashboard {
    margin-bottom: 20px;
    background-color: #3b82f6;
    color: #fff;
    border-radius: 20px;
    padding: 10px 20px;
    font-weight: bold;
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
<div class="wrapper">
    <div class="form-container">
        <a href="../dashboards/student_dashboard.php" class="btn-dashboard">Go to Dashboard</a>
        <div class="form-header">
            <h2>Report an Issue</h2>
            <p class="text-muted">Fill out the form below to report an issue.</p>
        </div>
        <form action="../../controllers/process_issue_report.php" method="POST">
            <div class="mb-4">
                <label for="issueType" class="form-label">Issue Type</label>
                <select class="form-select" id="issueType" name="issue_type" required>
                    <option selected disabled>Select issue type</option>
                    <option value="hardware">Hardware</option>
                    <option value="software">Software</option>
                    <option value="network">Network</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="computerID" class="form-label">Computer ID</label>
                <input type="text" class="form-control" id="computerID" name="computer_id" placeholder="e.g. 12" required>
            </div>
            <div class="mb-4">
                <label for="description" class="form-label">Issue Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="mb-4">
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
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <?php if ($toast): ?>
        <div class="toast align-items-center text-bg-<?= $toast['type'] === 'success' ? 'success' : 'danger' ?>" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <?= htmlspecialchars($toast['message']); ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toastElement = document.querySelector('.toast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    });
</script>
</body>
</html>
