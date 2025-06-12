<!-- Main layout wrapper -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Column -->
        <div class="col-md-3 col-lg-2 px-0">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content Column -->
        <div class="col-md-9 col-lg-10 px-md-4 py-4">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "ees";

            $conn = new mysqli($servername, $username, $password, $database);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['grade'])) {
                $answerId = $_POST['answer_id'];
                $grade = floatval($_POST['score']);

                $stmt = $conn->prepare("SELECT employee_num, exam_id FROM answers WHERE id = ?");
                $stmt->bind_param("i", $answerId);
                $stmt->execute();
                $stmt->bind_result($employeeNum, $examId);
                $stmt->fetch();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE answers SET is_correct = ? WHERE id = ?");
                $stmt->bind_param("di", $grade, $answerId);
                $stmt->execute();
                $stmt->close();

                $scoreColumn = "score_" . $examId;

                $stmt = $conn->prepare("SELECT `$scoreColumn` FROM employee WHERE employee_num = ?");
                $stmt->bind_param("s", $employeeNum);
                $stmt->execute();
                $result = $stmt->get_result();
                $existingScore = $result->fetch_assoc()[$scoreColumn] ?? 0;
                $stmt->close();

                $updatedScore = $existingScore + $grade;

                $stmt = $conn->prepare("UPDATE employee SET `$scoreColumn` = ? WHERE employee_num = ?");
                $stmt->bind_param("ds", $updatedScore, $employeeNum);
                $stmt->execute();
                $stmt->close();

                $totalPercentage = 0;
                $examsTaken = 0;

                for ($i = 1; $i <= 10; $i++) {
                    $scoreCol = "score_" . $i;
                    $stmt = $conn->prepare("SELECT `$scoreCol` FROM employee WHERE employee_num = ?");
                    $stmt->bind_param("s", $employeeNum);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $scoreRow = $result->fetch_assoc();
                    $stmt->close();

                    $score = $scoreRow[$scoreCol];
                    if (!is_null($score)) {
                        $stmt = $conn->prepare("SELECT correct_option, question_type FROM question WHERE exam_id = ?");
                        $stmt->bind_param("i", $i);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $totalPossible = 0;
                        while ($row = $result->fetch_assoc()) {
                            $type = strtolower($row['question_type']);
                            if ($type === 'enumeration') {
                                $items = array_filter(array_map('trim', explode(',', strtolower($row['correct_option']))));
                                $totalPossible += count($items);
                            } elseif ($type === 'essay') {
                                $totalPossible += 10;
                            } else {
                                $totalPossible += 1;
                            }
                        }
                        $stmt->close();

                        if ($totalPossible > 0) {
                            $percentage = ($score / $totalPossible) * 100;
                            $totalPercentage += $percentage;
                            $examsTaken++;
                        }
                    }
                }

                $averagePercentage = ($examsTaken > 0) ? ($totalPercentage / $examsTaken) : 0;
                $averageRounded = round($averagePercentage, 2);
                $status = ($averagePercentage >= 75) ? "Passed" : "Failed";

                $stmt = $conn->prepare("UPDATE employee SET average = ?, status = ? WHERE employee_num = ?");
                $stmt->bind_param("dss", $averageRounded, $status, $employeeNum);
                $stmt->execute();
                $stmt->close();
            }

            $sql = "SELECT id, employee_num, exam_id, question_id, full_answer, answered_at 
                    FROM answers 
                    WHERE is_correct IS NULL AND full_answer IS NOT NULL";
            $result = $conn->query($sql);
            ?>

            <h2 class="mb-4">Ungraded Essay Answers</h2>

            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Employee #</th>
                                <th>Exam ID</th>
                                <th>Question ID</th>
                                <th>Essay Answer</th>
                                <th>Score (out of 10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['employee_num']) ?></td>
                                    <td><?= htmlspecialchars($row['exam_id']) ?></td>
                                    <td><?= htmlspecialchars($row['question_id']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['full_answer'])) ?></td>
                                    <td>
                                        <form method="post" class="d-flex align-items-center" style="gap: 5px;">
                                            <input type="hidden" name="answer_id" value="<?= $row['id'] ?>">
                                            <input type="number" name="score" step="0.01" min="0" max="10" class="form-control form-control-sm" placeholder="0-10" required>
                                            <button type="submit" name="grade" value="1" class="btn btn-primary btn-sm">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No ungraded essay answers found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
