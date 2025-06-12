<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ees'; // Change to your actual DB name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT employee_num, full_name, branch, position, date_started, date_of_exam,
        score_1, score_2, score_3, score_4, score_5, score_6, score_7, score_8, score_9, score_10,
        average, status, submitted_at FROM employee";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>