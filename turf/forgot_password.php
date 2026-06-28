<?php
session_start();
require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$message = "";

// Get user type from URL (default customer)
$usertype = isset($_GET['usertype']) && $_GET['usertype'] === 'owner' ? 'owner' : 'customer';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Decide table based on usertype
    $table = $usertype === 'owner' ? 'owner' : 'customer';

    $sql = "SELECT * FROM $table WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Generate 6-digit code
        $code = rand(100000, 999999);

        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_usertype'] = $usertype;

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'YOUR_EMAIL@gmail.com'; // your gmail
            $mail->Password = 'YOUR_APP_PASSWORD';        // your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('YOUR_EMAIL@gmail.com', 'TurfZone');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code - TurfZone';
            $mail->Body = "
                Hi,<br><br>
                Your password reset code is: <strong>$code</strong><br><br>
                If you did not request this, please ignore this email.<br><br>
                Regards,<br>TurfZone Team
            ";

            $mail->send();
            header("Location: verify_code.php");
            exit();
        } catch (Exception $e) {
            $message = "Error sending email: " . $mail->ErrorInfo;
        }

    } else {
        $message = "No $usertype account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - TurfZone</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Forgot Password</h2>
        <?php if ($message) echo "<div class='error-text'>$message</div>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Verification Code</button>
        </form>
        <div style="margin-top:15px; text-align:center;">
            <a href="<?= $usertype === 'owner' ? 'owner_login.php' : 'login.php' ?>">← Back to Login</a>
        </div>
    </div>
</body>
</html>
