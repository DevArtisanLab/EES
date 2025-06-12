<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
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
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

$sql    = "SELECT * FROM examinations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Examinations Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
            color: #374151;
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
            font-size: 1.75rem;
            color: #111827;
        }
        #btnCreate {
            background-color: #2563eb;
            color: white;
            border-radius: 10px;
            padding: 8px 16px;
            border: none;
            font-size: 14px;
        }
        #btnCreate:hover {
            background-color: #1e40af;
        }
        #btnImport {
            font-size: 14px;
            border-radius: 10px;
        }
        .table thead {
            background-color: #f1f3f5;
        }
        .table th, .table td {
            vertical-align: middle;
            font-size: 14px;
        }
        .action-btns .btn {
            margin-right: 5px;
            font-size: 0.8rem;
        }
        .card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 20px;
        }
        /* Status badges */
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        /* Form inline styling */
        .form-inline input[type="file"] {
            margin-right: 10px;
            font-size: 14px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Examinations Management</h2>
            <div class="d-flex align-items-center gap-2">
                <form action="import_exam.php" method="POST" enctype="multipart/form-data" class="d-flex align-items-center form-inline">
                    <input type="file" name="excel_file" required>
                    <button class="btn btn-success" id="btnImport" type="submit"><i class="bi bi-upload"></i> Import</button>
                </form>
                <a href="admin_createexam.php" class="btn" id="btnCreate"><i class="bi bi-plus-circle"></i> Create Exam</a>
            </div>
        </div>

        <div class="table-responsive card">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Exam ID</th>
                        <th>Title</th>
                        <th>Position</th>
                        <th>Duration</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['exam_id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['position']) ?></td>
                        <td><?= $row['duration'] ?> min</td>
                        <td><?= $row['created'] ?></td>
                        <td>
                            <?php if (strtolower($row['status']) === 'active'): ?>
                                <span class="status-active"><?= htmlspecialchars($row['status']) ?></span>
                            <?php else: ?>
                                <span class="status-inactive"><?= htmlspecialchars($row['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="action-btns">
                            <a href="admin_editexam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="admin_editquestion.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-success"><i class="bi bi-pencil-square"></i></a>
                            <a href="delete_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
