<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e5e5e5;
            padding: 20px;
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
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="title">Admin Dashboard</div>
        <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="admin_employee.php" class="nav-link"><i class="bi bi-people-fill"></i> Examination</a>
        <a href="admin_examinations.php" class="nav-link"><i class="bi bi-clipboard-data"></i> Exams</a>
        <!-- <a href="admin_upload.php" class="nav-link"><i class="bi bi-file-earmark-arrow-up"></i> Upload Documents</a> -->
        <a href="admin_reports.php" class="nav-link"><i class="bi bi-journal-text"></i> Reports</a>
        <!-- <a href="admin_settings.php" class="nav-link"><i class="bi bi-gear-fill"></i> Settings</a> -->
        <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</body>
</html>
