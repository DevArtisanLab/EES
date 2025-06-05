<?php
// Increase session lifetime and start session
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);
session_start();

// REMOVE or comment this block in production when real session data is set.
if (!isset($_SESSION['answers'])) {
    $_SESSION['employee_num'];
    $_SESSION['full_name'];
    $_SESSION['branch'] ;
    $_SESSION['position'] ;
    $_SESSION['date_started'];
    $_SESSION['date_of_exam'] ;
    
}

// Required session keys
$required_keys = [
    "employee_num", "full_name", "branch", "position",
    "date_started", "date_of_exam", "answers"
];

// Check for missing session keys
$missing_keys = [];
foreach ($required_keys as $key) {
    if (!isset($_SESSION[$key])) {
        $missing_keys[] = $key;
    }
}
if (!empty($missing_keys)) {
    die("Session expired or missing: " . implode(', ', $missing_keys));
}

// Assign session values
$employee_num  = $_SESSION["employee_num"];
$full_name     = $_SESSION["full_name"];
$branch        = $_SESSION["branch"];
$position      = $_SESSION["position"];
$date_started  = $_SESSION["date_started"];
$date_of_exam  = $_SESSION["date_of_exam"];
$answers       = $_SESSION["answers"];

// Database connection info
$host = 'localhost';
$db   = 'ees';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Fetch correct answers for this exam
$correct_sql = "SELECT id, correct_option, exam_id FROM question";
$correct_result = $conn->query($correct_sql);

if (!$correct_result) {
    die("Failed to fetch questions: " . $conn->error);
}

$correct_answers = [];
$exam_id = null;

while ($row = $correct_result->fetch_assoc()) {
    $correct_answers[$row['id']] = $row['correct_option'];
    $exam_id = $row['exam_id']; // assuming all questions belong to one exam
}

// Step 2: Save answers & compute score
$score_count = 0;
$total_questions = count($correct_answers);
$current_time = date('Y-m-d H:i:s');

foreach ($answers as $question_id => $selected_option) {
    $parsed_letter = substr($selected_option, 0, 1);
    $is_correct = (isset($correct_answers[$question_id]) && $correct_answers[$question_id] === $parsed_letter) ? 1 : 0;
    if ($is_correct) $score_count++;

    $stmt = $conn->prepare("INSERT INTO answers (employee_num, exam_id,  selected_option, is_correct, answered_at) VALUES ( ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    // Bind parameters:
    // employee_num (int), exam_id (int),  selected_option (string), is_correct (int), answered_at (string)
    $stmt->bind_param("iisis", $employee_num, $exam_id, $selected_option, $is_correct, $current_time);
    $stmt->execute();
    $stmt->close();
}

// Step 3: Calculate final score and status
$percentage = ($total_questions > 0) ? ($score_count / $total_questions) * 100 : 0;
$pass_mark = 75;
$status = ($percentage >= $pass_mark) ? "Passed" : "Failed";

// Step 4: Update employee record
$update_sql = "UPDATE employee SET score = ?, status = ? WHERE employee_num = ?";
$stmt = $conn->prepare($update_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("dsi", $percentage, $status, $employee_num);
$stmt->execute();
$stmt->close();

// Optional: Clear answers from session after processing
unset($_SESSION["answers"]);
unset($_SESSION["start_time"]);
unset($_SESSION["exam_duration"]);

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
        <p>Thank you for completing the examination.<br />
        <a href="../index.php" class="btn btn-primary mt-4">Return to Home</a>
    </div>
</body>
</html>
