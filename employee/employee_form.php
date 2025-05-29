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
          <label>Branch <span class="required">*</span></label>
          <input type="text" name="branch" required />
        </div>

         <div class="form-group">
          <label>Position For <span class="required">*</span></label>
          <select name="position" required>
            <option value="">Select the position</option>
            <option value="Store Manager">Store Manager</option>
            <option value="Management Trainee">Management Trainee</option>
            <option value="Admin Assistant">Admin Assistant</option>
            <option value="Dining Supervisor">Dining Supervisor</option>
            <option value="Kitchen Supervisor">Kitchen Supervisor</option>
            <option value="Cashier">Cashier</option>
            <option value="Dining Staff">Dining Staff</option>
            <option value="Kitchen Staff">Kitchen Staff</option>
          </select>
        </div>

        <div class="form-group">
          <label>Date Started in the Company <span class="required">*</span></label>
          <input type="date" name="date_started" required />
        </div>

       <div class="form-group">
  <label>Date of Examination <span class="required">*</span></label>
  <input type="date" name="date_of_exam" value="<?php echo date('Y-m-d'); ?>" readonly required>
</div>

        <div class="text-center">
          <button type="submit" id="ExamBtn">Next: Start Examination</button>
        </div>
      </form>
    </div>
  </main>
   <script src="../assets/main.js"></script>
</body>
</html>


