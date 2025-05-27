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

// Fetch stats
$total = $conn->query("SELECT COUNT(*) as count FROM employee")->fetch_assoc()['count'];
$passed = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Passed'")->fetch_assoc()['count'];
$failed = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Failed'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) as count FROM employee WHERE status = 'Pending'")->fetch_assoc()['count'];

// Fetch employee results
$results = $conn->query("SELECT * FROM employee ORDER BY submitted_at DESC");
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
      <!-- Sidebar Include -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Employee Examination Results</h1>
        <button class="bg-white border px-4 py-1 rounded shadow-sm hover:bg-gray-50">Logout</button>
      </div>

      <!-- Stats Cards -->
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

      <!-- Table -->
      <div class="bg-white shadow rounded">
        <div class="p-4 border-b font-medium">Recent Examination Results</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700">
              <tr>
                <th class="px-4 py-2">NAME</th>
                <th class="px-4 py-2">EXAMINATION</th>
                <th class="px-4 py-2">SCORE</th>
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
                <td class="px-4 py-2"><?= $row['score'] !== null ? $row['score'].'%' : 'N/A' ?></td>
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
                <td class="px-4 py-2"><button class="text-blue-600 underline">View Details</button></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
