<?php
session_start();

// Automatically apply filters if GET parameters are present
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['position'])) {
  $_SESSION['filter_position'] = $_GET['position'] ?? 'All';
  $_SESSION['filter_time_period'] = $_GET['date_of_exam'] ?? 'All';
  $_SESSION['filter_status'] = $_GET['status'] ?? 'All';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Examination Results</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f9fafb;
      margin: 0;
    }
    .sidebar {
      width: 250px;
      min-height: 100vh;
      background-color: #fff;
      border-right: 1px solid #ddd;
      padding: 20px;
    }
    .main-content {
      padding: 30px;
      background-color: #f9fafb;
      min-height: 100vh;
      flex-grow: 1;
    }
    h1 {
      font-size: 32px;
      font-weight: bold;
      color: #1f2937;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    .filters {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }
    .filters label {
      font-size: 14px;
      color: #374151;
      display: flex;
      flex-direction: column;
    }
    .filters select {
      padding: 8px 12px;
      font-size: 14px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .button {
      padding: 8px 16px;
      font-size: 14px;
      font-weight: 500;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }
    .export-btn {
      background-color: #e5e7eb;
      color: #1f2937;
      margin-right: 10px;
    }
    .button:not(.export-btn) {
      background-color: #3b82f6;
      color: white;
    }
    .button:hover {
      opacity: 0.9;
    }
    table thead th {
      white-space: nowrap;
      background-color: #f3f4f6;
      font-size: 14px;
      color: #111827;
    }
    table tbody td {
      font-size: 14px;
      color: #374151;
    }
    #report-container h3 {
      font-size: 20px;
      font-weight: 600;
      color: #111827;
    }
    .table-responsive {
      max-height: 70vh;
      overflow: auto;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar Include -->
    <div class="sidebar">
      <?php include 'sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="top-bar">
        <h2>Reports</h2>
        <div>
          <button class="button export-btn" id="generateReportBtn">📄 Generate Report</button>
          <button class="button">🖨️ Export Report</button>
        </div>
      </div>

      <!-- Filter Form -->
      <form method="GET" class="filters" id="filterForm">
        <label>Position:
          <select name="position" id="position">
            <option value="All" <?= ($_SESSION['filter_position'] ?? '') == 'All' ? 'selected' : '' ?>>All Positions</option>
            <option value="Store Manager" <?= ($_SESSION['filter_position'] ?? '') == 'Store Manager' ? 'selected' : '' ?>>Store Manager</option>
            <option value="Assistant Store Manager" <?= ($_SESSION['filter_position'] ?? '') == 'Assistant Store Manager' ? 'selected' : '' ?>>Assistant Store Manager</option>
            <option value="Management Trainee" <?= ($_SESSION['filter_position'] ?? '') == 'Management Trainee' ? 'selected' : '' ?>>Management Trainee</option>
            <option value="Admin Assistant" <?= ($_SESSION['filter_position'] ?? '') == 'Admin Assistant' ? 'selected' : '' ?>>Admin Assistant</option>
            <option value="Dining Supervisor" <?= ($_SESSION['filter_position'] ?? '') == 'Dining Supervisor' ? 'selected' : '' ?>>Dining Supervisor</option>
            <option value="Kitchen Supervisor" <?= ($_SESSION['filter_position'] ?? '') == 'Kitchen Supervisor' ? 'selected' : '' ?>>Kitchen Supervisor</option>
            <option value="Cashier" <?= ($_SESSION['filter_position'] ?? '') == 'Cashier' ? 'selected' : '' ?>>Cashier</option>
            <option value="Dining Staff" <?= ($_SESSION['filter_position'] ?? '') == 'Dining Staff' ? 'selected' : '' ?>>Dining Staff</option>
            <option value="Kitchen Staff" <?= ($_SESSION['filter_position'] ?? '') == 'Kitchen Staff' ? 'selected' : '' ?>>Kitchen Staff</option>
          </select>
        </label>
        <label>Time Period:
          <select name="date_of_exam" id="date_of_exam">
            <option value="All" <?= ($_SESSION['filter_time_period'] ?? '') == 'All' ? 'selected' : '' ?>>All</option>
            <option value="This Month" <?= ($_SESSION['filter_time_period'] ?? '') == 'This Month' ? 'selected' : '' ?>>This Month</option>
            <option value="This Week" <?= ($_SESSION['filter_time_period'] ?? '') == 'This Week' ? 'selected' : '' ?>>This Week</option>
            <option value="Today" <?= ($_SESSION['filter_time_period'] ?? '') == 'Today' ? 'selected' : '' ?>>Today</option>
          </select>
        </label>
        <label>Status:
          <select name="status" id="status">
            <option value="All" <?= ($_SESSION['filter_status'] ?? '') == 'All' ? 'selected' : '' ?>>All Status</option>
            <option value="Passed" <?= ($_SESSION['filter_status'] ?? '') == 'Passed' ? 'selected' : '' ?>>Passed</option>
            <option value="Failed" <?= ($_SESSION['filter_status'] ?? '') == 'Failed' ? 'selected' : '' ?>>Failed</option>
            <option value="Pending" <?= ($_SESSION['filter_status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
          </select>
        </label>
      </form>

      <div class="mt-4" id="report-container" style="display:none;">
        <h3 class="mb-3">Generated Report</h3>
        <div class="table-responsive">
          <table class="table table-bordered align-middle text-center" id="report-table">
            <thead class="table-light">
              <tr>
                <th>Employee Number</th>
                <th>Full Name</th>
                <th>Branch</th>
                <th>Position</th>
                <th>Date Hired</th>
                <th>Date of Exam</th>
                <th>Score 1</th>
                <th>Score 2</th>
                <th>Score 3</th>
                <th>Score 4</th>
                <th>Score 5</th>
                <th>Score 6</th>
                <th>Average</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('generateReportBtn').addEventListener('click', function () {
      const position = document.getElementById('position').value;
      const date_of_exam = document.getElementById('date_of_exam').value;
      const status = document.getElementById('status').value;

      const params = new URLSearchParams({
        position: position,
        date_of_exam: date_of_exam,
        status: status
      });

      window.location.href = window.location.pathname + '?' + params.toString();
    });

    window.addEventListener('DOMContentLoaded', function () {
      <?php if (isset($_SESSION['filter_position'])): ?>
      fetch('generate_report.php')
        .then(response => response.json())
        .then(data => {
          const tbody = document.querySelector('#report-table tbody');
          tbody.innerHTML = '';
          data.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${row.employee_num ?? ''}</td>
              <td>${row.full_name ?? ''}</td>
              <td>${row.branch ?? ''}</td>
              <td>${row.position ?? ''}</td>
              <td>${row.date_started ?? ''}</td>
              <td>${row.date_of_exam ?? ''}</td>
              <td>${row.score_1 ?? ''}</td>
              <td>${row.score_2 ?? ''}</td>
              <td>${row.score_3 ?? ''}</td>
              <td>${row.score_4 ?? ''}</td>
              <td>${row.score_5 ?? ''}</td>
              <td>${row.score_6 ?? ''}</td>
              <td>${row.average ?? ''}</td>
              <td>${row.status ?? ''}</td>
            `;
            tbody.appendChild(tr);
          });
          document.getElementById('report-container').style.display = 'block';
        });
      <?php endif; ?>
    });

    document.querySelector('.button:not(.export-btn)').addEventListener('click', function () {
      const table = document.getElementById('report-table');
      let csv = '';
      const rows = table.querySelectorAll('tr');
      rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = Array.from(cols).map(col => `"${col.innerText.replace(/"/g, '""')}"`);
        csv += rowData.join(',') + '\n';
      });

      const blob = new Blob([csv], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'EES_report.csv';
      a.click();
      window.URL.revokeObjectURL(url);
    });
  </script>
</body>
</html>
