<?php
require __DIR__ . '/../vendor/autoload.php'; // Adjust this path if not inside /admin/

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Check file upload
if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $exam_inserted = false;
    $exam_id = null;

    for ($i = 1; $i < count($rows); $i++) {
        [
            $title,
            $position,
            $duration,
            $description,
            $passing_score,
            $status,
            $question_text,
            $question_type,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_option
        ] = $rows[$i];

        if (!$exam_inserted) {
            $title = $conn->real_escape_string($title);
            $position = $conn->real_escape_string($position);
            $description = $conn->real_escape_string($description);
            $status = $conn->real_escape_string($status);
            $duration = (int)$duration;
            $passing_score = (int)$passing_score;
            $created = date('Y-m-d H:i:s');

            $insert_exam = "INSERT INTO examinations (title, position, duration, description, passing_score, status, created)
                            VALUES ('$title', '$position', $duration, '$description', $passing_score, '$status', '$created')";

            if ($conn->query($insert_exam)) {
                $exam_id = $conn->insert_id;
                $exam_inserted = true;
            } else {
                die("Failed to insert exam: " . $conn->error);
            }
        }

        if ($exam_id) {
            $question_text = $conn->real_escape_string($question_text);
            $question_type = $conn->real_escape_string($question_type);
            $option_a = $conn->real_escape_string($option_a);
            $option_b = $conn->real_escape_string($option_b);
            $option_c = $conn->real_escape_string($option_c);
            $option_d = $conn->real_escape_string($option_d);
            $correct_option = $conn->real_escape_string($correct_option);
            $created_at = date('Y-m-d H:i:s');

            $insert_question = "INSERT INTO question (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option, created_at)
                                VALUES ($exam_id, '$question_text', '$question_type', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_option', '$created_at')";

            if (!$conn->query($insert_question)) {
                echo "Failed to insert question: " . $conn->error;
            }
        }
    }

    $conn->close();
    header("Location: admin_examinations.php?import=success");
    exit();
} else {
    echo "Upload error: " . $_FILES['excel_file']['error'];
}
?>
