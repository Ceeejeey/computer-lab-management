<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lab Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: "poppins" ,Arial, sans-serif;
            color: #333;
        }

        .container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); /* Subtle shadow for container */
            padding: 40px;
            margin-top: 40px;
        }

        h2 {
            color: #000000;
            font-weight: 400;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .schedule-card {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }

        .table-container {
            margin-top: 40px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .table {
            border: none;
            background: #ffffff;
            border-radius: 12px;
        }

        .table th, .table td {
            padding: 15px;
            text-align: center;
            border-top: none;
        }

        .table th {
            background-color: #000000; /* Set to black background */
            color: #ffffff;
            font-size: 1rem;
            font-weight: bold;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .empty-message {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 20px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content-wrapper">
            <h2 class="mb-4 text-center">Today's Lab Schedules</h2>
            <div id="today-schedules" class="row justify-content-center">
                <!-- Today's schedule cards will be inserted here -->
            </div>

            <h2 class="mt-5 text-center">Upcoming Lab Schedules</h2>
            <div class="table-container">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Topic</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody id="upcoming-schedules">
                        <!-- Upcoming schedules will be inserted here -->
                    </tbody>
                </table>
                <div id="empty-message" class="empty-message" style="display: none;">
                    No upcoming sessions
                </div>
            </div>
            <a href="../dashboards/lecturer_dashboard.php" class="btn btn-secondary w-100 mt-3">Go Back to Dashboard</a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("../../controllers/process_lecturer_lab_schedule.php")
                .then(response => response.json())
                .then(data => {
                    const todayContainer = document.getElementById("today-schedules");
                    const upcomingTable = document.getElementById("upcoming-schedules");
                    const emptyMessage = document.getElementById("empty-message");

                    // Display today's schedules in cards
                    if (data.today.length > 0) {
                        data.today.forEach(schedule => {
                            const card = document.createElement("div");
                            card.className = "col-md-4 schedule-card";
                            card.innerHTML = `
                                <h5>Batch: ${schedule.batch}</h5>
                                <p><strong>Topic:</strong> ${schedule.topic}</p>
                                <p><strong>Time:</strong> ${new Date(schedule.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(schedule.end_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                            `;
                            todayContainer.appendChild(card);
                        });
                    } else {
                        const noTodayMessage = document.createElement("p");
                        noTodayMessage.className = "empty-message";
                        noTodayMessage.innerText = "No lab sessions scheduled for today.";
                        todayContainer.appendChild(noTodayMessage);
                    }

                    // Display upcoming schedules in the table or show "No upcoming sessions" if empty
                    if (data.upcoming.length > 0) {
                        data.upcoming.forEach(schedule => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td>${schedule.batch}</td>
                                <td>${schedule.topic}</td>
                                <td>${new Date(schedule.start_time).toLocaleString([], { hour: '2-digit', minute: '2-digit' })}</td>
                                <td>${new Date(schedule.end_time).toLocaleString([], { hour: '2-digit', minute: '2-digit' })}</td>
                            `;
                            upcomingTable.appendChild(row);
                        });
                    } else {
                        emptyMessage.style.display = "block";
                    }
                })
                .catch(error => console.error("Error fetching schedules:", error));
        });
    </script>
</body>
</html>
