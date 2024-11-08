<?php
session_start();
include '../../config/config.php';

// Fetch lab requests from the database
$sql = "SELECT id, batch, topic, start_time, end_time, status FROM lab_requests WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Lab Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .wrapper {
            margin-top: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .table {
            margin-top: 20px;
        }
        .table thead th {
            background-color: #000;
            color: #ffffff;
            font-weight: 400;
            
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-approve, .btn-reject {
            color: white;
            transition: transform 0.2s;
        }
        .btn-approve {
            background-color: #28a745;
        }
        .btn-approve:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        .btn-reject {
            background-color: #dc3545;
        }
        .btn-reject:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
        /* Smooth row removal */
        .fade-out {
            animation: fadeOut 0.5s forwards;
        }
        @keyframes fadeOut {
            from { opacity: 1; height: auto; }
            to { opacity: 0; height: 0; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

<div class="container wrapper">
    <h2 class="mb-4 text-center">Review Lab Requests</h2>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastNotification" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <!-- Notification message will be inserted here by JavaScript -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Batch</th>
                <th>Lecture Topic</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="labRequestsTable">
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr id="request-<?php echo $row['id']; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['batch']; ?></td>
                        <td><?php echo $row['topic']; ?></td>
                        <td><?php echo $row['start_time']; ?></td>
                        <td><?php echo $row['end_time']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <button class="btn btn-approve" onclick="processRequest(<?php echo $row['id']; ?>, 'approve')">Approve</button>
                            <button class="btn btn-reject" onclick="processRequest(<?php echo $row['id']; ?>, 'reject')">Reject</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center">No lab requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Function to handle request processing
function processRequest(requestId, action) {
    fetch('../../controllers/process_admin_view_lab_request.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `request_id=${requestId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`${action.charAt(0).toUpperCase() + action.slice(1)}d successfully.`);
            // Smoothly remove the row after approving or rejecting
            const row = document.getElementById(`request-${requestId}`);
            row.classList.add('fade-out');
            setTimeout(() => row.remove(), 500);
        } else {
            showToast('Failed to process the request. Try again.', true);
        }
    })
    .catch(error => showToast('An error occurred. Please try again.', true));
}

// Function to display toast notifications
function showToast(message, isError = false) {
    const toastEl = document.getElementById('toastNotification');
    const toastBody = toastEl.querySelector('.toast-body');
    toastBody.textContent = message;
    toastEl.classList.remove('bg-success', 'bg-danger');
    toastEl.classList.add(isError ? 'bg-danger' : 'bg-success');

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
</script>

</body>
</html>
