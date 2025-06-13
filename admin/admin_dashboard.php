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

// Combine pending from employee table and answers table (is_correct is NULL)
$pendingEmployee = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status IS NULL AND average IS NULL")->fetch_assoc()['count'];

$pendingAnswers = $conn->query("
  SELECT COUNT(DISTINCT employee_num) as count
  FROM answers
  WHERE is_correct IS NULL
")->fetch_assoc()['count'];

// Use the higher of the two to avoid undercounting
$pending = max($pendingEmployee, $pendingAnswers);

// Pass rate (only based on completed exams)
$completed = $passed + $failed;
$pass_rate = $completed > 0 ? round(($passed / $completed) * 100, 2) : 0;

// Fetch employee results
$results = $conn->query("SELECT * FROM employee ORDER BY submitted_at DESC");

// Difficulty chart
$difficultyData = [];
$difficultyQuery = $conn->query("
  SELECT question_id,
         COUNT(*) AS total_attempts,
         SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) AS incorrect_count
  FROM answers
  GROUP BY question_id
");

while ($row = $difficultyQuery->fetch_assoc()) {
  if ($row['incorrect_count'] == 0) continue;
  $questionId = 'Q-' . $row['question_id'];
  $incorrectRate = round(($row['incorrect_count'] / $row['total_attempts']) * 100, 2);
  $difficultyData[$questionId] = $incorrectRate;
}

// Sort by incorrect rate descending
arsort($difficultyData);

// Limit to top 10
$difficultyData = array_slice($difficultyData, 0, 10, true);

// Fetch exam titles from `examinations` and match to score_1 to score_10
$examTitles = [];
$titleQuery = $conn->query("SELECT exam_id, title FROM examinations ORDER BY exam_id ASC LIMIT 10");
while ($row = $titleQuery->fetch_assoc()) {
  $examTitles[intval($row['exam_id'])] = $row['title'];
}

// Initialize labels and score accumulators
$examLabels = [];
$examScores = array_fill(0, 10, ['sum' => 0, 'count' => 0]);

// Get employee exam scores
$examQuery = $conn->query("SELECT score_1, score_2, score_3, score_4, score_5, score_6, score_7, score_8, score_9, score_10 FROM employee WHERE status IN ('Passed', 'Failed')");
while ($row = $examQuery->fetch_assoc()) {
  for ($i = 0; $i < 10; $i++) {
    $score = floatval($row["score_" . ($i + 1)]);
    if ($score !== null && $score > 0) {
      $examScores[$i]['sum'] += $score;
      $examScores[$i]['count'] += 1;
    }
  }
}

// Prepare labels and averages
$examAverages = [];
for ($i = 0; $i < 8; $i++) {
  $examLabels[] = isset($examTitles[$i + 1]) ? $examTitles[$i + 1] : "Exam " . ($i + 1);
  $data = $examScores[$i];
  $average = $data['count'] > 0 ? round(($data['sum'] / $data['count']), 2) : 0;
  $examAverages[] = $average;
}

// Pass rates per position (low-passing)
$positionRates = [];
$positionQuery = $conn->query("SELECT position, status FROM employee");
$positionStats = [];

while ($row = $positionQuery->fetch_assoc()) {
  $pos = $row['position'];
  $status = $row['status'];

  if (!isset($positionStats[$pos])) {
    $positionStats[$pos] = ['total' => 0, 'passed' => 0];
  }

  $positionStats[$pos]['total']++;
  if ($status === 'Passed') {
    $positionStats[$pos]['passed']++;
  }
}

foreach ($positionStats as $pos => $data) {
  $rate = ($data['total'] > 0) ? round(($data['passed'] / $data['total']) * 100, 2) : 0;
  $positionRates[$pos] = $rate;
}
asort($positionRates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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
  </style>
</head>
<body class="bg-gray-100 font-sans">
  <div class="flex min-h-screen">
    <?php include 'sidebar.php'; ?>
    <main class="flex-1 p-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Total Employees</div>
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

      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card p-3">
            <h5>Exam Pass Rates</h5>
            <p class="text-muted">Pass Rate: <?= $pass_rate ?>%</p>
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
            <h5>Low-Passing Examination Questions (by Position)</h5>
            <table class="table table-bordered mt-3">
              <thead class="table-light">
                <tr>
                  <th>Position</th>
                  <th>Pass Rate (%)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($positionRates as $position => $rate): ?>
                  <tr>
                    <td><?= htmlspecialchars($position) ?></td>
                    <td><?= $rate ?>%</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <script>
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
          type: 'pie',
          data: {
            labels: ['Passed', 'Failed', 'Pending'],
            datasets: [{
              data: [<?= $passed ?>, <?= $failed ?>, <?= $pending ?>],
              backgroundColor: ['#28a745', '#dc3545', '#ffc107']
            }]
          },
          options: { responsive: true, plugins: { legend: { position: 'top' } } }
        });

        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
          type: 'bar',
          data: {
            labels: <?= json_encode($examLabels) ?>,
            datasets: [{
              label: 'Average Score (%)',
              data: <?= json_encode($examAverages) ?>,
              backgroundColor: '#0d6efd'
            }]
          },
          options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } }
          }
        });

        const hBarCtx = document.getElementById('horizontalBarChart').getContext('2d');
        new Chart(hBarCtx, {
          type: 'bar',
          data: {
            labels: <?= json_encode(array_keys($difficultyData)) ?>,
            datasets: [{
              label: 'Incorrect Answer Rate (%)',
              data: <?= json_encode(array_values($difficultyData)) ?>,
              backgroundColor: '#dc3545'
            }]
          },
          options: {
            indexAxis: 'y',
            responsive: true,
            scales: { x: { beginAtZero: true, max: 100 } }
          }
        });
      </script>
    </main>
  </div>
</body>
</html>
