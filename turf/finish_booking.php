<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$customer_id    = $_SESSION['user_id'];
$turf_id        = isset($_POST['turf_id']) ? (int)$_POST['turf_id'] : null;
$slot_date      = $_POST['day'] ?? null;
$selected_slots = isset($_POST['selected_slots']) ? (array)$_POST['selected_slots'] : [];
$total_price    = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0;

if (!$turf_id || !$slot_date || empty($selected_slots)) {
    die("Invalid booking request.");
}

$success_slots = [];
$failed_slots  = [];

$booking_date = date('Y-m-d'); 
$status_id    = 1; // Confirmed

// -----------------------
// Generate Booking ID
// -----------------------
function generateBookingID($length = 8) {
    return 'BKG'.strtoupper(bin2hex(random_bytes($length))); // e.g., BKG4F3A2D1C
}

$booking_id = generateBookingID();

// -----------------------
// Insert booking record
// -----------------------
$insert_booking_sql = "INSERT INTO booking (booking_id, customer_id, turf_id, booking_date, status_id)
                       VALUES (?, ?, ?, ?, ?)";
$booking_stmt = $conn->prepare($insert_booking_sql);
$booking_stmt->bind_param("siisi", $booking_id, $customer_id, $turf_id, $booking_date, $status_id);

if (!$booking_stmt->execute()) {
    die("Failed to create booking: " . $conn->error);
}

// -----------------------
// Get turf owner
// -----------------------
$owner_id = null;
$turf_stmt = $conn->prepare("SELECT owner_id, name FROM turf WHERE turf_id = ?");
$turf_stmt->bind_param("i", $turf_id);
$turf_stmt->execute();
$turf_result = $turf_stmt->get_result();
$turf_name = "";
if ($turf_result->num_rows > 0) {
    $row = $turf_result->fetch_assoc();
    $owner_id = $row['owner_id'];
    $turf_name = $row['name'];
}

// -----------------------
// Insert slots
// -----------------------
foreach ($selected_slots as $start_time) {
    $slot_time = date('H:i:s', strtotime($start_time));

    // Check if slot already booked
    $check_sql = "SELECT 1 FROM slots 
                  WHERE turf_id = ? AND slot_date = ? AND slot_time = ? AND is_booked = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iss", $turf_id, $slot_date, $slot_time);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        // Insert new slot
        $insert_sql = "INSERT INTO slots (turf_id, customer_id, booking_id, slot_date, slot_time, is_booked)
                       VALUES (?, ?, ?, ?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iisss", $turf_id, $customer_id, $booking_id, $slot_date, $slot_time);

        if ($insert_stmt->execute()) {
            $success_slots[] = $slot_time;
        } else {
            $failed_slots[] = $slot_time;
        }
    } else {
        $failed_slots[] = $slot_time;
    }
}

// -----------------------
// Insert notification to customer
// -----------------------
if (!empty($success_slots) && $owner_id) {
    $message = "Hello! Your booking for '$turf_name' on " . $slot_date . " has been confirmed.";
    $sender_type = 'owner';
    $receiver_type = 'customer';
    
    $notif_sql = "INSERT INTO notifications (sender_id, receiver_id, sender_type, receiver_type, message, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_stmt->bind_param("iisss", $owner_id, $customer_id, $sender_type, $receiver_type, $message);
    $notif_stmt->execute();
}
// -----------------------
// If all slots failed, delete empty booking
// -----------------------
if (empty($success_slots)) {
    $conn->query("DELETE FROM booking WHERE booking_id = '$booking_id'");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Booking Confirmation</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #e8f5e9;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  text-align: center;
  animation: popin 0.6s ease-out;
  max-width: 500px;
}
@keyframes popin {
  0% { transform: scale(0.5); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
h1 { color: #2e7d32; }
.btn {
  margin-top: 20px;
  padding: 10px 20px;
  background: #388e3c;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: bold;
}
.btn:hover { background: #2e7d32; }
.error { color: red; font-weight: bold; margin-top: 20px; }
ul { list-style: none; padding: 0; }
</style>
</head>
<body>
<div class="card">
<?php if (!empty($success_slots)): ?>
    <h1>🎉 Booking Confirmed!</h1>
    <p>Booking ID: <strong><?= htmlspecialchars($booking_id) ?></strong></p>
    <p>Your booked slots on <strong><?= htmlspecialchars($slot_date) ?></strong>:</p>
    <ul>
    <?php foreach ($success_slots as $slot): ?>
        <li><?= htmlspecialchars(substr($slot,0,5)) ?></li>
    <?php endforeach; ?>
    </ul>
    <p><strong>Total Price:</strong> ₹<?= number_format($total_price,2); ?></p>
<?php endif; ?>

<?php if (!empty($failed_slots)): ?>
    <p class="error">Some slots were already booked:</p>
    <ul>
    <?php foreach ($failed_slots as $slot): ?>
        <li><?= htmlspecialchars(substr($slot,0,5)) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="booking.php?turf_id=<?= $turf_id ?>" class="btn">Back to Booking</a>
</div>
</body>
</html>
