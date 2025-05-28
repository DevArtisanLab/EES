<?php
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
    <title>Examinations Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        h2 {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .btn-create {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px;
        }
        .btn-create:hover {
            background-color: #0b5ed7;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 0.85rem;
            display: inline-block;
        }
        .table {
            background-color: white;
            border-collapse: collapse;
        }
        .table thead {
            background-color: #f1f3f5;
            font-size: 0.9rem;
        }
        .table th, .table td {
            border: none;
            padding: 0.75rem;
            font-size: 0.95rem;
            vertical-align: middle;
        }
        .nav-tabs .nav-link.active {
            font-weight: 600;
            border-bottom: 2px solid #0d6efd;
        }
        .action-btns .btn {
            margin-right: 5px;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Examinations Management</h2>
            <div>
                <a href="create_exam.php" class="btn btn-create">+ Create Exam</a>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" href="#">Active Examinations</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Draft</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Archived</a></li>
        </ul>

        <div class="table-responsive">
            <table class="table align-middle">
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
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['exam_id'] ?></td>
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['position'] ?></td>
                        <td><?= $row['questions'] ?></td>
                        <td><?= $row['duration'] ?> min</td>
                        <td><?= $row['created'] ?></td>
                        <td><span class="status-active"><?= $row['status'] ?></span></td>
                        <td class="action-btns">
                            <a href="view_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-primary" title="View">View</a>
                            <a href="edit_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-success" title="Edit">Edit</a>
                            <a href="delete_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete">Delete</a>
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
