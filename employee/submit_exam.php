<?php
session_start();

if (!isset($_SESSION["name"], $_SESSION["position"])) {
    die("Session expired or user not logged in.");
}

$full_name = $_SESSION["full_name"];
$branch = $_SESSION["branch"];
$position = $_SESSION["position"];
$date_started = $_SESSION["date_started"];
$date_of_exam = $_SESSION["date_of_exam"];

$host = 'localhost';
$db = 'ees';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$correct_answers = [
    "q1" => "Sequential execution",
];

$total_questions = count($correct_answers);
$score_count = 0;

foreach ($correct_answers as $q => $correct_answer) {
    if (isset($_POST[$q]) && $_POST[$q] === $correct_answer) {
        $score_count++;
    }
}

$percentage = ($score_count / $total_questions) * 100;

$pass_mark = 75;
$status = ($percentage >= $pass_mark) ? "Passed" : "Failed";

$sql = "UPDATE employee 
        SET exam_1 = ?, status = ? 
        WHERE full_name = ? AND branch = ? AND position = ? AND date_started = ? AND date_of_exam = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("dssssss", $percentage, $status, $full_name, $branch, $position, $date_started, $date_of_exam);
$stmt->execute();
$stmt->close();
$conn->close();

unset($_SESSION["start_time"]);
unset($_SESSION["exam_duration"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examination Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <p>Thank you for completing the examination. Your responses have been submitted successfully.</p>

              <a href="../index.php" class="btn btn-primary mt-4">Return to Home</a>
    </div>
</body>
</html>