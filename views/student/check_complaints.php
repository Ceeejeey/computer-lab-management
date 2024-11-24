<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reported Issues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #1f2937;
            font-weight: bold;
        }

        .table-striped {
            border-radius: 8px;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9fafb;
        }
        .table th {
            background-color: #000;
            color: #fff;
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
        .no-issues-message {
            display: none;
            text-align: center;
            color: #6b7280;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../dashboards/student_dashboard.php" class="btn-dashboard">Go to Dashboard</a>
        <h2>My Reported Issues</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Issue ID</th>
                        <th>Computer ID</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Reported At</th>
                        <th>Action Taken By Admin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="issueTableBody">
                    <!-- Issues will be dynamically loaded here -->
                </tbody>
            </table>
            <p id="noIssuesMessage" class="no-issues-message">No issues reported yet.</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("../../controllers/fetch_student_issues.php")
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById("issueTableBody");
                    const noIssuesMessage = document.getElementById("noIssuesMessage");

                    if (data.length === 0) {
                        noIssuesMessage.style.display = "block";
                    } else {
                        data.forEach(issue => {
                            const row = document.createElement("tr");

                            // Determine badge color based on status
                            let statusBadge;
                            if (issue.status === "Resolved") {
                                statusBadge = '<span class="badge bg-success">Resolved</span>';
                            } else if (issue.status === "Ongoing") {
                                statusBadge = '<span class="badge bg-warning text-dark">Ongoing</span>';
                            } else if (issue.status === "Pending") {
                                statusBadge = '<span class="badge bg-danger">Pending</span>';
                            } else {
                                statusBadge = '<span class="badge bg-secondary">Unknown</span>';
                            }

                            // Add a fallback for `action_taken` if it's null
                            const actionTaken = issue.action_taken ? issue.action_taken : "Not yet reviewed";

                            row.innerHTML = `
                                <td>${issue.issue_id}</td>
                                <td>${issue.computer_id || 'N/A'}</td>
                                <td>${issue.issue_type}</td>
                                <td>${issue.description}</td>
                                <td>${issue.priority}</td>
                                <td>${new Date(issue.created_at).toLocaleDateString()}</td>
                                <td>${actionTaken}</td>
                                <td>${statusBadge}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    }
                })
                .catch(error => console.error("Error fetching issues:", error));
        });
    </script>
</body>
</html>
