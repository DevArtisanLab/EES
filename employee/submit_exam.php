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

// Validate exam_id range
if ($exam_id < 1 || $exam_id > 10) {
    die("Invalid exam ID. Only values 1–10 are allowed.");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch correct answers for this exam
$correct_answers = [];
$stmt = $conn->prepare("SELECT id, correct_option FROM question WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $correct_answers[$row['id']] = strtoupper(trim($row['correct_option']));
}
$stmt->close();

$score_count = 0;
$total_questions = count($correct_answers);
$current_time = date('Y-m-d H:i:s');

// Insert answers and count score
foreach ($answers as $question_id => $selected_option) {
    $parsed_letter = strtoupper(substr(trim($selected_option), 0, 1));
    $is_correct = isset($correct_answers[$question_id]) && $correct_answers[$question_id] === $parsed_letter ? 1 : 0;
    $score_count += $is_correct;

    $stmt = $conn->prepare("INSERT INTO answers (employee_num, exam_id, selected_option, is_correct, answered_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sissi", $employee_num, $exam_id, $parsed_letter, $is_correct, $current_time);
    $stmt->execute();
    $stmt->close();
}

// Save raw score
$raw_score = $score_count;

// Save raw score to specific column
$score_column = "score_" . $exam_id;
$update_score_sql = "UPDATE employee SET `$score_column` = ?, submitted_at = ? WHERE employee_num = ?";
$stmt = $conn->prepare($update_score_sql);
$stmt->bind_param("iss", $raw_score, $current_time, $employee_num);
$stmt->execute();
$stmt->close();

// Calculate average percentage score
$total_percentage = 0;
$exams_taken = 0;

for ($i = 1; $i <= 10; $i++) {
    // Get total questions for each exam
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM question WHERE exam_id = ?");
    $stmt->bind_param("i", $i);
    $stmt->execute();
    $result = $stmt->get_result();
    $question_row = $result->fetch_assoc();
    $stmt->close();

    $total_qs = (int)$question_row['total'];

    if ($total_qs > 0) {
        // Get employee raw score
        $col = "score_$i";
        $stmt = $conn->prepare("SELECT `$col` FROM employee WHERE employee_num = ?");
        $stmt->bind_param("s", $employee_num);
        $stmt->execute();
        $result = $stmt->get_result();
        $score_row = $result->fetch_assoc();
        $stmt->close();

        $raw = $score_row[$col];
        if (!is_null($raw)) {
            $percentage = ($raw / $total_qs) * 100;
            $total_percentage += $percentage;
            $exams_taken++;
        }
    }
}

// Compute average percentage
$average_percentage = ($exams_taken > 0) ? ($total_percentage / $exams_taken) : 0;
$average_rounded = round($average_percentage, 2);

// Status: passed if average >= 75
$status = ($average_percentage >= 75) ? "Passed" : "Failed";

// Update employee average and status
$stmt = $conn->prepare("UPDATE employee SET status = ?, average = ? WHERE employee_num = ?");
$stmt->bind_param("sds", $status, $average_rounded, $employee_num);
$stmt->execute();
$stmt->close();

// Clean session
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
    <a href="../index.php" class="btn btn-primary mt-4">Return to Home</a>
</div>
</body>
</html>
