<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if ($exam_id <= 0) {
    die("Invalid exam ID.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $questions = $_POST['question'] ?? [];
    $types = $_POST['type'] ?? [];
    $corrects = $_POST['correct'] ?? [];
    $optionsA = $_POST['option_A'] ?? [];
    $optionsB = $_POST['option_B'] ?? [];
    $optionsC = $_POST['option_C'] ?? [];
    $optionsD = $_POST['option_D'] ?? [];

    for ($i = 0; $i < count($questions); $i++) {
        $question = trim($questions[$i]);
        $type = $types[$i] ?? '';
        $correct = is_array($corrects) ? trim($corrects[$i] ?? '') : trim($corrects ?? '');

        if ($question === '' || $type === '') continue;

        $requiresOptions = in_array($type, ['Multiple Choice', 'True/False']);
        $requiresCorrect = ($type !== 'Essay');

        if ($requiresCorrect && $correct === '') continue;

        $optionA = $optionsA[$i] ?? null;
        $optionB = $optionsB[$i] ?? null;
        $optionC = $optionsC[$i] ?? null;
        $optionD = $optionsD[$i] ?? null;

        if ($requiresOptions) {
            $stmt = $conn->prepare("INSERT INTO question (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $exam_id, $question, $type, $optionA, $optionB, $optionC, $optionD, $correct);
        } elseif ($requiresCorrect) {
            $stmt = $conn->prepare("INSERT INTO question (exam_id, question_text, question_type, correct_option) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $exam_id, $question, $type, $correct);
        } else {
            $stmt = $conn->prepare("INSERT INTO question (exam_id, question_text, question_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $exam_id, $question, $type);
        }

        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_examinations.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Examination Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .d-flex {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #fff;
            border-right: 1px solid #e5e5e5;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        .form-label {
            font-weight: 500;
        }
        .card-header {
            background-color: #eef2ff;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h3 class="mb-4">Add Examination Questions</h3>
        <h5 class="mb-4 text-muted" hidden>Examination ID: <?= htmlspecialchars($exam_id) ?></h5>
        <form method="POST">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="card mb-3">
                <div class="card-header">Question <?= $i + 1 ?></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea class="form-control" name="question[]"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question Type</label>
                        <select class="form-select question-type" name="type[]" data-index="<?= $i ?>">
                            <option value="">-- Select Type --</option>
                            <option value="Multiple Choice">Multiple Choice</option>
                            <option value="True/False">True/False</option>
                            <option value="Identification">Identification</option>
                            <option value="Enumeration">Enumeration</option>
                            <option value="Fill in the Blanks">Fill in the Blanks</option>
                            <option value="Essay">Essay</option>
                        </select>
                    </div>

                    <div class="answer-options" id="options_<?= $i ?>">
                        <div class="form-check d-flex align-items-center mb-2">
                            <input class="form-check-input me-2" type="radio" name="correct[<?= $i ?>]" value="A">
                            <label class="form-check-label me-2">A</label>
                            <input type="text" name="option_A[]" class="form-control" placeholder="Option A">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2">
                            <input class="form-check-input me-2" type="radio" name="correct[<?= $i ?>]" value="B">
                            <label class="form-check-label me-2">B</label>
                            <input type="text" name="option_B[]" class="form-control" placeholder="Option B">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2 option-c">
                            <input class="form-check-input me-2" type="radio" name="correct[<?= $i ?>]" value="C">
                            <label class="form-check-label me-2">C</label>
                            <input type="text" name="option_C[]" class="form-control" placeholder="Option C">
                        </div>
                        <div class="form-check d-flex align-items-center mb-2 option-d">
                            <input class="form-check-input me-2" type="radio" name="correct[<?= $i ?>]" value="D">
                            <label class="form-check-label me-2">D</label>
                            <input type="text" name="option_D[]" class="form-control" placeholder="Option D">
                        </div>
                    </div>

                    <div class="form-group mt-3" id="open_answer_<?= $i ?>" style="display: none;">
                        <label class="form-label">Correct Answer</label>
                        <textarea class="form-control" name="correct[]"></textarea>
                        <small class="form-text text-muted">For Enumeration, separate answers with commas.</small>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.question-type').forEach(select => {
        const index = select.dataset.index;
        const container = document.getElementById('options_' + index);
        const openAnswer = document.getElementById('open_answer_' + index);
        const optionCWrapper = container.querySelector('.option-c');
        const optionDWrapper = container.querySelector('.option-d');
        const optionAInput = container.querySelector('input[name="option_A[]"]');
        const optionBInput = container.querySelector('input[name="option_B[]"]');

        const updateOptions = () => {
            const type = select.value;
            const isMC = type === 'Multiple Choice';
            const isTF = type === 'True/False';
            const isOpenAnswer = ['Identification', 'Enumeration', 'Fill in the Blanks'].includes(type);
            const isEssay = type === 'Essay';

            container.style.display = (isMC || isTF) ? 'block' : 'none';
            openAnswer.style.display = isOpenAnswer ? 'block' : (isEssay ? 'none' : 'none');

            if (optionCWrapper) optionCWrapper.style.display = isTF ? 'none' : 'flex';
            if (optionDWrapper) optionDWrapper.style.display = isTF ? 'none' : 'flex';

            if (optionAInput && optionBInput) {
                if (isTF) {
                    optionAInput.value = 'True';
                    optionBInput.value = 'False';
                } else if (isMC) {
                    optionAInput.value = '';
                    optionBInput.value = '';
                }
            }
        };

        select.addEventListener('change', updateOptions);
        updateOptions();
    });
});
</script>
</body>
</html>
