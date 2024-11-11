<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 500px;
            margin-top: 80px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            font-weight: bold;
        }
        .btn-dashboard {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #3b82f6;
            color: #ffffff;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .btn-dashboard:hover {
            background-color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Toast Notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="passwordToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    <!-- Message will be updated here -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="container">
    <a href="../dashboards/student_dashboard.php" class="btn-dashboard">Go to Dashboard</a>
        <h2>Change Password</h2><br>
        <form id="changePasswordForm" action="../../controllers/student_process_change_password.php" method="POST">
            <div class="mb-3">
                <label for="currentPassword" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="newPassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Change Password</button>
            <p id="errorMessage" class="text-danger mt-3 text-center" style="display: none;">Passwords do not match!</p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("changePasswordForm").addEventListener("submit", function (e) {
            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                document.getElementById("errorMessage").style.display = "block";
            }
        });

        // Display toast message if there's feedback from the server
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            const toastElement = document.getElementById('passwordToast');
            const toastMessage = document.getElementById('toastMessage');

            if (message) {
                toastMessage.innerText = message;
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });
    </script>
</body>
</html>
