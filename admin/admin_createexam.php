<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = $conn->real_escape_string($_POST['title']);
    $position      = $conn->real_escape_string($_POST['position']);
    $duration      = (int) $_POST['duration'];
    $description   = $conn->real_escape_string($_POST['description']);
    $passing_score = (int) $_POST['passing_score'];
    $status        = $conn->real_escape_string($_POST['status']);
    $created       = date('Y-m-d H:i:s');

    $insert_sql = "INSERT INTO examinations (title, position, duration, description, passing_score, status, created)
                   VALUES ('$title', '$position', $duration, '$description', $passing_score, '$status', '$created')";

    if ($conn->query($insert_sql) === TRUE) {
        $exam_id = $conn->insert_id;
        header("Location: admin_createquestion.php?exam_id=" . $exam_id);
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Examination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
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
            padding: 30px;
            width: calc(100% - 250px);
        }
        h2 {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Create New Examination</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Examination Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="row">
                <div class="mb-3 col">
                    <label class="form-label">Position</label>
                    <select name="position" class="form-select" required>
                        <option value="" disabled selected>Select Department</option>
                        <option value="All">All</option>
                        <option value="SM">Store Manager</option>
                        <option value="MT">Management Trainee</option>
                        <option value="KS">Kitchen Supervisor</option>
                        <option value="DS">Dining Supervisor</option>
                        <option value="CS">Cashier</option>
                        <option value="KSS">Kitchen Staff</option>
                        <option value="DSS">Dining Staff</option>
                    </select>
                </div>
                <div class="mb-3 col">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" name="duration" class="form-control" value="60" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="mb-3 col">
                    <label class="form-label">Passing Score (%)</label>
                    <input type="number" name="passing_score" class="form-control" value="75" required>
                </div>
                <div class="mb-3 col">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="" disabled selected>Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="admin_examinations.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" name="create_exam">Next</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
