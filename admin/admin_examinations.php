<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM examinations WHERE status = 'Active'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Examinations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .btn-create {
            background-color: #0d6efd;
            color: white;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
            border-radius: 12px;
            padding: 2px 10px;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <!-- Sidebar Include -->
    <?php include 'sidebar.php'; ?>
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Examinations Management</h2>
        <div>
            <button class="btn btn-outline-secondary me-2">📥 Import</button>
            <a href="create_exam.php" class="btn btn-create">+ Create Exam</a>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Active Examinations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Draft</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Archived</a>
        </li>
    </ul>

    <!-- Exams Table -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>EXAM ID</th>
                    <th>TITLE</th>
                    <th>POSITION</th>
                    <th>QUESTIONS</th>
                    <th>DURATION</th>
                    <th>CREATED</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['exam_id'] ?></td>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['position'] ?></td>
                    <td><?= $row['questions'] ?></td>
                    <td><?= $row['duration'] ?> min</td>
                    <td><?= $row['created'] ?></td>
                    <td><span class="status-active"><?= $row['status'] ?></span></td>
                    <td>
                        <a href="view_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-sm btn-outline-primary">👁️</a>
                        <a href="edit_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-sm btn-outline-success">✏️</a>
                        <a href="delete_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">🗑️</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
