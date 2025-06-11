<?php
$exam_id = $_GET['id'];
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
</head>
<body>
<div class="container mt-5">
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
                        <select name="types[]" class="form-control">
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
        <button type="submit" class="btn btn-success">Save All Changes</button>
    </form>
</div>
</body>
</html>
