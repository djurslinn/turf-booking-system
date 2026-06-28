
<?php
require_once 'config.php';
$turf_id = intval($_GET['turf_id'] ?? 0);
$selected_day = $_GET['day'] ?? date("Y-m-d");

// Fetch booked slots
$booked_slots = [];
$booked_stmt = $conn->prepare("SELECT slot_time FROM slots WHERE turf_id = ? AND slot_date = ? AND is_booked = 1");
$booked_stmt->bind_param("is", $turf_id, $selected_day);
$booked_stmt->execute();
$booked_result = $booked_stmt->get_result();
while ($row = $booked_result->fetch_assoc()) {
    $booked_slots[] = $row['slot_time'];
}

// Generate hourly slots
$slots = [];
for ($h = 6; $h < 22; $h++) {
    $slots[] = [
        'start_time' => sprintf("%02d:00:00", $h),
        'end_time'   => sprintf("%02d:00:00", $h + 1)
    ];
}

// Render slots
foreach($slots as $slot):
  $is_booked = in_array($slot['start_time'],$booked_slots);
?>
 <div class="slot <?php echo $is_booked?'booked':''; ?>"
 data-start="<?php echo $slot['start_time']; ?>"
 <?php if(!$is_booked): ?>onclick="toggleSlot('<?php echo $slot['start_time']; ?>','<?php echo $slot['end_time']; ?>',this)"<?php endif; ?>>
<?php echo substr($slot['start_time'],0,5).' - '.substr($slot['end_time'],0,5); ?>
</div>
<?php endforeach; ?>