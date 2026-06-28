<?php
session_start();
include 'config.php'; // DB connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turf_id = intval($_POST['turf_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    // ✅ Check if user booked this turf
    $checkBooking = $conn->prepare("
        SELECT * FROM slots 
        WHERE turf_id = ? AND customer_id = ? LIMIT 1
    ");
    $checkBooking->bind_param("ii", $turf_id, $user_id);
    $checkBooking->execute();
    $bookingResult = $checkBooking->get_result();

    if ($bookingResult->num_rows === 0) {
        // Not allowed to review without booking
        $_SESSION['error'] = "You can only review turfs you have booked.";
        header("Location: customer_profile.php");
        exit();
    }

    // ✅ Check if review already exists
    $checkReview = $conn->prepare("
        SELECT * FROM tbl_reviews 
        WHERE turf_id = ? AND customer_id = ? LIMIT 1
    ");
    $checkReview->bind_param("ii", $turf_id, $user_id);
    $checkReview->execute();
    $reviewResult = $checkReview->get_result();

    if ($reviewResult->num_rows > 0) {
        // Update existing review
        $update = $conn->prepare("
            UPDATE tbl_reviews 
            SET rating = ?, review_text = ?, created_at = NOW() 
            WHERE turf_id = ? AND customer_id = ?
        ");
        $update->bind_param("isii", $rating, $review, $turf_id, $user_id);
        $update->execute();
    } else {
        // Insert new review
        $insert = $conn->prepare("
            INSERT INTO tbl_reviews (turf_id, customer_id, rating, review_text, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $insert->bind_param("iiis", $turf_id, $user_id, $rating, $review);
        $insert->execute();
    }

    $_SESSION['success'] = "Your review has been submitted successfully.";
    header("Location: customer_profile.php");
    exit();
} else {
    header("Location: customer_profile.php");
    exit();
}
