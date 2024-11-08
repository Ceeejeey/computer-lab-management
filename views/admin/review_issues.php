<?php
session_start();
include '../../config/config.php';

// Variable to store notification message
$notificationMessage = "";

// Fetch all issues marked as 'new' or 'ongoing'
$sql = "SELECT i.issue_id, i.issue_type, i.computer_id, i.description, i.priority,  
        oi.status, oi.action_taken, oi.reviewed_at, oi.resolved_at 
        FROM issues i 
        LEFT JOIN ongoing_issues oi ON i.issue_id = oi.issue_id
        WHERE oi.status IS NULL OR oi.status = 'ongoing'";
$result = $conn->query($sql);

// Handle status update and actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];
    $action_taken = $_POST['action_taken'];
    $status = $_POST['status'];

    if ($status === 'ongoing') {
        // Insert or update status to 'ongoing' in ongoing_issues table
        $sql = "INSERT INTO ongoing_issues (issue_id, status, action_taken, reviewed_at) 
                VALUES (?, 'ongoing', ?, NOW()) ";
               
        $notificationMessage = "Issue marked as ongoing.";
    } else {
        // Update status to 'resolved' in ongoing_issues table
        $sql = "UPDATE ongoing_issues SET status = 'resolved', resolved_at = NOW(), action_taken = ? 
                WHERE issue_id = ?";
        $notificationMessage = "Issue marked as resolved.";
    }

    $stmt = $conn->prepare($sql);
    if ($status === 'ongoing') {
        $stmt->bind_param("is", $issue_id, $action_taken);
    } else {
        $stmt->bind_param("si", $action_taken, $issue_id);
    }
    $stmt->execute();
    $stmt->close();

    // Update status in issues table
    $sql = "UPDATE issues SET status = ? WHERE issue_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $issue_id);
    $stmt->execute();
    $stmt->close();

    // Set session variable for notification
    $_SESSION['notification'] = $notificationMessage;
    header("Location: review_issues.php");
    exit;
}

// Retrieve and clear notification message from session
if (isset($_SESSION['notification'])) {
    $notificationMessage = $_SESSION['notification'];
    unset($_SESSION['notification']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Issues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: "poppins" ,Arial, sans-serif; }
        .main-container { margin-top: 30px; max-width: 1200px; padding: 20px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .issue-card { border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; background-color: #ffffff; transition: transform 0.3s, box-shadow 0.3s; }
        .issue-card:hover { transform: scale(1.02); box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15); }
        .status-badge { font-size: 0.85rem; }
        .resolved { color: #28a745; }
        .ongoing { color: #ffbb33; }
        .priority-high { color: #dc3545; }
        .priority-medium { color: #ffc107; }
        .priority-low { color: #28a745; }
        .card-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .card-column { flex: 0 0 48%; }
        #toastNotification { position: fixed; top: 20px; right: 20px; z-index: 9999; }
    </style>
</head>
<body>

<div class="container main-container">
    <h2 class="text-center mb-4"><i class="bi bi-wrench-adjustable-circle"></i> Issue Review</h2>

    <!-- Display Notification if Available -->
    <?php if ($notificationMessage): ?>
        <div class="toast align-items-center text-bg-primary border-0" role="alert" id="toastNotification" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo htmlspecialchars($notificationMessage); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Display No Issues Message if Result is Empty -->
    <?php if ($result->num_rows == 0): ?>
        <p class="text-center text-muted">No issues to review.</p>
    <?php else: ?>
        <div class="card-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="issue-card card-column">
                    <h5><i class="bi bi-exclamation-circle-fill"></i> Issue ID: <?php echo htmlspecialchars($row['issue_id']); ?></h5>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['issue_type']); ?></p>
                    <p><strong>Computer ID:</strong> <?php echo htmlspecialchars($row['computer_id']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Priority:</strong> 
                        <span class="<?php echo 'priority-' . strtolower($row['priority']); ?>">
                            <?php echo htmlspecialchars(ucfirst($row['priority'])); ?>
                        </span>
                    </p>
                    <p><strong>Status:</strong> 
                        <span class="badge <?php echo $row['status'] === 'resolved' ? 'resolved' : 'ongoing'; ?>">
                            <?php echo ucfirst($row['status'] ?: 'New'); ?>
                        </span>
                    </p>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="action_taken_<?php echo $row['issue_id']; ?>" class="form-label">Action Taken:</label>
                            <textarea name="action_taken" class="form-control" id="action_taken_<?php echo $row['issue_id']; ?>" rows="2"><?php echo htmlspecialchars($row['action_taken']); ?></textarea>
                        </div>
                        <input type="hidden" name="issue_id" value="<?php echo $row['issue_id']; ?>">
                        <div class="d-flex justify-content-between">
                            <button type="submit" name="status" value="ongoing" class="btn btn-warning"><i class="bi bi-hourglass-split"></i> Mark as Ongoing</button>
                            <button type="submit" name="status" value="resolved" class="btn btn-success"><i class="bi bi-check-circle-fill"></i> Mark as Resolved</button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
    <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show Toast Notification
    const toast = document.getElementById('toastNotification');
    if (toast) {
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast);
        toastBootstrap.show();
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
