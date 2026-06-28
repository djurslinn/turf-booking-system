<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - TurfZone</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #0f172a;
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 20px;
    }

    .login-container {
      background: #1e293b;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
      max-width: 400px;
      width: 100%;
    }

    .login-container h2 {
      margin-bottom: 25px;
      text-align: center;
      color: #a3e635;
      font-size: 1.8rem;
    }

    .login-container label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .login-container input[type="text"],
    .login-container input[type="email"],
    .login-container input[type="password"] {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #334155;
      color: white;
      margin-bottom: 20px;
      font-size: 1rem;
    }

    .login-container input::placeholder {
      color: #cbd5e1;
    }

    .login-container input:focus {
      outline: 2px solid #22c55e;
    }

    .login-container button {
      width: 100%;
      background: #16a34a;
      border: none;
      padding: 12px;
      color: white;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .login-container button:hover {
      background: #15803d;
    }

    .login-container .links {
      margin-top: 15px;
      text-align: center;
    }

    .login-container .links a {
      color: #a3e635;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .login-container .links a:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .login-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<div class="login-container">
  <h2>Customer Login</h2> <!-- Change to "Turf Owner Login" if needed -->
  <form action="process_login.php" method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="you@example.com" required />

    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Enter password" required />

    <button type="submit">Login</button>
  </form>
  <div class="links">
    <p><a href="register.php">New here? Register</a></p>
    <p><a href="index.php">← Back to Home</a></p>
  </div>
</div>

</body>
</html>
