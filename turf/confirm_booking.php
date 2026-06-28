<?php
session_start();
require_once 'config.php';

if (!isset($_GET['turf_id'], $_GET['day'], $_GET['selected_slots'])) {
    die("Missing booking data.");
}

$turf_id = intval($_GET['turf_id']);
$day = $_GET['day'];
$slots = $_GET['selected_slots'];

// Fetch turf details
$stmt = $conn->prepare("SELECT name, image_path, price_day, price_night FROM turf WHERE turf_id = ?");
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Turf not found.");
}

$turf = $result->fetch_assoc();
$price_day = floatval($turf['price_day']);
$price_night = floatval($turf['price_night']);
$turf_name = $turf['name'];

$total_price = 0;
$formatted_slots = [];
$slot_times = [];

foreach ($slots as $slot) {
    list($start, $end) = explode('|', $slot);
    $hour = intval(substr($start, 0, 2));
    $price = ($hour >= 6 && $hour < 18) ? $price_day : $price_night;

    $total_price += $price;

    $formatted_slots[] = [
        'start' => $start,
        'end' => $end,
        'price' => $price
    ];

    // Store only start time to send to finish_booking
    $slot_times[] = $start;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Confirm Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
    }
    .container {
      max-width: 1000px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h1, h2 {
      text-align: center;
      color: #1f4037;
      margin-bottom: 20px;
    }
    .slot-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    .slot {
      background: #e8f5e9;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
    }
    .price {
      margin-top: 10px;
      font-weight: bold;
      color: #388e3c;
    }
    .total {
      text-align: center;
      font-size: 18px;
      margin-top: 30px;
      font-weight: bold;
      color: #2e7d32;
    }
    .btn {
      padding: 10px 20px;
      background: #1f4037;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      margin-top: 20px;
    }
    .btn:hover {
      background: #2e7d32;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Confirm Booking</h1>

  <p style="text-align:center;">
    <strong>Turf:</strong> <?= htmlspecialchars($turf_name) ?><br>
    <strong>Date:</strong> <?= htmlspecialchars(date("l, F j, Y", strtotime($day))) ?>
  </p>

  <h2>Selected Slots</h2>
  <div class="slot-list">
    <?php foreach ($formatted_slots as $slot): ?>
      <div class="slot">
        <?= substr($slot['start'], 0, 5) . ' - ' . substr($slot['end'], 0, 5); ?>
        <div class="price">₹<?= number_format($slot['price'], 2); ?></div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="total">Total Price: ₹<?= number_format($total_price, 2); ?></div>

  <form action="finish_booking.php" method="POST" style="text-align: center;">
    <input type="hidden" name="turf_id" value="<?= $turf_id; ?>">
    <input type="hidden" name="day" value="<?= htmlspecialchars($day); ?>">
    <?php foreach ($slot_times as $time): ?>
      <input type="hidden" name="selected_slots[]" value="<?= htmlspecialchars($time); ?>">
    <?php endforeach; ?>
    <input type="hidden" name="total_price" value="<?= $total_price; ?>">
    <button class="btn" type="submit">Confirm Booking</button>
  </form>
</div>

</body>
</html>
