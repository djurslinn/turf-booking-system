<?php
// register.php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "SELECT * FROM customer WHERE phone='$phone' OR email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<script>alert('User already exists.');</script>";
        } else {
            $sql_insert = "INSERT INTO customer(name,password,phone,email) VALUES('$name','$password_hashed','$phone','$email')";
            if ($conn->query($sql_insert) === TRUE) {

                // Send welcome email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'YOUR_EMAIL@gmail.com'; // Replace with your Gmail
                    $mail->Password = 'YOUR_APP_PASSWORD'; // Use Gmail App Password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('YOUR_EMAIL@gmail.com', 'TurfZone');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to TurfZone';
                    $mail->Body = "
Hi <strong>$name</strong>,<br><br>

🎉 <strong>Welcome to TurfZone!</strong><br>
Thank you for registering with us. You can now log in and start exploring our turfs, book slots, and manage your bookings easily.<br><br>

If you did not register or have any questions, please contact us immediately:<br>
📞 <strong>+91 7559947412</strong><br>
📧 <strong>support@turfzone.com</strong><br><br>

Best regards,<br>
<strong>The TurfZone Team</strong>
";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mailer Error: {$mail->ErrorInfo}");
                }

                echo "<script>
                        alert('Registered successfully! Please check your email.');
                        window.location.href='login.php';
                      </script>";
                exit;
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - TurfZone</title>
  <style>
    * { box-sizing: border-box; }
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
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      max-width: 450px;
      width: 100%;
    }
    .register-container h2 {
      margin-bottom: 25px;
      text-align: center;
      color: #a3e635;
      font-size: 1.8rem;
    }
    .register-container input[type="text"],
    .register-container input[type="email"],
    .register-container input[type="password"],
    .register-container input[type="tel"] {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #334155;
      color: white;
      margin-bottom: 20px;
      font-size: 1rem;
    }
    .register-container input::placeholder { color: #cbd5e1; }
    .register-container input:focus { outline: 2px solid #22c55e; }
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
    .register-container button:hover { background: #15803d; }
    .register-container p { color: #ffffff; text-align: center; margin-top: 15px; }
    .register-container p a { color: #a3e635; text-decoration: none; }
    .register-container p a:hover { text-decoration: underline; }
    .register-container a[href="index.php"] { color: #ffffff; display: inline-block; margin-bottom: 15px; text-decoration: none; }
    .register-container a[href="index.php"]:hover { text-decoration: underline; }
    @media (max-width: 500px) { .register-container { padding: 30px 20px; } }
  </style>
</head>
<body>

<div class="register-container">
  <a href="index.php">← Back to Home</a>
  <h2>Create Account</h2>
  <form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Enter a 10-digit phone number">
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
