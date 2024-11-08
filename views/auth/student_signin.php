<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign In - Computer Laboratory Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #eef2f3, #dfe7fd);
            font-family: 'poppins',Arial, sans-serif;
        }

        .signin-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .signin-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .signin-header h1 {
            font-size: 1.8rem;
            color: #333;
        }

        .signin-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-label {
            color: #555;
            font-weight: 500;
        }

        .footer-text {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #666;
            text-align: center;
        }

        .footer-text a {
            color: #007bff;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signin-container">
        <div class="signin-header">
            <h1>Student Sign In</h1>
            <p>Sign in to access your dashboard, view attendance, report issues, and more.</p>
        </div>

        <!-- Sign In Form -->
        <form action="../../controllers/process_student_signin.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        
    </div>

     <!-- Toast Notification -->
     <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="invalidPasswordToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Invalid Password
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['showInvalidPasswordToast']) && $_SESSION['showInvalidPasswordToast'] === true): ?>
                var toastElement1 = document.getElementById('invalidPasswordToast');
                var toast1 = new bootstrap.Toast(toastElement1);
                toast1.show();
                <?php $_SESSION['showInvalidPasswordToast'] = false; // Reset after showing the toast 
                ?>
            <?php endif; ?>

            
        });
    </script>

</body>
</html>
