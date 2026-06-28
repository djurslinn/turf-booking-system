<?php
session_start();

$message = "";
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST['code']);

    if ($code == $_SESSION['reset_code']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $message = "Invalid code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Verify Code - TurfZone</title>
  <link rel="stylesheet" href="auth.css">

</head>
<body>
    <div class="auth-container">
  <h2>Enter Verification Code</h2>
  <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
  <form method="POST">
    <input type="text" name="code" placeholder="Enter code" required>
    <button type="submit">Verify</button>
  </form>
    </div>
</body>
</html>
