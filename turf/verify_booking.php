<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['owner_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$owner_id = $_SESSION['owner_id'];

if (isset($_POST['booking_id'])) {
    $bid = intval($_POST['booking_id']);
    $verified_status = 2; // Verified

    $verify = $conn->prepare("UPDATE booking 
                              SET status_id=? 
                              WHERE booking_id=? 
                              AND turf_id IN (SELECT turf_id FROM turf WHERE owner_id=?)");
    $verify->bind_param("iii", $verified_status, $bid, $owner_id);
    if ($verify->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
