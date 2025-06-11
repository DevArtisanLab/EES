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

// Score each answer
foreach ($answers as $question_id => $user_answer) {
    $full_answer = trim($user_answer);
    $type = strtolower($question_types[$question_id] ?? 'multiple choice');
    $correct_option = trim($correct_answers[$question_id] ?? '');
    $parsed_letter = strtoupper(substr($full_answer, 0, 1));
    $selected_option = '';
    $is_correct = 0;

    switch ($type) {
        case 'multiple choice':
        case 'true/false':
            $selected_option = $parsed_letter;
            $total_points_possible += 1;
            if (strtoupper($correct_option) === $selected_option) {
                $is_correct = 1;
                $score_count++;
            }
            break;

        case 'identification':
        case 'fill in the blanks':
            $selected_option = '';
            $total_points_possible += 1;
            if (strtolower($full_answer) === strtolower($correct_option)) {
                $is_correct = 1;
                $score_count++;
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
            $total_points_possible += count($correct_items); // Add all possible correct points
            break;

        default:
            $selected_option = '';
            break;
    }

    // Save each answer
    $stmt = $conn->prepare("INSERT INTO answers (employee_num, exam_id, question_id, selected_option, full_answer, is_correct, answered_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siissis", $employee_num, $exam_id, $question_id, $selected_option, $full_answer, $is_correct, $current_time);
    $stmt->execute();
    $stmt->close();
}

// Save raw score to DB
$raw_score = $score_count;
$score_column = "score_" . $exam_id;
$stmt = $conn->prepare("UPDATE employee SET `$score_column` = ?, submitted_at = ? WHERE employee_num = ?");
$stmt->bind_param("iss", $raw_score, $current_time, $employee_num);
$stmt->execute();
$stmt->close();

// Recalculate average
$total_percentage = 0;
$exams_taken = 0;

for ($i = 1; $i <= 10; $i++) {
    $stmt = $conn->prepare("SELECT id, correct_option, question_type FROM question WHERE exam_id = ?");
    $stmt->bind_param("i", $i);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_possible = 0;
    while ($row = $result->fetch_assoc()) {
        $type = strtolower($row['question_type']);
        if ($type === 'enumeration') {
            $items = array_filter(array_map('trim', explode(',', strtolower($row['correct_option']))));
            $total_possible += count($items);
        } else {
            $total_possible += 1;
        }
    }
    $stmt->close();

    if ($total_possible > 0) {
        $col = "score_" . $i;
        $stmt = $conn->prepare("SELECT `$col` FROM employee WHERE employee_num = ?");
        $stmt->bind_param("s", $employee_num);
        $stmt->execute();
        $result = $stmt->get_result();
        $score_row = $result->fetch_assoc();
        $stmt->close();

        $raw = $score_row[$col];
        if (!is_null($raw)) {
            $percentage = ($raw / $total_possible) * 100;
            $total_percentage += $percentage;
            $exams_taken++;
        }
    }
}

$average_percentage = ($exams_taken > 0) ? ($total_percentage / $exams_taken) : 0;
$average_rounded = round($average_percentage, 2);
$status = ($average_percentage >= 75) ? "Passed" : "Failed";

// Update final employee status
$stmt = $conn->prepare("UPDATE employee SET status = ?, average = ? WHERE employee_num = ?");
$stmt->bind_param("sds", $status, $average_rounded, $employee_num);
$stmt->execute();
$stmt->close();

// Clean session
unset($_SESSION["answers"], $_SESSION["start_time"], $_SESSION["exam_duration"]);
$conn->close();
?>


<!-- Confirmation Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Examination Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f9fafb;
        }
        .card {
            max-width: 600px;
            margin: 5% auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .check-icon {
            font-size: 40px;
            color: #28a745;
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
</body>
</html>
