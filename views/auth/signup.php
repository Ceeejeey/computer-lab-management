<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f4f7;
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        input,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: border 0.3s ease;
        }

        input:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
            background-color: #fff;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        button:hover:not(:disabled) {
            background-color: #0056b3;
        }

        .strength-meter {
            margin-top: 10px;
        }

        .strength-meter div {
            height: 10px;
            background-color: lightgray;
            margin-top: 5px;
            border-radius: 5px;
            transition: width 0.3s ease;
        }

        .strength-meter .weak {
            width: 25%;
            background-color: red;
        }

        .strength-meter .medium {
            width: 50%;
            background-color: orange;
        }

        .strength-meter .strong {
            width: 75%;
            background-color: yellowgreen;
        }

        .strength-meter .very-strong {
            width: 100%;
            background-color: green;
        }

        .confirm-password-valid {
            border-color: green !important;
        }

        .confirm-password-invalid {
            border-color: red !important;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form action="http://localhost/lab_management/controllers/signup_process.php" method="POST">
            
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required class="form-control">
                
                <!-- Password strength meter -->
                <div class="strength-meter">
                    <div id="strength-bar"></div>
                </div>

                
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" id="signup-btn" class="btn btn-primary" disabled>Sign Up</button>

            <div class="form-footer">
                <p>Already have an account? <a href="signin.php">Sign In</a></p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        const signupBtn = document.getElementById('signup-btn');

        // Password Strength Meter Logic
        password.addEventListener('input', function () {
            const val = password.value;
            let strength = 0;

            if (val.match(/[a-z]+/)) strength += 1; 
            if (val.match(/[A-Z]+/)) strength += 1; 
            if (val.match(/[0-9]+/)) strength += 1; 
            if (val.match(/[\W_]+/)) strength += 1; 

            switch (strength) {
                case 0:
                    strengthBar.className = '';
                    signupBtn.disabled = true;
                    break;
                case 1:
                    strengthBar.className = 'weak';
                    signupBtn.disabled = true;
                    break;
                case 2:
                    strengthBar.className = 'medium';
                    signupBtn.disabled = true;
                    break;
                case 3:
                    strengthBar.className = 'strong';
                    signupBtn.disabled = false;
                    break;
                case 4:
                    strengthBar.className = 'very-strong';
                    signupBtn.disabled = false;
                    break;
            }
        });

        // Confirm Password Match Logic
        confirmPassword.addEventListener('input', function () {
            if (confirmPassword.value === password.value && password.value !== '') {
                confirmPassword.classList.add('confirm-password-valid');
                confirmPassword.classList.remove('confirm-password-invalid');
            } else {
                confirmPassword.classList.add('confirm-password-invalid');
                confirmPassword.classList.remove('confirm-password-valid');
            }
        });
    </script>

</body>

</html>