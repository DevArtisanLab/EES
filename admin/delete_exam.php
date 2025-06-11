<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $exam_id = (int) $_GET['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete questions first
        $delete_questions_sql = "DELETE FROM question WHERE exam_id = $exam_id";
        if (!$conn->query($delete_questions_sql)) {
            throw new Exception("Error deleting questions: " . $conn->error);
        }

        // Then delete the exam
        $delete_exam_sql = "DELETE FROM examinations WHERE exam_id = $exam_id";
        if (!$conn->query($delete_exam_sql)) {
            throw new Exception("Error deleting exam: " . $conn->error);
        }

        // If both deletions succeed, commit
        $conn->commit();

        header("Location: admin_examinations.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
