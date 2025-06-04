<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get exam_id from URL
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

if ($exam_id <= 0) {
    die("Invalid exam ID.");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    for ($i = 1; $i <= 25; $i++) {
        $question = isset($_POST["question_$i"]) ? trim($_POST["question_$i"]) : '';
        $type     = isset($_POST["type_$i"]) ? $_POST["type_$i"] : '';
        $correct  = isset($_POST["correct_$i"]) ? $_POST["correct_$i"] : '';

        $optionA  = isset($_POST["option_{$i}_A"]) ? $_POST["option_{$i}_A"] : '';
        $optionB  = isset($_POST["option_{$i}_B"]) ? $_POST["option_{$i}_B"] : '';
        $optionC  = isset($_POST["option_{$i}_C"]) ? $_POST["option_{$i}_C"] : '';
        $optionD  = isset($_POST["option_{$i}_D"]) ? $_POST["option_{$i}_D"] : '';

        if ($question === '' || $type === '' || $correct === '') {
            // Skip this question if required fields are missing
            continue;
        }

        $stmt = $conn->prepare("
            INSERT INTO question (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssss", $exam_id, $question, $type, $optionA, $optionB, $optionC, $optionD, $correct);
        $stmt->execute();
        $stmt->close();
    }

    echo "<div class='alert alert-success'>Questions saved successfully!</div>";
    header("Location: admin_dashboard.php");
    exit;
}

?>
<?php include 'sidebar.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Examination Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h3 class="mb-4">Add Examination Questions (Exam ID: <?= htmlspecialchars($exam_id) ?>)</h3>
    <form method="POST">
        <?php for ($i = 1; $i <= 25; $i++): ?>
    <div class="card mb-3">
        <div class="card-header">Question <?= $i ?></div>
        <div class="card-body">
            <div class="mb-3">
                <label>Question Text</label>
                <textarea class="form-control" name="question_<?= $i ?>" required></textarea>
            </div>
            <div class="mb-3">
                <label>Question Type</label>
                <select class="form-select question-type" name="type_<?= $i ?>" data-index="<?= $i ?>">
                    <option value="Multiple Choice" selected>Multiple Choice</option>
                    <option value="True/False">True/False</option>
                </select>
            </div>

            <div class="answer-options" id="options_<?= $i ?>">
                <div class="form-check d-flex align-items-center mb-2">
                    <input class="form-check-input me-2" type="radio" name="correct_<?= $i ?>" value="A" required>
                    <label class="form-check-label me-2">A</label>
                    <input type="text"  name="option_<?= $i ?>_A" class="form-control" placeholder="Option A" required>
                </div>
                <div class="form-check d-flex align-items-center mb-2">
                    <input class="form-check-input me-2" type="radio" name="correct_<?= $i ?>" value="B">
                    <label class="form-check-label me-2">B</label>
                    <input type="text"  name="option_<?= $i ?>_B" class="form-control" placeholder="Option B" required>
                </div>
                <div class="form-check d-flex align-items-center mb-2 option-c">
                    <input class="form-check-input me-2" type="radio" name="correct_<?= $i ?>" value="C">
                    <label class="form-check-label me-2">C</label>
                    <input type="text"  name="option_<?= $i ?>_C" class="form-control" placeholder="Option C" required>
                </div>
                <div class="form-check d-flex align-items-center mb-2 option-d">
                    <input class="form-check-input me-2" type="radio" name="correct_<?= $i ?>" value="D">
                    <label class="form-check-label me-2">D</label>
                    <input type="text" name="option_<?= $i ?>_D"  class="form-control" placeholder="Option D" required>
                </div>
            </div>
        </div>
    </div>
<?php endfor; ?>

        <div class="d-flex justify-content-between">
            <a href="admin_createexam.php" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Questions</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.question-type').forEach(select => {
        const index = select.dataset.index;
        const container = document.getElementById('options_' + index);
        if (!container) return;

        const optionCWrapper = container.querySelector('.option-c');
        const optionDWrapper = container.querySelector('.option-d');
        const optionAInput = container.querySelector('input[name="option_' + index + '_A"]');
        const optionBInput = container.querySelector('input[name="option_' + index + '_B"]');

        const updateOptions = () => {
            const isTF = select.value === 'True/False';

            if (optionCWrapper) optionCWrapper.style.display = isTF ? 'none' : 'flex';
            if (optionDWrapper) optionDWrapper.style.display = isTF ? 'none' : 'flex';

            if (optionAInput && optionBInput) {
                if (isTF) {
                    optionAInput.value = 'True';
                    optionBInput.value = 'False';
                } else {
                    optionAInput.value = '';
                    optionBInput.value = '';
                }
            }
        };

        select.addEventListener('change', updateOptions);
        updateOptions(); // Run once at page load
    });
});

</script>




</body>
</html>
