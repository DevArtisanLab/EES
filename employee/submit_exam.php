<?php
session_start();

// Required session keys
$required_keys = ["employee_num", "full_name", "branch", "position", "date_started", "date_of_exam", "answers", "exam_id"];
foreach ($required_keys as $key) {
    if (!isset($_SESSION[$key])) {
        die("Missing session data: $key");
    }
}

// Session variables
$employee_num  = $_SESSION["employee_num"];
$full_name     = $_SESSION["full_name"];
$branch        = $_SESSION["branch"];
$position      = $_SESSION["position"];
$date_started  = $_SESSION["date_started"];
$date_of_exam  = $_SESSION["date_of_exam"];
$answers       = $_SESSION["answers"];
$exam_id       = (int)$_SESSION['exam_id'];

if ($exam_id < 1 || $exam_id > 10) {
    die("Invalid exam ID. Only values 1–10 are allowed.");
}

// DB connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch correct answers and types
$correct_answers = [];
$question_types = [];
$stmt = $conn->prepare("SELECT id, correct_option, question_type FROM question WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $qid = $row['id'];
    $correct_answers[$qid] = $row['correct_option'];
    $question_types[$qid] = strtolower($row['question_type']);
}
$stmt->close();

$score_count = 0;
$total_points_possible = 0;
$current_time = date('Y-m-d H:i:s');
$has_new_answers = false; // Track if new answers were saved

// Score each answer
foreach ($answers as $question_id => $user_answer) {
    // Prevent duplicate submissions
    $check_stmt = $conn->prepare("SELECT 1 FROM answers WHERE employee_num = ? AND question_id = ?");
    $check_stmt->bind_param("si", $employee_num, $question_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        continue; // Already answered, skip
    }
    $check_stmt->close();

    $has_new_answers = true; // Mark that we have a new answer

    $full_answer = trim($user_answer);
    $type = strtolower($question_types[$question_id] ?? 'multiple choice');
    $correct_option = trim($correct_answers[$question_id] ?? '');
    $parsed_letter = strtoupper(substr($full_answer, 0, 1));
    $selected_option = '';
    $is_correct = null; // default for safety

    switch ($type) {
        case 'multiple choice':
        case 'true/false':
            $selected_option = $parsed_letter;
            $total_points_possible += 1;
            if (strtoupper($correct_option) === $selected_option) {
                $is_correct = 1;
                $score_count++;
            } else {
                $is_correct = 0;
            }
            break;

        case 'identification':
        case 'fill in the blanks':
            $selected_option = '';
            $total_points_possible += 1;
            if (strtolower($full_answer) === strtolower($correct_option)) {
                $is_correct = 1;
                $score_count++;
            } else {
                $is_correct = 0;
            }
            break;

        case 'enumeration':
            $selected_option = '';
            $user_items = array_filter(array_map('trim', explode(',', strtolower($full_answer))));
            $correct_items = array_filter(array_map('trim', explode(',', strtolower($correct_option))));
            $user_items = array_unique($user_items);
            $correct_items = array_unique($correct_items);

            $matches = array_intersect($user_items, $correct_items);
            $match_count = count($matches);

            $is_correct = ($match_count > 0) ? 1 : 0;
            $score_count += $match_count;
            $total_points_possible += count($correct_items);
            break;

        case 'essay':
            $selected_option = '';
            $is_correct = null; // manually scored later
            break;

        default:
            $selected_option = '';
            $is_correct = 0;
            break;
    }

    // Save each answer
    $stmt = $conn->prepare("INSERT INTO answers (employee_num, exam_id, question_id, selected_option, full_answer, is_correct, answered_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (is_null($is_correct)) {
        $stmt->bind_param("siissss", $employee_num, $exam_id, $question_id, $selected_option, $full_answer, $is_correct, $current_time);
    } else {
        $stmt->bind_param("siissis", $employee_num, $exam_id, $question_id, $selected_option, $full_answer, $is_correct, $current_time);
    }

    $stmt->execute();
    $stmt->close();
}

// Save raw score to DB if there are new answers
if ($has_new_answers) {
    $raw_score = $score_count;
    $score_column = "score_" . $exam_id;
    $stmt = $conn->prepare("UPDATE employee SET `$score_column` = ?, submitted_at = ? WHERE employee_num = ?");
    $stmt->bind_param("iss", $raw_score, $current_time, $employee_num);
    $stmt->execute();
    $stmt->close();
}

// Clean session
unset($_SESSION["answers"], $_SESSION["start_time"], $_SESSION["exam_duration"]);
$conn->close();
?>
<!-- Confirmation Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 15px;
        }
        .card {
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 100%;
        }
        .check-icon {
            font-size: 3rem;
            color: #10b981;
        }
    </style>
</head>
<body>
<div class="card text-center">
    <div class="check-icon mb-3">✔️</div>
    <h3>Examination Complete!</h3>
    <p>Thank you for completing the examination.</p>
    <a href="../index.php" class="btn btn-primary mt-4">Return to Home</a>
</div>

<!-- Bootstrap JS (optional for buttons or interactions) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
