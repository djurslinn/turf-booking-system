<?php
session_start();
include 'config.php';

$booking_id = $_GET['booking_id'];

// fetch booking info with turf and owner
$sql = "SELECT b.booking_id, b.booking_date, c.name AS customer_name,
        t.name AS turf_name, t.price_day, t.price_night,
        o.name AS owner_name, o.email, o.phone
        FROM booking b
        JOIN turf t ON b.turf_id = t.turf_id
        JOIN customer c ON b.customer_id = c.customer_id
        JOIN owner o ON t.owner_id = o.owner_id
        WHERE b.booking_id = '$booking_id'";
$res = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($res);

// fetch slots
$slots_res = mysqli_query($conn, "SELECT slot_date, slot_time FROM slots WHERE booking_id='$booking_id'");
$slots = [];
$total = 0;
while($s = mysqli_fetch_assoc($slots_res)){
    $slots[] = $s;
    $hour = intval(substr($s['slot_time'],0,2));
    $total += ($hour>=6 && $hour<18) ? $booking['price_day'] : $booking['price_night'];
}

// Use Google Charts URL directly
$qr_url = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($booking['booking_id']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Invoice</title>
  <style>
    body{font-family:Arial; color:#000;}
    .invoice{max-width:700px; margin:auto; padding:20px; border:1px solid #ccc;}
    h2{color:#16a34a;}
    table{width:100%; border-collapse:collapse; margin-top:10px;}
    th, td{border:1px solid #ccc; padding:8px; text-align:center;}
    th{background:#f0fdf4;}
    .total{font-weight:bold; text-align:right; padding-right:10px;}
    .contact{margin-top:20px; font-size:14px;}
   
    button{margin-top:20px; padding:10px; background:#16a34a; color:#fff; border:none; cursor:pointer;}
  </style>
</head>
<body>
  <div class="invoice">
    <h2>TurfZone Invoice</h2>
    <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?></p>
    <p><strong>Customer:</strong> <?php echo $booking['customer_name']; ?></p>
    <p><strong>Booking Date:</strong> <?php echo $booking['booking_date']; ?></p>
    <p><strong>Turf:</strong> <?php echo $booking['turf_name']; ?></p>

    <table>
      <tr>
        <th>Slot Date</th>
        <th>Slot Time</th>
        <th>Price</th>
      </tr>
      <?php foreach($slots as $s): 
        $hour = intval(substr($s['slot_time'],0,2));
        $price = ($hour>=6 && $hour<18) ? $booking['price_day'] : $booking['price_night'];
      ?>
      <tr>
        <td><?php echo $s['slot_date']; ?></td>
        <td><?php echo $s['slot_time']; ?></td>
        <td>₹<?php echo $price; ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="2" class="total">Total</td>
        <td>₹<?php echo $total; ?></td>
      </tr>
    </table>

    <div class="contact">
      <p><strong>Owner:</strong> <?php echo $booking['owner_name']; ?></p>
      <p><strong>Email:</strong> <?php echo $booking['email']; ?> | <strong>Phone:</strong> <?php echo $booking['phone']; ?></p>
    </div>

    

    <button onclick="window.print()">Download / Print PDF</button>
  </div>
</body>
</html>
