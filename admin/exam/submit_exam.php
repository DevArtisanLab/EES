<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $position = $_POST['position'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $passing_score = $_POST['passing_score'];
    $status = $_POST['status'];

    // Example: store into database (assume connection is made)
    // $stmt = $pdo->prepare("INSERT INTO exams (...) VALUES (...)");
    // $stmt->execute([...]);

    echo "Examination '$title' created successfully!";
}
?>
