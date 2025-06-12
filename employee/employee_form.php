<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Examination System</title>
  <link rel="stylesheet" href="../assets/employee_form.css" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
    }

    /* Fixed header */
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      background-color: #ffffff;
      border-bottom: 1px solid #ccc;
      z-index: 1000;
    }

    .title {
      font-size: 20px;
      font-weight: bold;
      color: #333;
      text-decoration: none;
    }

    .back-button {
      font-size: 16px;
      color: #007bff;
      text-decoration: none;
    }

    .back-button:hover {
      text-decoration: underline;
    }

    main {
      padding: 80px 20px 40px;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 8px;
    }

    h2 {
      margin-top: 0;
      font-size: 24px;
    }

    .description {
      font-size: 14px;
      color: #555;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }

    input, select {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .required {
      color: red;
    }

    .text-center {
      text-align: center;
      margin-top: 20px;
    }

    #ExamBtn {
      padding: 12px 20px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }

    #ExamBtn:hover {
      background-color: #0056b3;
    }

    /* Responsive styles */
    @media (max-width: 480px) {
      header {
        flex-direction: row;
        justify-content: space-between;
        padding: 0 15px;
      }

      .title {
        font-size: 16px;
      }

      .back-button {
        font-size: 14px;
      }

      .container {
        padding: 20px 15px;
      }

      h2 {
        font-size: 20px;
      }

      #ExamBtn {
        font-size: 15px;
      }
    }
    
  </style>
</head>
<body>

  <!-- Fixed Header -->
  <header>
    <a href="../index.php" class="title text-primary">Employee Examination System</a>
    <a href="../index.php" class="back-button">← Back</a>
  </header>

  <main>
    <div class="container">
      <h2>Employee Information</h2>
      <p class="description">Please provide your personal information before starting the examination.</p>

      <form action="employee_handler.php" method="POST">
        <div class="form-group">
          <label for="employee_num">Employee Number <span class="required">*</span></label>
          <input type="text" id="employee_num" name="employee_num" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
        </div>

        <div class="form-group">
          <label>Full Name <span class="required">*</span></label>
          <input type="text" name="full_name" required />
        </div>

        <div class="form-group">
          <label>Branch <span class="required">*</span></label>
          <input type="text" name="branch" required />
        </div>

        <div class="form-group">
          <label>Position <span class="required">*</span></label>
          <select name="position" required>
            <option value="">Select the position</option>
            <option value="Store Manager">Store Manager</option>
            <option value="Assistant Store Manager">Assistant Store Manager</option>
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
