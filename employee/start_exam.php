<?php
session_start();

if (!isset($_SESSION["name"], $_SESSION["start_time"], $_SESSION["exam_duration"])) {
    header("Location: employee_form.php");
    exit;
}

$remaining_time = ($_SESSION["start_time"] + $_SESSION["exam_duration"]) - time();
if ($remaining_time <= 0) {
    header("Location: submit_exam.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>/* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background-color: #f9fafb;
  color: #111827;
  line-height: 1.6;
}

h2 {
  text-align: center;
  margin-top: 20px;
}

.container {
  max-width: 600px;
  background-color: #fff;
  margin: 40px auto;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

.user-info {
  background: #f1f5f9;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  border-left: 3px solid #3b82f6;
}

.user-info p {
  margin-bottom: 5px;
  font-weight: 500;
}

#timer {
  color: #2563eb;
  font-weight: bold;
  float: right;
}

.question {
  margin-bottom: 20px;
  font-weight: 500;
}

.options label {
  display: block;
  padding: 10px 14px;
  margin: 6px 0;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.options input[type="radio"] {
  margin-right: 10px;
}

.options label:hover {
  background-color: #eff6ff;
  border-color: #60a5fa;
}

button {
  background-color: #2563eb;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  float: right;
  transition: background-color 0.2s ease;
}

button:hover {
  background-color: #1e40af;
}
</style>
    <title>Assessment Test</title>
    <script>
        let timeLeft = <?= $remaining_time ?>;
        function startTimer() {
            const timerDisplay = document.getElementById("timer");
            const form = document.getElementById("examForm");

            const interval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(interval);
                    alert("Time's up! Submitting your exam.");
                    form.submit();
                }
            }, 1000);
        }

        window.onload = startTimer;
    </script>
</head>
    <body>
  <div class="container">
    <div class="user-info">
      <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION["full_name"]) ?></p>
      <p><strong>Position:</strong> <?= htmlspecialchars($_SESSION["position"]) ?></p>
      <p><strong>Time Remaining:</strong> <span id="timer"></span></p>
    </div>

    <form id="examForm" action="submit_exam.php" method="POST">
      <p class="question">Question 1 of 5: Which of the following is NOT a primary feature of object-oriented programming?</p>

      <div class="options"required>
        <label><input type="radio" name="q1" value="Encapsulation"required > Encapsulation</label>
        <label><input type="radio" name="q1" value="Inheritance"required> Inheritance</label>
        <label><input type="radio" name="q1" value="Sequential execution"required> Sequential Execution</label>
        <label><input type="radio" name="q1" value="Polymorphism"required> Polymorphism</label>
      </div>

      <button type="submit">Submit Exam</button>
    </form>
  </div>
</body>
    </form>
</body>
</html>
