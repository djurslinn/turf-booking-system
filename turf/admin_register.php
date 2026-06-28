<?php
require_once 'config.php';

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

  // Check if email already exists
  $check = $conn->prepare("SELECT * FROM admin WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check_result = $check->get_result();

  if ($check_result->num_rows > 0) {
    $error = "Email already registered.";
  } else {
    $stmt = $conn->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
      $success = "Admin registered successfully!";
      header("Location: admin_login.php");
      exit();
    } else {
      $error = "Registration failed.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Register - TurfZone</title>
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

    .register-container {
      background: rgba(30, 41, 59, 0.9);
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
      max-width: 450px;
      width: 100%;
      color: white;
    }

    .register-container h2 {
      margin-bottom: 25px;
      text-align: center;
      color: #a3e635;
      font-size: 1.8rem;
    }

    .success-text {
      color: #86efac;
      font-weight: 500;
      margin-bottom: 15px;
      text-align: center;
    }

    .error-text {
      color: #f87171;
      font-weight: 500;
      margin-bottom: 15px;
      text-align: center;
    }

    .register-container input {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #334155;
      color: white;
      margin-bottom: 20px;
      font-size: 1rem;
    }

    .register-container input::placeholder {
      color: #cbd5e1;
    }

    .register-container input:focus {
      outline: 2px solid #22c55e;
    }

    .register-container button {
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

    .register-container button:hover {
      background: #15803d;
    }

    .register-container .links {
      margin-top: 15px;
      text-align: center;
    }

    .register-container .links a {
      color: #ffffff;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .register-container .links a:hover {
      text-decoration: underline;
    }

    .register-container a[href="index.php"] {
      color: #ffffff;
      display: inline-block;
      margin-bottom: 15px;
      text-decoration: none;
    }

    .register-container a[href="index.php"]:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .register-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="register-container">
    <a href="index.php">← Back to Home</a>
    <h2>Admin Registration</h2>

    <?php if ($error): ?>
      <div class="error-text"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-text"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Register</button>
    </form>
  </div>

</body>
</html>
