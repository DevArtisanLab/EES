<?php
$conn = new mysqli("localhost", "root", "", "ees");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql    = "SELECT * FROM examinations WHERE status = 'Active'";
$result = $conn->query($sql);
include 'sidebar.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Examinations Management</title>
    <link href = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel = "stylesheet">
    <style>
        body {
            font-family     : 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        h2 {
            font-weight: 600;
            font-size  : 1.5rem;
        }
        #btnCreate {
            background-color: #0d6efd;
            color           : white;
            border-radius   : 10px;
            padding         : 8px 16px;
            border          : none;
        }
        #btnCreate:hover {
            background-color: #0b5ed7;
        }
        .status-active {
            background-color: #d1e7dd;
            color           : #0f5132;
            border-radius   : 8px;
            padding         : 4px 12px;
            font-size       : 0.85rem;
        }
        .table thead {
            background-color: #f1f3f5;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .action-btns .btn {
            margin-right: 5px;
            font-size   : 0.8rem;
        }

          /* Modal Styles */
        .modal {
            display         : none;
            position        : fixed;
            z-index         : 999;
            left            : 0;
            top             : 0;
            width           : 100%;
            height          : 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fff;
            margin          : 5% auto;
            padding         : 20px;
            border-radius   : 8px;
            width           : 600px;
            box-shadow      : 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .form-row {
            display: flex;
            gap    : 10px;
        }
        .form-row > * {
            flex: 1;
        }
        input, select, textarea {
            width        : 100%;
            padding      : 10px;
            margin-bottom: 16px;
            border       : 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        .modal-footer {
            display        : flex;
            justify-content: space-between;
        }
        .btn-primary {
            background-color: #2563eb;
            color           : white;
            border          : none;
        }
        .btn-secondary {
            font-size       : 15px;
            background-color: WHITE;
            COLOR           : BLACK;
        }
        .close {
            float    : right;
            font-size: 20px;
            cursor   : pointer;
        }
    </style>
</head>
<body>

<div class = "container-fluid p-4">
<div class = "d-flex justify-content-between align-items-center mb-4">
        <h2>Examinations Management</h2>
        <button id = "btnCreate" onclick = "openModal()">+ Create Exam</button>
    </div>

    <ul class = "nav nav-tabs mb-3">
    <li class = "nav-item"><a class = "nav-link active" href = "#">Active Examinations</a></li>
    <li class = "nav-item"><a class = "nav-link" href        = "#">Draft</a></li>
    <li class = "nav-item"><a class = "nav-link" href        = "#">Archived</a></li>
    </ul>

    <div   class = "table-responsive">
    <table class = "table align-middle">
            <thead>
                <tr>
                    <th>Exam ID</th>
                    <th>Title</th>
                    <th>Position</th>
                    <th>Questions</th>
                    <th>Duration</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                              <td><? = $row['exam_id'] ?></td>
                              <td><? = $row['title'] ?></td>
                              <td><? = $row['position'] ?></td>
                              <td><? = $row['questions'] ?></td>
                              <td><? = $row['duration'] ?> min</td>
                              <td><? = $row['created'] ?></td>
                    <td><span class  = "status-active"><?                                = $row['status'] ?></span></td>
                    <td       class  = "action-btns">
                    <a        href   = "view_exam.php?id=<?= $row['exam_id'] ?>" class   = "btn btn-outline-primary">View</a>
                    <a        href   = "edit_exam.php?id=<?= $row['exam_id'] ?>" class   = "btn btn-outline-success">Edit</a>
                    <a        href   = "delete_exam.php?id=<?= $row['exam_id'] ?>" class = "btn btn-outline-danger" onclick = "return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div  id    = "examModal" class = "modal">
<div  class = "modal-content">
<span class = "close" onclick   = "closeModal()">&times;</span>
        <h2>Create New Examination</h2>
        <form>
            <label>Examination Title</label>
            <input type  = "text" placeholder = "e.g. Human Resource Department" required>
            <div   class = "form-row">
                <div>
    <label  for   = "position">Position</label>
    <select name  = "position" id = "position" required class = "form-select">
    <option value = "" disabled selected hidden>Select Department</option>
    <option value = "All"required>All</option>
    <option value = "Technical Service Department"required>Technical Service Department</option>
    <option value = "Human Resource Department"required>Human Resource Department</option>
    <option value = "Accounting Department"required>Accounting Department</option>
    </select>

                </div>
                <div>
                    <label>Duration (minutes)</label>
                    <input type = "number" value = "60" required>
                </div>
            </div>
            <label>Description</label>
            <textarea placeholder = "Provide a description of this examination" required></textarea>
            <div      class       = "form-row">
                <div>
                    <label>Passing Score (%)</label>
                    <input type = "number" value = "75"required>
                </div>
                <div>
                    <label  for   = "status">Status</label>
                    <select name  = "status" id = "status" required class = "form-select">
                        <option value = "" disabled selected hidden>Select Status</option>
                        <option value = "Draft">Draft</option>
                        <option value = "Active">Active</option>
                    </select>

                </div>
            </div>
            <div    class = "modal-footer">
            <button type  = "button" class = "btn btn-secondary" onclick = "closeModal()">Cancel</button>
            <button type  = "submit" class = "btn btn-primary">Next</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById("examModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("examModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("examModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php $conn->close(); ?>
</body>
</html>
