<?php
// main file (e.g., dashboard.php)
$host = "localhost";
$dbname = "ees";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX to view scores
if (isset($_GET['view_scores']) && isset($_GET['id'])) {
  header('Content-Type: application/json');
  $id = (int)$_GET['id'];
  $stmt = $conn->prepare("SELECT employee_num, full_name, branch, position, date_started, date_of_exam, average, status,
    score_1, score_2, score_3, score_4, score_5, 
    score_6, score_7, score_8, score_9, score_10 
    FROM employee WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  echo json_encode($result);
  exit;
}

// Fetch stats
$total = $conn->query("SELECT COUNT(*) as count FROM employee")->fetch_assoc()['count'];
$passed = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Passed'")->fetch_assoc()['count'];
$failed = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Failed'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Pending'")->fetch_assoc()['count'];

// Search handling
$search = $_GET['search'] ?? '';
$search_query = '';
if ($search !== '') {
  $search_param = "%" . $conn->real_escape_string($search) . "%";
  $stmt = $conn->prepare("SELECT * FROM employee WHERE full_name LIKE ? OR employee_num LIKE ? ORDER BY submitted_at DESC");
  $stmt->bind_param("ss", $search_param, $search_param);
  $stmt->execute();
  $results = $stmt->get_result();
} else {
  $results = $conn->query("SELECT * FROM employee ORDER BY submitted_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Employee Examination Results</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-8">

    <div class="flex justify-between items-center p-4 border-b font-medium mb-4">
      <span class="fw-bold fs-3">Recent Examination Results</span>
      <form method="GET" class="flex space-x-2">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by Name or ID" class="px-4 py-2 border border-gray-300 rounded" />
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
      </form>
    </div>

    <div class="bg-white shadow rounded overflow-x-auto">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-700">
          <tr><th>NAME</th><th>POSITION</th><th>AVERAGE</th><th>STATUS</th><th>DATE</th><th>ACTION</th></tr>
        </thead>
        <tbody>
        <?php while($row = $results->fetch_assoc()): ?>
          <tr class="border-t">
            <td class="px-4 py-2"><?= htmlspecialchars($row['full_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['position']) ?></td>
            <td class="px-4 py-2"><?= $row['average'] ?? 'N/A' ?>%</td>
            <td class="px-4 py-2">
              <span class="<?= match($row['status']) {
                'Passed' => 'bg-green-100 text-green-700',
                'Failed' => 'bg-red-100 text-red-700',
                'Pending' => 'bg-yellow-100 text-yellow-700',
                default => 'bg-gray-100 text-gray-700'
              } ?> px-2 py-1 rounded text-xs"><?= $row['status'] ?></span>
            </td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['submitted_at']) ?></td>
            <td class="px-4 py-2"><button class="text-blue-600 underline" onclick="viewDetails(<?= $row['id'] ?>)">View Details</button></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- Modals -->
<div id="scoreModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
  <div class="bg-white w-full max-w-xl rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center border-b pb-3 mb-4">
      <h2 class="text-lg font-bold">Employee Examination Details</h2>
      <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
    </div>
    <div id="modalContent" class="space-y-2 text-sm"></div>
    <div class="mt-6 text-right"><button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded">Close</button></div>
  </div>
</div>

<div id="questionnaireModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 px-4">
  <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg p-6 max-h-[80vh] overflow-y-auto">
    <div class="flex justify-between items-center border-b pb-3 mb-4">
      <h2 class="text-lg font-bold">Questionnaire Details</h2>
      <button onclick="closeQuestionnaireModal()" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
    </div>
    <div id="questionnaireContent" class="space-y-4 text-sm text-gray-700"></div>
    <div class="mt-6 text-right"><button onclick="closeQuestionnaireModal()" class="bg-blue-600 text-white px-4 py-2 rounded">Close</button></div>
  </div>
</div>

<script>
let examTitles = {};

// Load exam titles from the server
fetch('exam_titles.php')
  .then(res => res.json())
  .then(data => {
    examTitles = data;
  });

function viewDetails(empId) {
  const modal = document.getElementById('scoreModal');
  const content = document.getElementById('modalContent');
  modal.classList.remove('hidden');
  content.innerHTML = 'Loading...';

  fetch(`?view_scores=1&id=${empId}`)
    .then(res => res.json())
    .then(data => {
      let scoresHtml = '';
      for (let i = 1; i <= 10; i++) {
        const score = data[`score_${i}`];
        if (score !== null && score !== '') {
          const title = examTitles[i] || `Exam ${i}`;
          scoresHtml += `
            <div class="flex justify-between items-center">
              <p><strong>${title}:</strong> ${score}</p>
              <button onclick="viewQuestionnaire('${data.employee_num}', ${i})" class="text-blue-600 underline text-sm">View Questionnaire</button>
            </div>`;
        }
      }

      content.innerHTML = `
        <p><strong>Employee Number:</strong> ${data.employee_num}</p>
        <p><strong>Full Name:</strong> ${data.full_name}</p>
        <p><strong>Branch:</strong> ${data.branch}</p>
        <p><strong>Position:</strong> ${data.position}</p>
        <p><strong>Date of Exam:</strong> ${data.date_of_exam}</p>
        <p><strong>Average:</strong> ${data.average}%</p>
        <p><strong>Status:</strong> ${data.status}</p>
        ${scoresHtml}
      `;
    });
}

function viewQuestionnaire(employeeNum, examIndex) {
  const modal = document.getElementById('questionnaireModal');
  const content = document.getElementById('questionnaireContent');
  modal.classList.remove('hidden');
  content.innerHTML = 'Loading...';

  fetch(`fetch_questionnaire.php?employee_num=${employeeNum}&exam_id=${examIndex}`)
    .then(res => res.text())
    .then(html => content.innerHTML = html)
    .catch(() => content.innerHTML = '<p class="text-red-500">Failed to load data.</p>');
}

function closeModal() {
  document.getElementById('scoreModal').classList.add('hidden');
}

function closeQuestionnaireModal() {
  document.getElementById('questionnaireModal').classList.add('hidden');
}
</script>

</body>
</html>
