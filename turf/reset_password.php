<?php
session_start();
require 'config.php';

$message = "";

// Check if user verified code
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_usertype']) || !isset($_SESSION['code_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$reset_email = $_SESSION['reset_email'];
$usertype = $_SESSION['reset_usertype'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $table = $usertype === 'owner' ? 'owner' : 'customer';

        $sql = "UPDATE $table SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $reset_email);

        if ($stmt->execute()) {
            // Clear reset session data
            unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['code_verified'], $_SESSION['reset_usertype']);
            
            $login_page = $usertype === 'owner' ? 'owner_login.php' : 'login.php';
            header("Location: $login_page?reset=success");
            exit();
        } else {
            $message = "Error updating password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - TurfZone</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Reset Password</h2>
        <?php if ($message) echo "<div class='error-text'>$message</div>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Set New Password</button>
        </form>
        <div style="margin-top:15px; text-align:center;">
            <a href="forgot_password.php?usertype=<?= $usertype ?>">← Back</a>
        </div>
    </div>
</body>
</html>
