
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
        $exam_id = $conn->insert_id;  // Get last inserted exam ID
        header("Location: admin_createquestion.php?exam_id=" . $exam_id);
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}
?>
<?php include 'sidebar.php' ?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Examination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">Create New Examination</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Examination Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="row">
                <div class="mb-3 col">
                    <label>Position</label>
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
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration" class="form-control" value="60" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="mb-3 col">
                    <label>Passing Score (%)</label>
                    <input type="number" name="passing_score" class="form-control" value="75" required>
                </div>
                <div class="mb-3 col">
                    <label>Status</label>
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
</body>
</html>
