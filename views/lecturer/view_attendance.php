<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Attendance Report</h2>
        <form method="GET" class="mb-4">
            <label for="batch">Batch:</label>
            <input type="text" name="batch" id="batch">
            <label for="date_from">From:</label>
            <input type="date" name="date_from" id="date_from">
            <label for="date_to">To:</label>
            <input type="date" name="date_to" id="date_to">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Session Topic</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceData as $data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($data['name']); ?></td>
                        <td><?php echo htmlspecialchars($data['topic']); ?></td>
                        <td><?php echo htmlspecialchars($data['attendance_date']); ?></td>
                        <td><?php echo htmlspecialchars($data['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
