<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['user_id'];

    // Verify booking belongs to the logged-in user
    $check = $conn->prepare("SELECT booking_id FROM booking WHERE booking_id = ? AND customer_id = ? AND is_deleted = 0 LIMIT 1");
    $check->bind_param("ii", $booking_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Delete slots linked to this booking
        $delSlots = $conn->prepare("DELETE FROM slots WHERE booking_id = ?");
        $delSlots->bind_param("i", $booking_id);
        $delSlots->execute();

        // Optionally also mark booking as deleted (soft delete)
        $updateBooking = $conn->prepare("UPDATE booking SET is_deleted = 1 WHERE booking_id = ?");
        $updateBooking->bind_param("i", $booking_id);
        $updateBooking->execute();

        echo "<script>alert('Booking cancelled successfully!'); window.location='customer_profile.php';</script>";
    } else {
        echo "<script>alert('Invalid request or booking not found.'); window.location='customer_profile.php';</script>";
    }
} else {
    header("Location: customer_profile.php");
    exit();
}
