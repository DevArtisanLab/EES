<?php
// exam_titles.php
$host = "localhost";
$dbname = "ees";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Database connection error");
}

// Get exam_id to title mapping
$result = $conn->query("SELECT exam_id, title FROM examinations");
$titles = [];

while ($row = $result->fetch_assoc()) {
  $titles[$row['exam_id']] = $row['title'];
}

header('Content-Type: application/json');
echo json_encode($titles);
