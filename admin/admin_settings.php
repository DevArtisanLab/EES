<?php include 'sidebar.php'; ?>
<?php
// Database connection (adjust with your own credentials)
$host = 'localhost';
$db   = 'ees';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .avatar {
      width: 40px;
      height: 40px;
      background-color: #e9ecef;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: #495057;
    }
    .badge-admin {
      background-color: #dbeafe;
      color: #1d4ed8;
    }
    .badge-hr {
      background-color: #d1fae5;
      color: #047857;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <h4 class="mb-4">System Settings</h4>

  <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-management" type="button" role="tab">User Management</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">Security</button>
    </li>
  </ul>

  <div class="tab-content" id="settingsTabContent">
    <!-- User Management Tab -->
    <div class="tab-pane fade show active" id="user-management" >
      <div class="mb-3">
        <button class="btn btn-primary"><i class="bi bi-person-plus-fill"></i> Add New User</button>
      </div>
      <div class="list-group">

        <?php
        $sql = "SELECT full_name, username FROM admins";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $initial = strtoupper(substr($row['full_name'], 0, 1));
                

                echo '
                <div class="list-group-item d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                    <div class="avatar me-3">' . $initial . '</div>
                    <div>
                      <div class="fw-semibold">' . htmlspecialchars($row['full_name']) . '</div>
                      <div class="text-muted small">' . htmlspecialchars($row['username']) . '</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                   
                    <button class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                  </div>
                </div>';
            }
        } else {
            echo '<p class="text-muted">No users found.</p>';
        }

        $conn->close();
        ?>

      </div>
    </div>

    <!-- Security Tab -->
    <div class="tab-pane fade" id="security" role="tabpanel">
      <p class="text-muted">Security settings will be available here.</p>
    </div>
  </div>
</div>

<!-- Bootstrap Icons and JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
