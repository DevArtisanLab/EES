<?php
require_once '../includes/db_config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Examinations Management</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f9fafe; margin: 0; }
    .container { margin-left: 220px; padding: 30px; }
    h1 { font-size: 36px; font-weight: bold; }
    .tabs { margin-top: 10px; }
    .tabs button {
      border: none;
      background: none;
      font-weight: bold;
      margin-right: 20px;
      padding-bottom: 5px;
      cursor: pointer;
    }
    .tabs .active { border-bottom: 3px solid #3b82f6; color: #3b82f6; }
    table { width: 100%; margin-top: 20px; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    th, td { padding: 15px; text-align: left; }
    th { background-color: #f1f5f9; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
    td { border-bottom: 1px solid #f0f0f0; }
    .btn {
      padding: 5px 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn-view { background: #e0f2fe; color: #0369a1; }
    .btn-edit { background: #dcfce7; color: #15803d; }
    .btn-delete { background: #fee2e2; color: #b91c1c; }
    .status-active {
      background: #d1fae5;
      color: #065f46;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
    }
    .actions i { margin: 0 4px; }
    .top-actions {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 20px;
    }
    .top-actions button {
      margin-left: 10px;
      padding: 10px 16px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn-import { background: #f1f5f9; }
    .btn-create { background: #3b82f6; color: white; }
  </style>
</head>
<body>
    <aside class="w-64 bg-white border-r">
      <div class="p-6 font-bold text-xl text-blue-600">Administrator</div>
      <nav class="space-y-2 text-gray-700 pl-6">
        <a href="#" class="block py-2 font-medium bg-blue-100 rounded pr-4">Dashboard</a>
        <a href="#" class="block py-2 hover:text-blue-600">Employee</a>
        <a href="#" class="block py-2 hover:text-blue-600">Examinations</a>
        <a href="#" class="block py-2 hover:text-blue-600">Upload File</a>
        <a href="#" class="block py-2 hover:text-blue-600">Results</a>
        <a href="#" class="block py-2 hover:text-blue-600">Settings</a>
      </nav>
    </aside>
<div class="container">
  <h1>Examinations Management</h1>

  <div class="top-actions">
    <button class="btn-import"><i class="fas fa-file-import"></i> Import</button>
    <button class="btn-create"><i class="fas fa-plus"></i> Create Exam</button>
  </div>

  <div class="tabs">
    <button class="active">Active Examinations</button>
    <button>Draft</button>
    <button>Archived</button>
  </div>

  <table>
    <thead>
      <tr>
        <th>Exam ID</th>
        <th>Title</th>
        <th>Position</th>
        <th>Questions</th>
        <th>Duration</th>
        <th>Created</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "SELECT * FROM examinations WHERE status = 'Active'";
      $result = $conn->query($query);

      while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['exam_id']}</td>
                <td>{$row['title']}</td>
                <td>{$row['position']}</td>
                <td>{$row['questions']}</td>
                <td>{$row['duration']} min</td>
                <td>{$row['created_at']}</td>
                <td><span class='status-active'>{$row['status']}</span></td>
                <td class='actions'>
                  <button class='btn btn-view'><i class='fas fa-eye'></i></button>
                  <button class='btn btn-edit'><i class='fas fa-pen'></i></button>
                  <button class='btn btn-delete'><i class='fas fa-trash'></i></button>
                </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</div>

</body>
</html>
