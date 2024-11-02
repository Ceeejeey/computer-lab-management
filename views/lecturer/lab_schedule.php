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
            font-family: Arial, sans-serif;
            color: #333;
        }

        h2 {
            color: #333;
            font-weight: 600;
        }

        .schedule-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #e2e8f0, #f8fafc);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .table-container {
            margin-top: 40px;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9fafb;
        }

        .table {
            border: none;
            background: #ffffff;
        }

        .table th, .table td {
            border-top: none;
            padding: 15px;
            text-align: center;
        }

        .table thead {
            background-color: #1d4ed8;
            color: #ffffff;
            font-size: 1rem;
            font-weight: bold;
        }

        .table tbody tr:hover {
            background-color: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Today's Lab Schedules</h2>
        <div id="today-schedules" class="row">
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
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("../../controllers/process_lecturer_lab_schedule.php")
                .then(response => response.json())
                .then(data => {
                    const todayContainer = document.getElementById("today-schedules");
                    const upcomingTable = document.getElementById("upcoming-schedules");

                    // Display today's schedules in cards
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

                    // Display upcoming schedules in the table
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
                })
                .catch(error => console.error("Error fetching schedules:", error));
        });
    </script>
</body>
</html>
