<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Examination System</title>
  <link rel="stylesheet" href="..\assets\employee_form.css"/>
</head>
<body>
   <header>
  <a href="..\index.php" class="title">Employee Examination System</a>
  <a href="..\index.php" class="back-button">← Back to Home</a>
</header>


  <main>
    <div class="container">
      <h2>Employee Information</h2>
      <p class="description">Please provide your personal information before starting the examination.</p>

      <form action="employee_handler.php" method="POST">
        <div class="form-group">
          <label>Full Name <span class="required">*</span></label>
          <input type="text" name="full_name" required />
        </div>

        <div class="form-group">
          <label>Email Address <span class="required">*</span></label>
          <input type="email" name="email" required />
        </div>

        <div class="form-group">
          <label>Date of Birth <span class="required">*</span></label>
          <input type="date" name="dob" required />
        </div>

        <div class="form-group">
          <label>Phone Number <span class="required">*</span></label>
          <input type="tel" name="phone" required maxlength="11"/>
        </div>

        <div class="form-group">
          <label>Highest Education Level <span class="required">*</span></label>
          <select name="education_level" required>
            <option value="">Select your education level</option>
            <option value="high_school">High School</option>
            <option value="bachelor">Bachelor's Degree</option>
            <option value="master">Master's Degree</option>
            <option value="phd">PhD</option>
          </select>
        </div>

        <div class="form-group">
          <label>Position Applied For <span class="required">*</span></label>
          <select name="position" required>
            <option value="">Select the position</option>
            <option value="Developer">Developer</option>
            <option value="Designer">Designer</option>
            <option value="Manager">Manager</option>
            <option value="Analyst">Analyst</option>
          </select>
        </div>

        <div class="text-center">
          <button type="submit" id="ExamBtn">Next: Start Examination</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
