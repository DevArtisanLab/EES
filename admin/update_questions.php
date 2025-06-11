<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate POST data
if (
    !isset($_POST['exam_id']) ||
    !isset($_POST['question_ids']) ||
    !isset($_POST['questions']) ||
    !isset($_POST['types']) ||
    !isset($_POST['corrects'])
) {
    die("Error: Missing required form fields.");
}

// Fetch POST data
$exam_id = $_POST['exam_id'];
$ids = $_POST['question_ids'];
$questions = $_POST['questions'];
$types = $_POST['types'];
$corrects = $_POST['corrects'];

// Check array lengths are consistent
if (
    !is_array($ids) ||
    count($ids) !== count($questions) ||
    count($ids) !== count($types) ||
    count($ids) !== count($corrects)
) {
    die("Error: Mismatched data arrays.");
}

// Loop through all questions and update each one
for ($i = 0; $i < count($ids); $i++) {
    $id = $conn->real_escape_string($ids[$i]);
    $question = $conn->real_escape_string($questions[$i]);
    $type = $conn->real_escape_string($types[$i]);
    $correct = $conn->real_escape_string($corrects[$i]);

    $sql = "UPDATE question 
            SET question_text = '$question', 
                question_type = '$type', 
                correct_option = '$correct' 
            WHERE id = $id AND exam_id = $exam_id";

    if (!$conn->query($sql)) {
        echo "Error updating question ID $id: " . $conn->error;
    }
}

// Redirect back to the manage page after successful update
header("Location: admin_examinations.php?id=$exam_id");
exit();
?>
