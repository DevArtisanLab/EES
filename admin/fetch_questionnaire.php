<?php
$host = "localhost";
$dbname = "ees";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Database error");
}

$employee_num = $_GET['employee_num'] ?? '';
$exam_id = (int)($_GET['exam_id'] ?? 0);

if (!$employee_num || !$exam_id) {
  exit("Invalid request");
}

// ✅ Get exam title from examinations table (moved to top)
$examTitleQuery = $conn->prepare("SELECT title FROM examinations WHERE exam_id = ?");
$examTitleQuery->bind_param("i", $exam_id);
$examTitleQuery->execute();
$examResult = $examTitleQuery->get_result();

if ($examRow = $examResult->fetch_assoc()) {
  $examTitle = htmlspecialchars($examRow['title']);
  echo "<div class='mt-4 mb-3'><h6 style='color: #000; font-weight: 600; text-align: left;'>Exam Title: $examTitle</h6></div>";

}

// Get questions and answers
$sql = "
  SELECT 
    q.question_text, 
    q.correct_option, 
    q.question_type,
    q.option_a, q.option_b, q.option_c, q.option_d,
    a.full_answer, 
    a.selected_option, 
    a.is_correct
  FROM question q
  LEFT JOIN answers a 
    ON q.id = a.question_id 
    AND a.employee_num = ?
    AND a.exam_id = ?
  WHERE q.exam_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $employee_num, $exam_id, $exam_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo "<p>No questionnaire data found.</p>";
  exit;
}

$questionNo = 1;

while ($row = $res->fetch_assoc()) {
  $question = htmlspecialchars($row['question_text']);
  $type = strtolower($row['question_type']);
  $correct = trim($row['correct_option']);
  $isCorrect = $row['is_correct'];

  // Determine the employee's answer
  $employeeAnswer = 'No answer';
  if (!empty($row['full_answer']) && in_array($type, ['essay', 'identification', 'enumeration', 'fill-in-the-blanks'])) {
    $employeeAnswer = nl2br(htmlspecialchars($row['full_answer']));
  } elseif (!empty($row['selected_option'])) {
    $employeeAnswer = htmlspecialchars($row['selected_option']);
  }

  // Show options if question is MCQ or True/False
  $optionsHtml = '';
  if (in_array($type, ['multiple choice', 'true or false'])) {
    $optionsHtml = "<ul class='list-unstyled ms-3'>";
    if (!empty($row['option_a'])) $optionsHtml .= "<li><strong>A:</strong> " . htmlspecialchars($row['option_a']) . "</li>";
    if (!empty($row['option_b'])) $optionsHtml .= "<li><strong>B:</strong> " . htmlspecialchars($row['option_b']) . "</li>";
    if (!empty($row['option_c'])) $optionsHtml .= "<li><strong>C:</strong> " . htmlspecialchars($row['option_c']) . "</li>";
    if (!empty($row['option_d'])) $optionsHtml .= "<li><strong>D:</strong> " . htmlspecialchars($row['option_d']) . "</li>";
    $optionsHtml .= "</ul>";
  }

  // Handle status output
  $statusHtml = "<span class='text-muted'>Not yet graded</span>";
  if (is_numeric($isCorrect)) {
    $statusHtml = ($isCorrect >= 0.5)
      ? "<span class='text-success'>Correct</span>"
      : "<span class='text-danger'>Incorrect</span>";
  }

  // Output the question block
  echo "<div class='border p-3 rounded mb-4'>
    <p><strong>Question $questionNo:</strong> $question</p>
    $optionsHtml
    <p><strong>Your Answer:</strong> $employeeAnswer</p>";

  if (!empty($correct)) {
    echo "<p><strong>Correct Answer:</strong> " . htmlspecialchars($correct) . "</p>";
  }

  echo "<p><strong>Status:</strong> $statusHtml</p>
  </div>";

  $questionNo++;
}

$conn->close();
?>
