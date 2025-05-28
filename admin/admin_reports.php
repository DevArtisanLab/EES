<?php include'sidebar.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Examination Results</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f9fafb;
      margin: 0;
      padding: 20px;
    }

    h1 {
      font-size: 36px;
      font-weight: 700;
      color: #111827;
    }

    .filters {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }

    select {
      padding: 8px 12px;
      font-size: 14px;
    }

    .stats-container {
      display: flex;
      gap: 20px;
      margin-top: 30px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      flex: 1;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      text-align: center;
    }

    .card-title {
      font-size: 14px;
      color: #6b7280;
      margin-bottom: 8px;
    }

    .card-value {
      font-size: 28px;
      font-weight: 700;
      color: #111827;
    }

    .trend {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 5px;
      margin-top: 6px;
      font-size: 12px;
    }

    .trend-up {
      color: #10b981; /* green */
    }

    .trend-down {
      color: #ef4444; /* red */
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .button {
      padding: 8px 14px;
      background: #3b82f6;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .export-btn {
      background: #f3f4f6;
      color: #374151;
      margin-right: 10px;
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <h1>Examination Results</h1>
    <div>
      <button class="button export-btn">📄 Export Report</button>
      <button class="button">🖨️ Print</button>
    </div>
  </div>

  <div class="filters">
    <label>Position:
      <select>
        <option>All Positions</option>
      </select>
    </label>
    <label>Time Period:
      <select>
        <option>This Month</option>
      </select>
    </label>
    <label>Status:
      <select>
        <option>All Status</option>
      </select>
    </label>
  </div>

  <div class="stats-container">
    <div class="card">
      <div class="card-title">Total Applicants</div>
      <div class="card-value">156</div>
      <div class="trend trend-up">⬆ 12% from last month</div>
    </div>
    <div class="card">
      <div class="card-title">Pass Rate</div>
      <div class="card-value">56%</div>
      <div class="trend trend-up">⬆ 4% from last month</div>
    </div>
    <div class="card">
      <div class="card-title">Average Score</div>
      <div class="card-value">78%</div>
      <div class="trend trend-up">⬆ 2% from last month</div>
    </div>
    <div class="card">
      <div class="card-title">Pending Reviews</div>
      <div class="card-value">27</div>
      <div class="trend trend-down">⬇ 5% from last month</div>
    </div>
  </div>

</body>
</html>
