<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['owner_id'])) {
    header("Location: owner_login.php");
    exit;
}

$owner_id = $_SESSION['owner_id'];

if (!isset($_GET['turf_id'])) {
    echo "Turf ID missing.";
    exit;
}

$turf_id = intval($_GET['turf_id']);

// Verify that the turf belongs to the logged-in owner
$check_sql = "SELECT name FROM turf WHERE turf_id = ? AND owner_id = ? AND is_deleted = 0";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $turf_id, $owner_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo "You don't have access to this turf.";
    exit;
}

$turf = $check_result->fetch_assoc();

// Fetch bookings for this turf
$sql = "SELECT b.booking_id, b.booking_date, bs.status_name, c.name AS customer_name
        FROM booking b
        JOIN booking_status bs ON b.status_id = bs.status_id
        JOIN customer c ON b.customer_id = c.customer_id
        WHERE b.turf_id = ? AND b.is_deleted = 0
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Bookings for <?php echo htmlspecialchars($turf['name']); ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #e3f2fd;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: auto;
    }
    h2 {
      text-align: center;
      color: #0d47a1;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }
    th {
      background: #1976d2;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .back-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 16px;
      background: #388e3c;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Bookings for "<?php echo htmlspecialchars($turf['name']); ?>"</h2>

    <?php if ($result->num_rows > 0): ?>
      <table>
        <tr>
          <th>Booking ID</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
            <td><?php echo $row['booking_date']; ?></td>
            <td><?php echo htmlspecialchars($row['status_name']); ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No bookings found for this turf.</p>
    <?php endif; ?>

    <a href="owner_dashboard.php" class="back-btn">← Back to Dashboard</a>
  </div>
</body>
</html>
