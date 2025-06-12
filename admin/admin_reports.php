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

    .main-content {
      margin-left: 250px;
      padding: 30px;
      min-height: 100vh;
      background-color: #f9fafb;
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
  <div class="main-content">
    <div class="top-bar">
      <h2>Reports</h1>
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
              <th>Date Started</th>
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
