<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Examination System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f6f9;
    }
    header {
      background: #fff;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 {
      margin: 0;
      color: #222;
    }
    .login-link {
      font-size: 16px;
      font-weight: 600;
      color: #2563eb;
      cursor: pointer;
      border: none;
      background: none;
    }
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.4);
      display: none;
      z-index: 999;
    }
    .login-modal {
      position: fixed;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      width: 350px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      display: none;
      z-index: 1000;
    }
    .login-modal.active,
    .overlay.active {
      display: block;
    }
    .close-btn {
      float: right;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
    }
    .login-form input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    .login-form button {
      width: 100%;
      padding: 10px;
      background: #2563eb;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    #log {
      text-align: center;
    }
  </style>
</head>
<body>

<header>
  <h1>Employee Examination System</h1>
  <button class="login-link" id="loginTrigger">Login</button>
</header>

<div class="overlay" id="overlay"></div>

<div class="login-modal" id="loginModal">
  <button class="close-btn" id="closeBtn">&times;</button>
  <h2 id="log">Login</h2>

  <form class="login-form" method="POST" action="../ees/admin/admin_handler.php">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
  </form>
</div>

<script>
  const loginTrigger = document.getElementById('loginTrigger');
  const loginModal = document.getElementById('loginModal');
  const overlay = document.getElementById('overlay');
  const closeBtn = document.getElementById('closeBtn');

  loginTrigger.addEventListener('click', () => {
    loginModal.classList.add('active');
    overlay.classList.add('active');
  });

  closeBtn.addEventListener('click', () => {
    loginModal.classList.remove('active');
    overlay.classList.remove('active');
  });

  overlay.addEventListener('click', () => {
    loginModal.classList.remove('active');
    overlay.classList.remove('active');
  });
</script>

</body>
</html>
