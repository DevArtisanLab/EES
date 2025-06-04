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

    $created = date('Y-m-d H:i:s');

    $insert_sql = "INSERT INTO examinations (title, position, duration, description, passing_score, status, created)
                   VALUES ('$title', '$position', $duration, '$description', $passing_score, '$status', '$created')";

    if ($conn->query($insert_sql) === TRUE) {
        // Redirect to refresh the page and avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

$sql    = "SELECT * FROM examinations WHERE status = 'Active'";
$result = $conn->query($sql);

include 'sidebar.php';
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
        #btnCreate {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px;
            padding: 8px 16px;
            border: none;
        }
        #btnCreate:hover {
            background-color: #0b5ed7;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        .table thead {
            background-color: #f1f3f5;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .action-btns .btn {
            margin-right: 5px;
            font-size: 0.8rem;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .form-row {
            display: flex;
            gap: 10px;
        }
        .form-row > * {
            flex: 1;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        .modal-footer {
            display: flex;
            justify-content: space-between;
        }
        .btn-primary {
            background-color: #2563eb;
            color: white;
            border: none;
        }
        .btn-secondary {
            background-color: white;
            color: black;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Examinations Management</h2>
        <a href="admin_createexam.php" class="btn btn-primary" id="btnCreate">+ Create Exam</a>
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
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['position'] ?></td>
               
                    <td><?= $row['duration'] ?> min</td>
                    <td><?= $row['created'] ?></td>
                    <td><span class="status-active"><?= $row['status'] ?></span></td>
                    <td class="action-btns">
                        <a href="view_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-primary">View</a>
                        <a href="edit_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-success">Edit</a>
                        <a href="delete_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
