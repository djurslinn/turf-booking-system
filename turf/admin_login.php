<?php
session_start();
require_once 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $sql = "SELECT * FROM admin WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);

  if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
      $admin = $result->fetch_assoc();
      if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: admin_dashboard.php");
        exit();
      } else {
        $error = "Incorrect password.";
      }
    } else {
      $error = "Email not found.";
    }
  } else {
    $error = "Database error.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login - TurfZone</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('images/login_bg.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-color: #0f172a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      background: rgba(30, 41, 59, 0.9);
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
      max-width: 450px;
      width: 100%;
      color: white;
    }

    .login-container h2 {
      margin-bottom: 25px;
      text-align: center;
      color: #a3e635;
      font-size: 1.8rem;
    }

    .error-text {
      color: #f87171;
      font-weight: 500;
      margin-bottom: 15px;
      text-align: center;
    }

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
      color: #ffffff;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .login-container .links a:hover {
      text-decoration: underline;
    }

    .login-container a[href="index.php"] {
      color: #ffffff;
      display: inline-block;
      margin-bottom: 15px;
      text-decoration: none;
    }

    .login-container a[href="index.php"]:hover {
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
    <a href="index.php">← Back to Home</a>
    <h2>Admin Login</h2>

    <?php if ($error): ?>
      <div class="error-text"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>

</body>
</html>
