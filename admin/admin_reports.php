<?php include 'sidebar.php'; ?>
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

    .d-flex {
      display: flex;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #ffffff;
      border-right: 1px solid #e5e5e5;
      padding: 20px;
      position: fixed;
      top: 0;
      left: 0;
    }

    .sidebar .title {
      color: #2563eb;
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .sidebar .nav-link {
      color: #374151;
      font-size: 14px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      text-decoration: none;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #eef2ff;
      border-radius: 8px;
      color: #2563eb;
      font-weight: 500;
    }

    .sidebar .nav-link i {
      margin-right: 10px;
      font-size: 16px;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
    }

    h1 {
      font-size: 36px;
      font-weight: 700;
      color: #111827;
    }

    .filters {
      margin-top: 20px;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .filters label {
      font-size: 14px;
      color: #374151;
    }

    .filters select {
      padding: 8px 12px;
      font-size: 14px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .stats-container {
      display: flex;
      gap: 20px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      flex: 1;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      text-align: center;
      min-width: 200px;
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
      color: #10b981;
    }

    .trend-down {
      color: #ef4444;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
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
  <div class="d-flex">
    <!-- Sidebar already included via include -->

    <div class="main-content">
      <div class="top-bar mb-4">
        <h1>Reports</h1>
        <div>
          <button class="button export-btn">📄 Generate Report</button>
          <button class="button">🖨️ Export Report</button>
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

      <div class="stats-container mt-5">
        <div class="card">
          <div class="card-title">Total Exams</div>
          <div class="card-value">25</div>
        </div>
        <div class="card">
          <div class="card-title">Passed</div>
          <div class="card-value">18</div>
          <div class="trend trend-up"><i class="bi bi-arrow-up"></i> 10%</div>
        </div>
        <div class="card">
          <div class="card-title">Failed</div>
          <div class="card-value">7</div>
          <div class="trend trend-down"><i class="bi bi-arrow-down"></i> 5%</div>
        </div>
      </div>

      <!-- ADDED REPORT TABLE -->
      <div class="mt-5" id="report-container" style="display:none;">
        <h3 class="mb-3">Generated Report</h3>
        <div class="table-responsive">
          <table class="table table-bordered" id="report-table">
            <thead class="table-light">
              <tr>
                <th>Employee #</th>
                <th>Full Name</th>
                <th>Branch</th>
                <th>Position</th>
                <th>Date of Exam</th>
                <th>Average</th>
                <th>Status</th>
                <th>Submitted At</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ADDED SCRIPT -->
  <script>
    document.querySelector('.export-btn').addEventListener('click', function () {
      fetch('generate_report.php')
        .then(response => response.json())
        .then(data => {
          const tbody = document.querySelector('#report-table tbody');
          tbody.innerHTML = '';
          data.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${row.employee_num}</td>
              <td>${row.full_name}</td>
              <td>${row.branch}</td>
              <td>${row.position}</td>
              <td>${row.date_of_exam}</td>
              <td>${row.average}</td>
              <td>${row.status}</td>
              <td>${row.submitted_at}</td>
            `;
            tbody.appendChild(tr);
          });
          document.getElementById('report-container').style.display = 'block';
        });
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
      a.download = 'exam_report.csv';
      a.click();
      window.URL.revokeObjectURL(url);
    });
  </script>
</body>
</html>
