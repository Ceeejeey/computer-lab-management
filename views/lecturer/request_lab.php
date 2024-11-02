<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Lab Session</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container {
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        @media (max-width: 576px) {
            .container {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Request Lab Session</h2>
        <form action="../../controllers/process_lab_request.php" method="POST">
            <div class="mb-3">
                <label for="batch" class="form-label">Batch</label>
                <select name="batch" id="batch" class="form-select" required>
                    <option value="" disabled selected>Select your batch</option>
                    <option value="18/19">18/19</option>
                    <option value="19/20">19/20</option>
                    <option value="20/21">20/21</option>
                    <option value="21/22">21/22</option>
                    <option value="22/23">22/23</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="topic" class="form-label">Lecture Topic</label>
                <input type="text" name="topic" id="topic" class="form-control" placeholder="Enter lecture topic" required>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
        </form>
        <a href="../dashboards/lecturer_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
