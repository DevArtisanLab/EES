<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$exam_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($exam_id === 0) {
    echo "<div class='alert alert-danger'>Invalid exam ID.</div>";
    exit;
}

// Fetch exam details
$exam_sql = "SELECT * FROM examinations WHERE exam_id = $exam_id";
$exam_result = $conn->query($exam_sql);

if (!$exam_result || $exam_result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Exam not found.</div>";
    exit;
}

$exam = $exam_result->fetch_assoc();

// Handle exam update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $position = $conn->real_escape_string($_POST['position']);
    $duration = (int) $_POST['duration'];
    $description = $conn->real_escape_string($_POST['description']);
    $passing_score = (int) $_POST['passing_score'];
    $status = $conn->real_escape_string($_POST['status']);

    $update_sql = "UPDATE examinations SET 
        title = '$title',
        position = '$position',
        duration = $duration,
        description = '$description',
        passing_score = $passing_score,
        status = '$status'
        WHERE exam_id = $exam_id";
    $conn->query($update_sql);

    echo "<script>alert('Exam updated successfully.'); window.location.href='admin_examinations.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Exam</title>
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
            padding: 40px;
            width: calc(100% - 250px);
        }

        .form-control, .form-select {
            border-radius: 10px;
        }

        .btn {
            border-radius: 10px;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="bg-white rounded shadow p-4">
            <h2 class="mb-4 text-primary">Edit Examination</h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input name="title" class="form-control" value="<?= htmlspecialchars($exam['title']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position</label>
                        <input name="position" class="form-control" value="<?= htmlspecialchars($exam['position']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input name="duration" type="number" class="form-control" value="<?= $exam['duration'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Passing Score</label>
                        <input name="passing_score" type="number" class="form-control" value="<?= $exam['passing_score'] ?>" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"><?= htmlspecialchars($exam['description']) ?></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Active" <?= $exam['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $exam['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="admin_examinations.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
