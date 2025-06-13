<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ees'; // Use your actual DB name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Build dynamic WHERE clause from session filters
$where = [];

if (!empty($_SESSION['filter_position']) && $_SESSION['filter_position'] !== 'All') {
    $position = $conn->real_escape_string($_SESSION['filter_position']);
    $where[] = "position = '$position'";
}

if (!empty($_SESSION['filter_status']) && $_SESSION['filter_status'] !== 'All') {
    $status = $conn->real_escape_string($_SESSION['filter_status']);
    $where[] = "status = '$status'";
}

if (!empty($_SESSION['filter_time_period'])) {
    switch ($_SESSION['filter_time_period']) {
        case 'Today':
            $where[] = "DATE(date_of_exam) = CURDATE()";
            break;
        case 'This Week':
            $where[] = "YEARWEEK(date_of_exam, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'This Month':
            $where[] = "MONTH(date_of_exam) = MONTH(CURDATE()) AND YEAR(date_of_exam) = YEAR(CURDATE())";
            break;
    }
}

$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT employee_num, full_name, branch, position, date_started, date_of_exam,
        score_1, score_2, score_3, score_4, score_5, score_6, score_7, score_8, score_9, score_10,
        average, status, submitted_at
        FROM employee $where_sql
        ORDER BY submitted_at DESC";

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>
