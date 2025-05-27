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
    $full_name = htmlspecialchars($_POST["full_name"]);
    $email = htmlspecialchars($_POST["email"]);
    $dob = $_POST["dob"];
    $phone = htmlspecialchars($_POST["phone"]);
    $education_level = $_POST["education_level"];
    $position = $_POST["position"];

    $_SESSION["name"] = $full_name;
    $_SESSION["email"] = $email;
    $_SESSION["dob"] = $dob;
    $_SESSION["phone"] = $phone;
    $_SESSION["education_level"] = $education_level;
    $_SESSION["position"] = $position;
    $_SESSION["start_time"] = time();
    $_SESSION["exam_duration"] = 1000;


    $stmt = $conn->prepare("INSERT INTO employee (full_name, email, dob, phone, education_level, position)
                            VALUES (:full_name, :email, :dob, :phone, :education_level, :position)");
    $stmt->bindParam(":full_name", $full_name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":dob", $dob);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":education_level", $education_level);
    $stmt->bindParam(":position", $position);
    $stmt->execute();

    header("Location: start_exam.php");
    exit;
} else {
    header("Location: application_form.php");
    exit;
}
?>
