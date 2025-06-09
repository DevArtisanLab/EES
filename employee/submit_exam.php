<?php
session_start();

// Required session keys
$required_keys = ["employee_num", "full_name", "branch", "position", "date_started", "date_of_exam", "answers"];
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
$exam_id       = $_SESSION['exam_id'] ?? null;

// Database connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$exam_id) {
    die("Exam ID is missing.");
}

// Fetch correct answers
$correct_answers = [];
$stmt = $conn->prepare("SELECT id, correct_option FROM question WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $correct_answers[$row['id']] = strtoupper(trim($row['correct_option']));
}
$stmt->close();

// Score evaluation
$score_count = 0;
$total_questions = count($correct_answers);
$current_time = date('Y-m-d H:i:s');

// Insert answers and calculate score
foreach ($answers as $question_id => $selected_option) {
    $parsed_letter = strtoupper(substr(trim($selected_option), 0, 1));
    $is_correct = isset($correct_answers[$question_id]) && $correct_answers[$question_id] === $parsed_letter ? 1 : 0;
    $score_count += $is_correct;

    $stmt = $conn->prepare("INSERT INTO answers (employee_num, exam_id, selected_option, is_correct, answered_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sissi", $employee_num, $exam_id, $parsed_letter, $is_correct, $current_time);
    $stmt->execute();
    $stmt->close();
}

// Final score computation
$percentage = ($total_questions > 0) ? ($score_count / $total_questions) * 100 : 0;
$int_score = round($percentage); // For int columns in DB

// Determine which score column is empty
$score_column = null;
$query = "SELECT score_1, score_2, score_3, score_4, score_5 FROM employee WHERE employee_num = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_num);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

foreach ($row as $column => $value) {
    if (is_null($value)) {
        $score_column = $column;
        break;
    }
}
if (!$score_column) {
    // All score columns filled, overwrite score_1 or handle as needed
    $score_column = "score_1";
}

// Update employee record with new score
$update_score_sql = "UPDATE employee SET `$score_column` = ?, submitted_at = ? WHERE employee_num = ?";
$stmt = $conn->prepare($update_score_sql);
$stmt->bind_param("iss", $int_score, $current_time, $employee_num);
$stmt->execute();
$stmt->close();

// Now re-fetch all scores to calculate average
$stmt = $conn->prepare("SELECT score_1, score_2, score_3, score_4, score_5 FROM employee WHERE employee_num = ?");
$stmt->bind_param("s", $employee_num);
$stmt->execute();
$result = $stmt->get_result();
$scores_row = $result->fetch_assoc();
$stmt->close();

// Calculate average ignoring NULLs
$sum = 0;
$count = 0;
foreach ($scores_row as $score) {
    if ($score !== null) {
        $sum += $score;
        $count++;
    }
}
$average = ($count > 0) ? ($sum / $count) : 0;
$status = ($average >= 75) ? "Passed" : "Failed";

// Update status with average-based pass/fail
$stmt = $conn->prepare("UPDATE employee SET status = ? WHERE employee_num = ?");
$stmt->bind_param("ss", $status, $employee_num);
$stmt->execute();
$stmt->close();

// Clean up session
unset($_SESSION["answers"], $_SESSION["start_time"], $_SESSION["exam_duration"]);
$conn->close();
?>

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
    <p><strong>Score:</strong> <?= $int_score ?>%</p>
    <p><strong>Average Score:</strong> <?= round($average, 2) ?>%</p>
    <p><strong>Status:</strong> <?= $status ?></p>
    <a href="../index.php" class="btn btn-primary mt-4">Return to Home</a>
</div>
</body>
</html>
