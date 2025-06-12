<?php
$exam_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM question WHERE exam_id = $exam_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Questions for Exam #<?= $exam_id ?></title>
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

        table th {
            background-color: #f1f5f9;
        }

        h2 {
            color: #2563eb;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="bg-white rounded shadow p-4">
            <h2>Edit Questions for Exam #<?= $exam_id ?></h2>
            <form action="update_questions.php" method="post">
                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Correct Answer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= $row['id'] ?>
                                <input type="hidden" name="question_ids[]" value="<?= $row['id'] ?>">
                            </td>
                            <td>
                                <input type="text" name="questions[]" class="form-control" value="<?= htmlspecialchars($row['question_text']) ?>">
                            </td>
                            <td>
                                <select name="types[]" class="form-select">
                                    <option value="Multiple Choice" <?= $row['question_type'] == 'Multiple Choice' ? 'selected' : '' ?>>Multiple Choice</option>
                                    <option value="True/False" <?= $row['question_type'] == 'True/False' ? 'selected' : '' ?>>True or False</option>
                                    <option value="Identification" <?= $row['question_type'] == 'Identification' ? 'selected' : '' ?>>Identification</option>
                                    <option value="Enumeration" <?= $row['question_type'] == 'Enumeration' ? 'selected' : '' ?>>Enumeration</option>
                                    <option value="Fill in the Blanks" <?= $row['question_type'] == 'Fill in the Blanks' ? 'selected' : '' ?>>Fill in the Blanks</option>
                                    <option value="Essay" <?= $row['question_type'] == 'Essay' ? 'selected' : '' ?>>Essay</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="corrects[]" class="form-control" value="<?= htmlspecialchars($row['correct_option']) ?>">
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">Save All Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
