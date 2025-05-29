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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        h2 {
            font-weight: 600;
            font-size: 1.5rem;
        }
        #btnCreate {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px;
            padding: 8px 16px;
            border: none;
        }
        #btnCreate:hover {
            background-color: #0b5ed7;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        .table thead {
            background-color: #f1f3f5;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .action-btns .btn {
            margin-right: 5px;
            font-size: 0.8rem;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .form-row {
            display: flex;
            gap: 10px;
        }
        .form-row > * {
            flex: 1;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        .modal-footer {
            display: flex;
            justify-content: space-between;
        }
        .btn-primary {
            background-color: #2563eb;
            color: white;
            border: none;
        }
        .btn-secondary {
            background-color: white;
            color: black;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Examinations Management</h2>
        <button id="btnCreate" onclick="openModal()">+ Create Exam</button>
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" href="#">Active Examinations</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Draft</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Archived</a></li>
    </ul>

    <div class="table-responsive">
        <table class="table align-middle">
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
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['exam_id'] ?></td>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['position'] ?></td>
                    <td><?= $row['questions'] ?></td>
                    <td><?= $row['duration'] ?> min</td>
                    <td><?= $row['created'] ?></td>
                    <td><span class="status-active"><?= $row['status'] ?></span></td>
                    <td class="action-btns">
                        <a href="view_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-primary">View</a>
                        <a href="edit_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-success">Edit</a>
                        <a href="delete_exam.php?id=<?= $row['exam_id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Exam Modal -->
<div id="examModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Create New Examination</h2>
        <form>
            <label>Examination Title</label>
            <input type="text" placeholder="e.g. Human Resource Department" required>
            <div class="form-row">
                <div>
                    <label for="position">Position</label>
                    <select name="position" id="position" required class="form-select">
                        <option value="" disabled selected hidden>Select Department</option>
                        <option value="All" required>All</option>
                        <option value="Technical Service Department" required>Store Manager</option>
                        <option value="Human Resource Department" required>Management Trainee</option>
                        <option value="Accounting Department" required>Accounting Department</option>
                    </select>
                </div>
                <div>
                    <label>Duration (minutes)</label>
                    <input type="number" value="60" required>
                </div>
            </div>
            <label>Description</label>
            <textarea placeholder="Provide a description of this examination" required></textarea>
            <div class="form-row">
                <div>
                    <label>Passing Score (%)</label>
                    <input type="number" value="75" required>
                </div>
                <div>
                    <label for="status">Status</label>
                    <select name="status" id="status" required class="form-select">
                        <option value="" disabled selected hidden>Select Status</option>
                        <option value="Draft">Draft</option>
                        <option value="Active">Active</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="opensModal()">Next</button>
            </div>
        </form>
    </div>
</div>

<!-- Question Modal -->
<div id="questionModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <span class="close" onclick="closesModal()">&times;</span>
        <h4 class="mb-3">Add Examination Questions</h4>

        <!-- Exam Summary -->
        <div class="bg-light p-3 mb-3 rounded">
            <strong>Title:</strong> <span id="examTitle"></span><br>
            <strong>Position:</strong> <span id="examPosition"></span><br>
            <strong>Duration:</strong> <span id="examDuration"></span>
        </div>

        <form id="questionForm">
            <div id="questionContainer">
                <!-- Question Card Template -->
                <div class="border p-3 mb-3 rounded question-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Question <span class="question-number">1</span></strong>
                        <button type="button" class="btn-close" aria-label="Close" onclick="removeQuestion(this)"></button>
                    </div>

                    <div class="mb-2">
                        <textarea class="form-control" placeholder="Enter your question here..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Question Type</label>
                        <select class="form-select" onchange="updateOptions(this)">
                            <option value="Multiple Choice">Multiple Choice</option>
                            <option value="True/False">True/False</option>
                            <!-- Add more if needed -->
                        </select>
                    </div>

                    <div class="answer-options">
                        <!-- Answer options go here -->
                        <div class="form-check d-flex align-items-center mb-1">
                            <input class="form-check-input me-2" type="radio" name="q1">
                            <input class="form-control" placeholder="Option 1">
                        </div>
                        <div class="form-check d-flex align-items-center mb-1">
                            <input class="form-check-input me-2" type="radio" name="q1">
                            <input class="form-control" placeholder="Option 2">
                        </div>
                        <div class="form-check d-flex align-items-center mb-1">
                            <input class="form-check-input me-2" type="radio" name="q1">
                            <input class="form-control" placeholder="Option 3">
                        </div>
                        <div class="form-check d-flex align-items-center mb-1">
                            <input class="form-check-input me-2" type="radio" name="q1">
                            <input class="form-control" placeholder="Option 4">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary w-100 mb-3" onclick="addQuestion()">+ Add Another Question</button>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closesModal()">Back</button>
                <button type="submit" class="btn btn-primary">Create Examination</button>
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

    function opensModal() {
        closeModal();
        document.getElementById("questionModal").style.display = "block";
    }

    function closesModal() {
        document.getElementById("questionModal").style.display = "none";
    }

    window.onclick = function(event) {
        const examModal = document.getElementById("examModal");
        const questionModal = document.getElementById("questionModal");
        if (event.target === examModal) {
            closeModal();
        } else if (event.target === questionModal) {
            closesModal();
        }
    };
</script>

<script>
    let questionCount = 1;

    function addQuestion() {
        const container = document.getElementById("questionContainer");
        const template = container.querySelector(".question-card");
        const clone = template.cloneNode(true);

        questionCount++;
        clone.querySelector(".question-number").textContent = questionCount;
        clone.querySelectorAll("input[type=radio]").forEach(radio => {
            radio.name = `q${questionCount}`;
            radio.checked = false;
        });
        clone.querySelectorAll("input[type=text], textarea").forEach(input => input.value = "");
        container.appendChild(clone);
    }

    function removeQuestion(btn) {
        const card = btn.closest(".question-card");
        if (document.querySelectorAll(".question-card").length > 1) {
            card.remove();
            updateQuestionNumbers();
        }
    }

    function updateQuestionNumbers() {
        document.querySelectorAll(".question-card").forEach((card, index) => {
            card.querySelector(".question-number").textContent = index + 1;
        });
    }

    function updateOptions(select) {
        const container = select.closest(".question-card").querySelector(".answer-options");
        container.innerHTML = "";

        if (select.value === "Multiple Choice") {
            for (let i = 1; i <= 4; i++) {
                container.innerHTML += `
                    <div class="form-check d-flex align-items-center mb-1">
                        <input class="form-check-input me-2" type="radio" name="q${questionCount}">
                        <input class="form-control" placeholder="Option ${i}">
                    </div>
                `;
            }
        } else if (select.value === "True/False") {
            container.innerHTML = `
                <div class="form-check d-flex align-items-center mb-1">
                    <input class="form-check-input me-2" type="radio" name="q${questionCount}">
                    <input class="form-control" value="True" disabled>
                </div>
                <div class="form-check d-flex align-items-center mb-1">
                    <input class="form-check-input me-2" type="radio" name="q${questionCount}">
                    <input class="form-control" value="False" disabled>
                </div>
            `;
        }
    }
</script>


<?php $conn->close(); ?>
</body>
</html>
