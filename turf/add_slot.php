<?php
session_start();
require_once 'config.php';

// 🔐 Replace with your actual login check
if (!isset($_SESSION['owner_id'])) {
  header("Location: login.php");
  exit();
}

$owner_id = $_SESSION['owner_id'];
$msg = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $turf_id = intval($_POST['turf_id']);
  $slot_date = $_POST['slot_date'];
  $start_time = $_POST['start_time'];
  $end_time = $_POST['end_time'];

  $sql = "INSERT INTO slots (turf_id, slot_date, start_time, end_time) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isss", $turf_id, $slot_date, $start_time, $end_time);

  if ($stmt->execute()) {
    $msg = "Slot added successfully!";
  } else {
    $msg = "Error: " . $stmt->error;
  }
}

// Get turfs owned by this owner
$turf_sql = "SELECT turf_id, name FROM turf WHERE owner_id = ? AND is_deleted = 0";
$turf_stmt = $conn->prepare($turf_sql);
$turf_stmt->bind_param("i", $owner_id);
$turf_stmt->execute();
$turf_result = $turf_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Turf Slot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      background-color: #f4f4f4;
    }

    .container {
      max-width: 500px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #1f4037;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      margin-top: 12px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background: #1f4037;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background: #14532d;
    }

    .msg {
      margin-top: 15px;
      text-align: center;
      color: green;
    }

    .error {
      color: red;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Add Turf Slot</h2>

  <?php if ($msg): ?>
    <p class="msg"><?php echo htmlspecialchars($msg); ?></p>
  <?php endif; ?>

  <form method="POST">
    <label for="turf_id">Select Turf:</label>
    <select name="turf_id" id="turf_id" required>
      <?php while ($turf = $turf_result->fetch_assoc()): ?>
        <option value="<?php echo $turf['turf_id']; ?>"><?php echo htmlspecialchars($turf['name']); ?></option>
      <?php endwhile; ?>
    </select>

    <label for="slot_date">Date:</label>
    <input type="date" name="slot_date" id="slot_date" required>

    <label for="start_time">Start Time:</label>
    <input type="time" name="start_time" id="start_time" required>

    <label for="end_time">End Time:</label>
    <input type="time" name="end_time" id="end_time" required>

    <button type="submit">Add Slot</button>
  </form>
</div>

</body>
</html>
