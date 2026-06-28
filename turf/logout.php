<?php
// logout.php
session_start();

// Detect user type based on stored session ID
if (isset($_SESSION['admin_id'])) {
    $redirect = "admin_login.php?message=loggedout";
} elseif (isset($_SESSION['owner_id'])) {
    $redirect = "owner_login.php?message=loggedout";
} elseif (isset($_SESSION['user_id'])) {
    $redirect = "login.php?message=loggedout";
} else {
    $redirect = "login.php";
}

// Clear session
$_SESSION = array();
session_destroy();

// Redirect to correct login page
header("Location: $redirect");
exit();
?>
