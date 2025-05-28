<?php
// DB connection
$host = "localhost";
$dbname = "ees";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch stats
$total = $conn->query("SELECT COUNT(*) as count FROM employee")->fetch_assoc()['count'];
$passed = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Passed'")->fetch_assoc()['count'];
$failed = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Failed'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Pending'")->fetch_assoc()['count'];

// Fetch employee results
$results = $conn->query("SELECT * FROM employee ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">
  <div class="flex min-h-screen">
      <!-- Sidebar Include -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Total Employee</div>
          <div class="text-2xl font-bold"><?= $total ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Passed</div>
          <div class="text-2xl font-bold"><?= $passed ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Failed</div>
          <div class="text-2xl font-bold"><?= $failed ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Pending</div>
          <div class="text-2xl font-bold"><?= $pending ?></div>
        </div>
      </div>
      <body>
        <html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            border-radius: 12px;
        }
        .card h5 {
            font-size: 16px;
            margin-bottom: 0.5rem;
        }
        .card .display-6 {
            font-size: 32px;
            font-weight: 600;
        }
        .chart-container {
            width: 100%;
            height: 300px;
        }
        .table img {
            max-width: 100%;
            border-radius: 12px;
        }
    </style>
</head>
<body>
   

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Exam Pass Rates</h5>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Exam Comparison</h5>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Question Difficulty</h5>
                <div class="chart-container">
                    <canvas id="horizontalBarChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Low-Passing Examination Questions</h5>
                <img src="/mnt/data/3b838d46-bf03-4613-91e8-9cfa080c0c16.png" alt="Low-Passing Examination Questions Table">
            </div>
        </div>
    </div>

    <script>
        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Passed', 'Failed', 'Pending'],
                datasets: [{
                    data: [87, 42, 27],
                    backgroundColor: ['#28a745', '#dc3545', '#d39e00']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Bar Chart - Exam Comparison
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Software Dev', 'Data Analyst', 'Network Eng', 'UI/UX', 'DevOps'],
                datasets: [{
                    label: 'Pass Rate (%)',
                    data: [85, 88, 75, 92, 78],
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Horizontal Bar Chart - Question Difficulty
        const hBarCtx = document.getElementById('horizontalBarChart').getContext('2d');
        new Chart(hBarCtx, {
            type: 'bar',
            data: {
                labels: ['Q-1023', 'Q-0458', 'Q-2198', 'Q-0871', 'Q-1156'],
                datasets: [{
                    label: 'Pass Rate (%)',
                    data: [35, 28, 40, 45, 30],
                    backgroundColor: '#dc3545'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>
