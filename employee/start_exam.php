<?php
session_start();

// Reset question index on first page load (non-POST request)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['current_question'] = 0;
    $_SESSION['answers'] = [];
}

// Validate session
if (!isset($_SESSION["full_name"], $_SESSION["start_time"], $_SESSION["exam_duration"])) {
    header("Location: employee_form.php");
    exit;
}

// Timer calculation
$remaining_time = ($_SESSION["start_time"] + $_SESSION["exam_duration"]) - time();
if ($remaining_time <= 0) {
    header("Location: submit_exam.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get active exam_id
$exam_result = $conn->query("SELECT exam_id FROM examinations WHERE status = 'Active'");
if ($exam_result && $exam_result->num_rows > 0) {
    $exam_row = $exam_result->fetch_assoc();
    $exam_id = $exam_row['exam_id'];
    $_SESSION['exam_id'] = $exam_id; // Store it in session if needed
} else {
    die("No active exam found.");
}

// Get total number of questions for the active exam
$total_q_result = $conn->query("SELECT COUNT(*) as total FROM question WHERE exam_id = '$exam_id'");
$total_q = $total_q_result->fetch_assoc()['total'];

// Initialize current question index
if (!isset($_SESSION['current_question'])) {
    $_SESSION['current_question'] = 0;
}

// Process submitted answer
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['answer'])) {
    $selected = $_POST['answer'];
    $_SESSION['answers'][$_SESSION['current_question']] = $selected;
    $_SESSION['current_question']++;

    if ($_SESSION['current_question'] >= $total_q) {
        header("Location: submit_exam.php");
        exit;
    }
}

// Fetch current question
$q_index = $_SESSION['current_question'];
$question_sql = "SELECT * FROM question WHERE exam_id = '$exam_id' LIMIT 1 OFFSET $q_index";
$question_result = $conn->query($question_sql);
$question = $question_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment Test</title>
    <style>
        body {
            background-color: #f9fafb;
            color: #111827;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            margin: 40px auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .user-info {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 3px solid #3b82f6;
        }
        .question {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .options label {
            display: block;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            cursor: pointer;
        }
        .options input[type="radio"] {
            margin-right: 10px;
        }
        button {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            float: right;
        }
        #timer {
            float: right;
            color: #2563eb;
            font-weight: bold;
        }
    </style>
    <script>
        let timeLeft = <?= $remaining_time ?>;
        function startTimer() {
            const timerDisplay = document.getElementById("timer");
            const form = document.getElementById("examForm");

            const interval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(interval);
                    alert("Time's up! Submitting your exam.");
                    form.submit();
                }
            }, 1000);
        }
        window.onload = startTimer;
    </script>
</head>
<body>
<div class="container">
    <div class="user-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION["full_name"]) ?></p>
        <p><strong>Position:</strong> <?= htmlspecialchars($_SESSION["position"]) ?></p>
        <p><strong>Time Remaining:</strong> <span id="timer"></span></p>
    </div>

    <form id="examForm" method="POST">
        <p class="question">
            Question <?= $q_index + 1 ?> of <?= $total_q ?>:<br>
            <?= htmlspecialchars($question['question_text']) ?>
        </p>
        <div class="options">
            <label><input type="radio" name="answer" value="A. <?= htmlspecialchars($question['option_a']) ?>" required> <?= htmlspecialchars($question['option_a']) ?></label>
            <label><input type="radio" name="answer" value="B. <?= htmlspecialchars($question['option_b']) ?>"> <?= htmlspecialchars($question['option_b']) ?></label>
            <label><input type="radio" name="answer" value="C. <?= htmlspecialchars($question['option_c']) ?>"> <?= htmlspecialchars($question['option_c']) ?></label>
            <label><input type="radio" name="answer" value="D. <?= htmlspecialchars($question['option_d']) ?>"> <?= htmlspecialchars($question['option_d']) ?></label>
        </div>
        <button type="submit">
            <?php
            if ($q_index + 1 === $total_q) {
                echo "Submit";
            } else {
                echo "Next";
            }
            ?>
        </button>
    </form>
</div>
</body>
</html>
