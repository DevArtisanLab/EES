<?php
session_start();

$host = "localhost";
$dbname = "ees";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $full_name = htmlspecialchars($_POST["full_name"]);
    $branch = htmlspecialchars($_POST["branch"]);
    $position = htmlspecialchars($_POST["position"]);
    $date_started = $_POST["date_started"];
    $date_of_exam = $_POST["date_of_exam"] ?? date('Y-m-d'); // fallback to today's date


    // Store in session
    $_SESSION["full_name"] = $full_name;
    $_SESSION["branch"] = $branch;
    $_SESSION["position"] = $position;
    $_SESSION["date_started"] = $date_started;
    $_SESSION["date_of_exam"] = $date_of_exam;

    // Start exam session data
    $_SESSION["start_time"] = time();
    $_SESSION["exam_duration"] = 1000; // in seconds

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO employee 
        (full_name, branch, position, date_started, date_of_exam, submitted_at) 
        VALUES (:full_name, :branch, :position, :date_started, :date_of_exam, NOW())");

    $stmt->bindParam(":full_name", $full_name);
    $stmt->bindParam(":branch", $branch);
    $stmt->bindParam(":position", $position);
    $stmt->bindParam(":date_started", $date_started);
    $stmt->bindParam(":date_of_exam", $date_of_exam);

    $stmt->execute();

    header("Location: /np/ees/employee/start_exam.php");
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
?>
