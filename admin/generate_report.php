<?php
// Replace with your database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "your_database_name"; // Replace with your actual DB name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

$sql = "SELECT employee_num, full_name, branch, position, date_of_exam, average, status, submitted_at FROM employee ORDER BY submitted_at DESC";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
}

echo json_encode($data);
?>
