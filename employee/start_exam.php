<?php
session_start();

// Reset question index on first page load (non-POST request)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['current_question'] = 0;
    $_SESSION['answers'] = [];
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get active exam_id and duration
$exam_result = $conn->query("SELECT exam_id, duration FROM examinations WHERE status = 'Active'");
if ($exam_result && $exam_result->num_rows > 0) {
    $exam_row = $exam_result->fetch_assoc();
    $exam_id = $exam_row['exam_id'];
    $exam_duration_minutes = $exam_row['duration'];

    $_SESSION['exam_id'] = $exam_id;
    $_SESSION['exam_duration'] = $exam_duration_minutes * 60;
} else {
    header("Location: ../index.php"); // Change this to your desired page
    exit();
}

// Validate session variables
if (!isset($_SESSION["full_name"], $_SESSION["start_time"], $_SESSION["exam_duration"])) {
    header("Location: employee_form.php");
    exit;
}

// Timer
$remaining_time = ($_SESSION["start_time"] + $_SESSION["exam_duration"]) - time();
if ($remaining_time <= 0) {
    header("Location: submit_exam.php");
    exit;
}

// Get total questions
$total_q_result = $conn->query("SELECT COUNT(*) as total FROM question WHERE exam_id = '$exam_id'");
$total_q = $total_q_result->fetch_assoc()['total'];

if (!isset($_SESSION['current_question'])) {
    $_SESSION['current_question'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['answer']) && isset($_POST['question_id'])) {
    $selected = $_POST['answer'];
    $question_id = $_POST['question_id'];

    $_SESSION['answers'][$question_id] = $selected;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            background-color: #f9fafb;
            color: #111827;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            margin: 40px auto;
            padding: 30px 20px;
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
            transition: background 0.2s;
        }
        .options label:hover {
            background-color: #f0f0f0;
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
            margin-top: 20px;
        }
        #timer {
            float: right;
            color: #2563eb;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px 10px;
                padding: 20px 15px;
            }
            .user-info p {
                font-size: 14px;
            }
            .question {
                font-size: 16px;
            }
            button {
                width: 100%;
                float: none;
                margin-top: 15px;
            }
            #timer {
                float: none;
                display: block;
                text-align: left;
                margin-top: 10px;
            }
        }
    </style>
    <script>
        let timeLeft = <?php echo $remaining_time; ?>;
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
            <input type="hidden" name="question_id" value="<?= htmlspecialchars($question['id']) ?>">
        </p>
        <div class="options">
        <?php
        $qtype = strtolower($question['question_type']);
        if (in_array($qtype, ['identification', 'enumeration', 'fill in the blanks', 'essay'])) {
            echo '<textarea name="answer" placeholder="Enter your answer here..." required></textarea>';
        } else {
            if (!empty($question['option_a']))
                echo '<label><input type="radio" name="answer" value="A. ' . htmlspecialchars($question['option_a']) . '" required> ' . htmlspecialchars($question['option_a']) . '</label>';
            if (!empty($question['option_b']))
                echo '<label><input type="radio" name="answer" value="B. ' . htmlspecialchars($question['option_b']) . '"> ' . htmlspecialchars($question['option_b']) . '</label>';
            if (!empty($question['option_c']))
                echo '<label><input type="radio" name="answer" value="C. ' . htmlspecialchars($question['option_c']) . '"> ' . htmlspecialchars($question['option_c']) . '</label>';
            if (!empty($question['option_d']))
                echo '<label><input type="radio" name="answer" value="D. ' . htmlspecialchars($question['option_d']) . '"> ' . htmlspecialchars($question['option_d']) . '</label>';
        }
        ?>
        </div>
        <button type="submit">
            <?= ($q_index + 1 === $total_q) ? "Submit" : "Next" ?>
        </button>
    </form>
</div>
</body>
</html>
