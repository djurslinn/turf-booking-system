<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['owner_id']) || !isset($_GET['turf_id'])) {
    header("Location: login_owner.php");
    exit;
}

$turf_id = intval($_GET['turf_id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price_day = $_POST['price_day'];
    $price_night = $_POST['price_night'];
    $unavailable_days = $_POST['unavailable_days'] ?? [];

    // Update prices
    $update_sql = "UPDATE turf SET price_day = ?, price_night = ? WHERE turf_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ddi", $price_day, $price_night, $turf_id);
    $stmt->execute();

    // Upload image if provided
    if ($_FILES['image']['size'] > 0) {
        $target = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        $img_sql = "UPDATE turf SET image_path = ? WHERE turf_id = ?";
        $img_stmt = $conn->prepare($img_sql);
        $img_stmt->bind_param("si", $target, $turf_id);
        $img_stmt->execute();
    }

    // Delete previous unavailability
    $conn->query("DELETE FROM turf_unavailable_days WHERE turf_id = $turf_id");

    // Insert new unavailable days
    $insert_sql = $conn->prepare("INSERT INTO turf_unavailable_days (turf_id, date) VALUES (?, ?)");
    foreach ($unavailable_days as $day) {
        $insert_sql->bind_param("is", $turf_id, $day);
        $insert_sql->execute();
    }

    echo "<script>alert('Turf updated successfully!'); window.location='owner_dashboard.php';</script>";
    exit;
}

// Fetch turf details
$sql = "SELECT * FROM turf WHERE turf_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$turf = $stmt->get_result()->fetch_assoc();

// Fetch unavailable days
$days_result = $conn->query("SELECT date FROM turf_unavailable_days WHERE turf_id = $turf_id");
$unavailable = [];
while ($row = $days_result->fetch_assoc()) {
    $unavailable[] = $row['date'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Turf</title>
  <style>
    body { font-family: Arial; background: #f1f8e9; padding: 20px; }
    .container { max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; }
    input, label { display: block; width: 100%; margin-bottom: 15px; }
    input[type="submit"] { background: #2e7d32; color: white; border: none; padding: 10px; }
  </style>
</head>
<body>

<div class="container">
  <h2>Edit Turf - <?php echo htmlspecialchars($turf['name']); ?></h2>
  <form method="post" enctype="multipart/form-data">
    <label>Current Image:</label>
    <img src="<?php echo $turf['image_path']; ?>" style="width: 100%; max-height: 200px; object-fit: cover;"><br>

    <label>Upload New Image:</label>
    <input type="file" name="image">

    <label>Price (Day):</label>
    <input type="number" step="0.01" name="price_day" value="<?php echo $turf['price_day']; ?>" required>

    <label>Price (Night):</label>
    <input type="number" step="0.01" name="price_night" value="<?php echo $turf['price_night']; ?>" required>

    <label>Select Unavailable Days (Next 7 Days):</label>
    <?php for ($i = 0; $i < 7; $i++): 
      $date = date("Y-m-d", strtotime("+$i days"));
      ?>
      <label>
        <input type="checkbox" name="unavailable_days[]" value="<?php echo $date; ?>" 
          <?php echo in_array($date, $unavailable) ? 'checked' : ''; ?>>
        <?php echo date("l, M j", strtotime($date)); ?>
      </label>
    <?php endfor; ?>

    <input type="submit" value="Update Turf">
  </form>
</div>

</body>
</html>
