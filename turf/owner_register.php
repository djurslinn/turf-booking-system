<?php
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $plain_password = $_POST['password']; 
    $password   = password_hash($plain_password, PASSWORD_BCRYPT);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $address_id = !empty($_POST['address_id']) ? intval($_POST['address_id']) : "NULL";

    $sql = "SELECT * FROM owner WHERE phone='$phone' OR email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $error = "User already exists.";
    } else {
        $sql = "INSERT INTO owner (name, password, phone, email, address_id)
                VALUES ('$name', '$password', '$phone', '$email', $address_id)";
        if (mysqli_query($conn, $sql)) {

            // Send email to the owner
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'YOUR_EMAIL@gmail.com'; // your Gmail
                $mail->Password = 'YOUR_APP_PASSWORD';   // App password from Gmail
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('YOUR_EMAIL@gmail.com', 'TurfZone');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Welcome to TurfZone - Owner Registration Successful';
                $mail->Body = "
                Hi <strong>$name</strong>,<br><br>
                🎉 <strong>Welcome to TurfZone!</strong><br>
                Thank you for registering as an Owner.<br>
                You can now log in and add your turfs, manage slots, and view bookings.<br><br>

                

                If you did not register or have any questions, please contact us immediately:<br>
                📞 <strong>+91 9876543210</strong><br>
                📧 <strong>support@turfzone.com</strong><br><br>

                Best regards,<br>
                <strong>The TurfZone Team</strong>
                ";

                $mail->send();
                $success = "Registered successfully! An email has been sent to $email.";
            } catch (Exception $e) {
                $success = "Registered successfully, but email could not be sent. Error: {$mail->ErrorInfo}";
            }

        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Owner Register - TurfZone</title>
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

    .form-container {
      background: rgba(30, 41, 59, 0.9);
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
      max-width: 500px;
      width: 100%;
      color: white;
    }

    .form-container h2 {
      text-align: center;
      color: #a3e635;
      font-size: 1.8rem;
      margin-bottom: 25px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      background: #334155;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 1rem;
    }

    input::placeholder { color: #cbd5e1; }
    input:focus { outline: 2px solid #22c55e; }

    input[type="submit"] {
      background-color: #16a34a;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      width: 100%;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    input[type="submit"]:hover { background-color: #15803d; }

    .success, .error {
      text-align: center;
      font-weight: 500;
      margin-bottom: 15px;
    }

    .success { color: #4ade80; }
    .error   { color: #f87171; }

    a.back-link {
      display: inline-block;
      color: #ffffff;
      text-decoration: none;
      margin-bottom: 20px;
    }

    a.back-link:hover { text-decoration: underline; }

    @media (max-width: 500px) {
      .form-container { padding: 30px 20px; }
    }
  </style>
</head>
<body>

  <div class="form-container">
    <a href="index.php" class="back-link">← Back to Home</a>
    <h2>Register as Owner</h2>

    <?php if ($success): ?>
      <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="text" name="phone" placeholder="Phone" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Register">
    </form>
  </div>

</body>
</html>
