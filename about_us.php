<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Computer Laboratory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9fafb;
    margin: 0;
    padding: 0;
}

/* Navigation Bar */
.navbar {
    background-color: #4e73df;
    border-radius: 0;
}

.navbar .navbar-brand {
    color: #fff;
    font-weight: bold;
}

.navbar .navbar-nav .nav-link {
    color: #fff;
    padding: 10px 15px;
}

.navbar .navbar-nav .nav-link:hover {
    background-color: #2e5bdb;
    border-radius: 5px;
}

/* About Us Section */
.about-heading {
    font-size: 32px;
    color: #4e73df;
    font-weight: 700;
    margin-bottom: 20px;
}

.about-text {
    font-size: 18px;
    color: #333;
    line-height: 1.6;
    margin-bottom: 20px;
}

/* Image Styling */
.about-image img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Footer */
footer {
    background-color: #f1f3f5;
    color: #555;
    font-size: 14px;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .about-heading {
        font-size: 28px;
    }
    .about-text {
        font-size: 16px;
    }
    .about-image {
        display: none;
    }
}

    </style> <!-- Link to your custom CSS -->
</head>
<body>


    <!-- About Us Section -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="about-heading">About The System</h2>
                <p class="about-text">
                    The Department of Business & Management Studies heavily relies on its computer laboratory,
                    which serves as a vital resource for students across all academic years. It houses 75 desktop
                    computers, with only 60 currently functional, and is equipped with a smart board for teaching.
                    The lab can accommodate up to 75 students. However, the laboratory faces significant challenges
                    in management and efficient usage, affecting its ability to support the department's academic
                    activities effectively.
                </p>
                <p class="about-text">
                    At present, the lab operates under a manual system for tasks such as scheduling, inventory
                    management, and maintenance tracking. This manual process leads to several inefficiencies,
                    including scheduling conflicts, underutilized resources, and challenges in tracking equipment
                    status and addressing maintenance issues. As the number of users grows, these problems become
                    more pronounced, negatively impacting the learning environment.
                </p>
                <p class="about-text">
                    The proposed Computer Laboratory Management System aims to address these issues by automating
                    core processes. The system will enhance operational efficiency by streamlining scheduling,
                    improving resource allocation, ensuring timely maintenance, and generating detailed reports.
                    The ultimate goal is to create a more organized, efficient, and user-friendly system for managing
                    the department’s computer lab, thereby improving the learning experience for both students and faculty.
                </p>
            </div>

        </div>
    </div>

    <!-- Footer Section -->
    <footer class="bg-light text-center py-4 mt-5">
        <p>&copy; 2024 Computer Laboratory Management System. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
