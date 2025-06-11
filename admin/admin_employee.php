<?php
// DB connection
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
  $stmt = $conn->prepare("SELECT employee_num, full_name, branch, position, date_started, date_of_exam, average, 
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">
  <div class="flex min-h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Employee Examination Results</h1>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Total Employee</div>
          <div class="text-2xl font-bold"><?= $total ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Passed</div>
          <div class="text-2xl font-bold"><?= $passed ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Failed</div>
          <div class="text-2xl font-bold"><?= $failed ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Pending</div>
          <div class="text-2xl font-bold"><?= $pending ?></div>
        </div>
      </div>

      <!-- Title and Search -->
      <div class="flex justify-between items-center p-4 border-b font-medium mb-4">
        <span class="text-base">Recent Examination Results</span>
        <form method="GET" class="flex space-x-2">
          <input type="text" name="search" placeholder="Search by Name or ID"
            value="<?= htmlspecialchars($search) ?>"
            class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
        </form>
      </div>

      <div class="bg-white shadow rounded">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700">
              <tr>
                <th class="px-4 py-2">NAME</th>
                <th class="px-4 py-2">POSITION</th>
                <th class="px-4 py-2">AVERAGE</th>
                <th class="px-4 py-2">STATUS</th>
                <th class="px-4 py-2">DATE</th>
                <th class="px-4 py-2">ACTION</th>
              </tr>
            </thead>
            <tbody class="text-gray-700">
              <?php while($row = $results->fetch_assoc()): ?>
              <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($row['full_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['position']) ?></td>
                <td class="px-4 py-2"><?= $row['average'] !== null ? $row['average'].'%' : 'N/A' ?></td>
                <td class="px-4 py-2">
                  <?php
                    $status = $row['status'];
                    $statusClass = match ($status) {
                      'Passed' => 'bg-green-100 text-green-700',
                      'Failed' => 'bg-red-100 text-red-700',
                      'Pending' => 'bg-yellow-100 text-yellow-700',
                      default => 'bg-gray-100 text-gray-700',
                    };
                  ?>
                  <span class="<?= $statusClass ?> px-2 py-1 rounded text-xs"><?= $status ?></span>
                </td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['submitted_at']) ?></td>
                <td class="px-4 py-2">
                  <button class="text-blue-600 underline" onclick="viewDetails(<?= $row['id'] ?>)">View Details</button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal -->
  <div id="scoreModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg max-w-lg w-full mx-auto mt-24">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Employee Scores</h2>
        <button onclick="closeModal()" class="text-gray-600 hover:text-red-600 text-2xl">&times;</button>
      </div>
      <div id="modalContent" class="space-y-2 text-sm text-gray-700">
        <!-- Dynamic content -->
      </div>
      <div class="mt-4 text-right">
        <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Close</button>
      </div>
    </div>
  </div>

  <script>
    function viewDetails(empId) {
      document.getElementById('scoreModal').classList.remove('hidden');
      document.getElementById('modalContent').innerHTML = '<p>Loading...</p>';

      fetch(`?view_scores=1&id=${empId}`)
        .then(res => res.json())
        .then(data => {
          const content = document.getElementById('modalContent');
          if (!data) {
            content.innerHTML = '<p>No score data found.</p>';
            return;
          }

          let scoresHtml = '';
          for (let i = 1; i <= 10; i++) {
            const key = `score_${i}`;
            const score = data[key];
            if (score !== null && score !== '') {
              scoresHtml += `<p><strong>Score ${i}:</strong> ${score}</p>`;
            }
          }

          content.innerHTML = `
            <p><strong>Employee Number:</strong> ${data.employee_num ?? 'N/A'}</p>
            <p><strong>Full Name:</strong> ${data.full_name ?? 'N/A'}</p>
            <p><strong>Branch:</strong> ${data.branch ?? 'N/A'}</p>
            <p><strong>Position:</strong> ${data.position ?? 'N/A'}</p>
            <p><strong>Date Started:</strong> ${data.date_started ?? 'N/A'}</p>
            <p class="mt-2"><strong>Average:</strong> ${data.average ?? 'N/A'}%</p>
            ${scoresHtml || '<p>No individual scores available.</p>'}
          `;
        })
        .catch(err => {
          document.getElementById('modalContent').innerHTML = '<p>Error loading data.</p>';
          console.error(err);
        });
    }

    function closeModal() {
      document.getElementById('scoreModal').classList.add('hidden');
    }
  </script>
</body>
</html>
