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
            $conn = new mysqli("localhost", "root", "", "ees");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['grade'])) {
                $answerId = $_POST['answer_id'];
                $grade = min(floatval($_POST['score']), 10.0);

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
                $existingScore = $stmt->get_result()->fetch_assoc()[$scoreColumn] ?? 0;
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
                    $score = $stmt->get_result()->fetch_assoc()[$scoreCol];
                    $stmt->close();

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

                $average = ($examsTaken > 0) ? ($totalPercentage / $examsTaken) : 0;
                $averageRounded = round($average);
                $status = ($average >= 75) ? "Passed" : "Failed";

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
                    <table class="table table-bordered table-hover align-middle bg-white shadow-sm rounded">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Employee #</th>
                                <th>Title</th>
                                <th style="width: 30%">Question</th>
                                <th style="width: 40%">Essay Answer</th>
                                <th style="width: 15%">Score (0–10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $qid = $row['question_id'];
                                $questionText = "Not found";
                                $q = $conn->query("SELECT question_text FROM question WHERE id = $qid");
                                if ($q && $qrow = $q->fetch_assoc()) {
                                    $questionText = $qrow['question_text'];
                                }

                                $examTitle = "Unknown";
                                $examQuery = $conn->prepare("SELECT title FROM examinations WHERE exam_id = ?");
                                $examQuery->bind_param("i", $row['exam_id']);
                                $examQuery->execute();
                                $examResult = $examQuery->get_result();
                                if ($examResult && $examRow = $examResult->fetch_assoc()) {
                                    $examTitle = $examRow['title'];
                                }
                                $examQuery->close();
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['employee_num']) ?></td>
                                    <td><?= htmlspecialchars($examTitle) ?></td>
                                    <td>
                                        <strong>Q:</strong> <?= nl2br(htmlspecialchars($questionText)) ?>
                                    </td>
                                    <td style="white-space: pre-wrap; overflow-wrap: anywhere; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                                        <?= nl2br(htmlspecialchars($row['full_answer'])) ?>
                                        <div class="text-muted small mt-2">Answered at: <?= htmlspecialchars($row['answered_at']) ?></div>
                                    </td>
                                    <td>
                                        <form method="post" class="d-flex flex-column align-items-center">
                                            <input type="hidden" name="answer_id" value="<?= $row['id'] ?>">
                                            <input type="number"
                                                name="score"
                                                min="0"
                                                max="10"
                                                step="1"
                                                class="form-control form-control-sm text-center mb-2"
                                                style="width: 80px;"
                                                required
                                                placeholder="0-10"
                                                oninput="this.value = Math.min(Math.max(this.value, 0), 10)">
                                            <button type="submit" name="grade" value="1" class="btn btn-success btn-sm w-100">
                                                Submit
                                            </button>
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
