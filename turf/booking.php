<?php
session_start();
require_once 'config.php';

if (!isset($_GET['turf_id'])) {
    die("No turf selected.");
}

$turf_id = intval($_GET['turf_id']);

// Fetch turf details
$turf_sql = "SELECT turf.*, tbl_address.city, tbl_address.state 
             FROM turf 
             JOIN tbl_address ON turf.address_id = tbl_address.address_id 
             WHERE turf.turf_id = ? AND turf.is_approved = 1 AND turf.is_deleted = 0";
$turf_stmt = $conn->prepare($turf_sql);
$turf_stmt->bind_param("i", $turf_id);
$turf_stmt->execute();
$turf_result = $turf_stmt->get_result();

if ($turf_result->num_rows === 0) {
    die("Turf not found or not approved.");
}

$turf = $turf_result->fetch_assoc();

// Fetch additional turf images
$turf_images = [];
$image_stmt = $conn->prepare("SELECT image_path FROM turf_images WHERE turf_id = ?");
$image_stmt->bind_param("i", $turf_id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();
while($row = $image_result->fetch_assoc()) {
    $turf_images[] = $row['image_path'];
}

// Generate next 7 days
$days = [];
for ($i = 0; $i < 7; $i++) {
    $days[] = date("Y-m-d", strtotime("+$i day"));
}
$selected_day = $_GET['day'] ?? $days[0];

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

// Fetch reviews
$reviews = [];
$review_stmt = $conn->prepare("SELECT r.rating, r.review_text, r.created_at, c.name AS customer_name 
                               FROM tbl_reviews r
                               JOIN customer c ON r.customer_id = c.customer_id
                               WHERE r.turf_id = ?
                               ORDER BY r.created_at DESC");
$review_stmt->bind_param("i", $turf_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result();
while($row = $review_result->fetch_assoc()) {
    $reviews[] = $row;
}

// Calculate average rating
$avg_rating = 0;
$total_reviews = count($reviews);
if($total_reviews > 0){
    $sum = 0;
    foreach($reviews as $rev){
        $sum += $rev['rating'];
    }
    $avg_rating = round($sum / $total_reviews, 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Booking - <?php echo htmlspecialchars($turf['name']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: url('images/bg.png') no-repeat center center fixed !important;
    background-size: cover !important;
}
.container { max-width:1000px; margin:40px auto; background:rgba(255,255,255); padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
h1,h2 { text-align:center; color:#29914fff; margin-bottom:20px; }

/* Image Slider */
.image-slider { position:relative; width:100%; max-width:800px; margin:0 auto 20px; border-radius:10px; overflow:hidden; height:400px; }
.slider-wrapper { width:100%; height:100%; position:relative; }
.slider-img { width:100%; height:100%; object-fit:cover; display:none; border-radius:10px; }
.slider-btn { position:absolute; top:50%; transform:translateY(-50%); background:rgba(31,64,55,0.8); color:#fff; border:none; font-size:24px; padding:8px 12px; cursor:pointer; border-radius:5px; z-index:2; }
.slider-btn:hover { background:#388e3c; }
.prev { left:10px; } .next { right:10px; }

/* Calendar */
.calendar { display:flex; justify-content:center; gap:10px; margin-bottom:30px; flex-wrap:wrap; }
.calendar a { padding:10px 15px; border-radius:5px; background:#eee; text-decoration:none; color:#333; transition:background 0.3s ease; }
.calendar a.active, .calendar a:hover { background:#1f4037; color:white; }

/* Slots */
.slots { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
    gap: 15px; 
    opacity: 0; 
    transform: translateY(20px); 
    animation: fadeIn 0.5s forwards; 
}

@keyframes fadeIn { 
    to { opacity: 1; transform: translateY(0); } 
}

.slot { 
    background: #bde7c6ff; 
    padding: 10px; 
    border-radius: 8px; 
    text-align: center; 
    cursor: pointer; 
    min-height: 70px; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    font-size: 16px; 
    font-weight: bold; 
    border: 1px solid #3db341ff; /* added border */
    transition: transform 0.2s, background 0.2s, border-color 0.2s; 
}

.slot:hover { 
    transform: translateY(-2px); 
    border-color: #3db341ff; /* hover border color */
}

.slot.selected { 
    background-color: #3db341ff; 
    color: white; 
    border-color: #1f4037; /* maintain border color when selected */
}

.slot.booked { 
    background: #ccc; 
    color: #777; 
    pointer-events: none; 
    cursor: not-allowed; 
    border-color: #999; /* differentiate booked slots */
}


/* Reviews */
.reviews-container { max-height:400px; overflow-y:auto; display:grid; gap:15px; padding:10px; background:#f9f9f9; border-radius:10px; margin-top:30px; }
.review-card { background:#fff; border-radius:10px; padding:12px 15px; box-shadow:0 2px 6px rgba(0,0,0,0.1); transition:transform 0.2s; }
.review-card:hover { transform:translateY(-2px); }
.review-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:14px; }
.review-header .stars { color:gold; font-size:16px; }
.review-text { font-size:14px; color:#333; margin-bottom:6px; }
.review-date { font-size:12px; color:#777; text-align:right; }
.btn {
    display: inline-block;
    padding: 12px 25px;
    font-size: 16px;
    font-weight: 600;
    color: #fff;                   /* White text */
    background-color: #1f4037;    /* Green background */
    border: 1px solid #22c55e;    /* Dark green border */
    border-radius: 8px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    margin: 0 auto;
}

.btn:hover {
    background-color: #22c55e;    /* Darker green on hover */
    border-color: #16632a;
    transform: translateY(-2px);
}

.btn:active {
    transform: translateY(1px);
}

form.book-slot-form {
    margin-top: 25px;
}

/* Make sure the parent div centers the button */
div {
    text-align: center;
}
.slot-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 15px;
    font-size: 14px;
    font-weight: bold;
    color: #333;
}

.slot-legend div {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-box {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: 2px solid #1f4037;
}

.legend-box.available { background-color: #b7dbb6ff; }
.legend-box.selected { background-color: #3db341ff; }
.legend-box.booked { background-color: #ccc; border-color: #999; }


/* Responsive */
@media(max-width:900px){ .review-card { padding:10px; } .review-header,.review-text{ font-size:13px; } .review-header .stars{ font-size:14px; } }
@media(max-width:600px){ .reviews-container{ max-height:none; } .container{ padding:15px; } .calendar a{ font-size:14px; padding:8px 12px; } .btn{ width:100%; } }
</style>
<script>
let slideIndex = 0;
function showSlide(index){
  const slides = document.querySelectorAll('.slider-img');
  slides.forEach((s,i)=>s.style.display='none');
  if(slides.length>0){
    slideIndex = (index + slides.length) % slides.length;
    slides[slideIndex].style.display='block';
  }
}
function nextSlide(){ showSlide(slideIndex+1); }
function prevSlide(){ showSlide(slideIndex-1); }
document.addEventListener('DOMContentLoaded',()=>{ showSlide(0); });

// Slots selection
const selectedSlots = new Set();
function toggleSlot(start,end,el){
  const key=start+'|'+end;
  if(selectedSlots.has(key)){ selectedSlots.delete(key); el.classList.remove('selected'); }
  else{ selectedSlots.add(key); el.classList.add('selected'); }
  updateHiddenInputs();
}
function updateHiddenInputs(){
  const container=document.getElementById('hiddenInputs');
  container.innerHTML='';
  selectedSlots.forEach(slot=>{
    const input=document.createElement('input');
    input.type='hidden';
    input.name='selected_slots[]';
    input.value=slot;
    container.appendChild(input);
  });
}

// Hide past slots for today based on device time
document.addEventListener('DOMContentLoaded', () => {
    const todayStr = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
    const selectedDay = "<?php echo $selected_day; ?>"; // PHP selected day

    if (selectedDay === todayStr) {
        const now = new Date();
        document.querySelectorAll('.slot').forEach(slot => {
            const slotTime = slot.getAttribute('data-start'); // HH:MM:SS
            const [h, m, s] = slotTime.split(':');
            const slotDateTime = new Date();
            slotDateTime.setHours(parseInt(h), parseInt(m), parseInt(s), 0);

            if (slotDateTime <= now) {
                slot.classList.add('booked'); // mark as unavailable
                slot.removeAttribute('onclick');
                slot.style.cursor = 'not-allowed';
            }
        });
    }
});


</script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
 

  <!-- Image slider -->
  <div class="image-slider">
    <div class="slider-wrapper">
      <img src="<?php echo htmlspecialchars($turf['image_path']); ?>" class="slider-img">
      <?php foreach($turf_images as $img): ?>
        <img src="<?php echo htmlspecialchars($img); ?>" class="slider-img">
      <?php endforeach; ?>
    </div>
    <?php if(count($turf_images)>0): ?>
      <button class="slider-btn prev" onclick="prevSlide()">❮</button>
      <button class="slider-btn next" onclick="nextSlide()">❯</button>
    <?php endif; ?>
  </div>
 <h1><?php echo htmlspecialchars($turf['name']); ?></h1>


  <!-- Turf Info -->
  <p style="text-align:center; margin-bottom:10px;">
    <strong>Location:</strong> <?php echo htmlspecialchars($turf['city']).', '.htmlspecialchars($turf['state']); ?> |
    <strong>Category:</strong> <?php echo htmlspecialchars($turf['category']); ?> |
    <strong>Size:</strong> <?php echo htmlspecialchars($turf['size']); ?> sq ft
  </p>
  <p style="text-align:center; margin-bottom:20px;">
    <strong>Day Price:</strong> ₹<?php echo number_format($turf['price_day'],2); ?> |
    <strong>Night Price:</strong> ₹<?php echo number_format($turf['price_night'],2); ?>
     <hr style="border: 1px solid #1f4037; margin: 30px auto; width: 60%;">
  </p>
    <!-- Description -->
  <?php if(!empty($turf['description'])): ?>
    <p style="text-align:center; margin-bottom:20px;"><?php echo htmlspecialchars($turf['description']); ?></p>
  <?php endif; ?>

  <!-- Directions -->

<a href="<?php echo htmlspecialchars($turf['map_url']); ?>" target="_blank" class="btn">
  <span class="material-icons" style="vertical-align: middle;">location_on</span> Get Directions
</a>


  <!-- Calendar -->
  <h2>Choose a Day</h2>
  <div class="calendar">
    <?php foreach($days as $day): ?>
      <a class="<?php echo ($selected_day==$day)?'active':''; ?>" href="?turf_id=<?php echo $turf_id; ?>&day=<?php echo $day; ?>#slots">
        <?php echo date("D, M j", strtotime($day)); ?>
      </a>
    <?php endforeach; ?>
  </div>
<div class="slot-legend">
  <div><span class="legend-box available"></span> Available</div>
  <div><span class="legend-box selected"></span> Selected</div>
  <div><span class="legend-box booked"></span> Booked</div>
</div>

  <!-- Slots -->
  <h2>Available Slots for <?php echo date("l, F j", strtotime($selected_day)); ?></h2>
  <div id="slots" class="slots">
    <?php foreach($slots as $slot):
      $is_booked = in_array($slot['start_time'],$booked_slots);
    ?>
     <div class="slot <?php echo $is_booked?'booked':''; ?>"
     data-start="<?php echo $slot['start_time']; ?>"
     <?php if(!$is_booked): ?>onclick="toggleSlot('<?php echo $slot['start_time']; ?>','<?php echo $slot['end_time']; ?>',this)"<?php endif; ?>>
    <?php echo substr($slot['start_time'],0,5).' - '.substr($slot['end_time'],0,5); ?>
    </div>

    <?php endforeach; ?>
  </div>

<form action="confirm_booking.php" method="GET" class="book-slot-form" style="text-align:center;">
    <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
    <input type="hidden" name="day" value="<?php echo $selected_day; ?>">
    <div id="hiddenInputs"></div>
    <button class="btn" type="submit">Book Selected Slot(s)</button>
  </form>

  <!-- Reviews -->
  <h2 style="text-align:center; margin-top:40px;">User Reviews</h2>
  <?php if($total_reviews>0): ?>
    <div style="text-align:center; margin-bottom:15px;">
      <span style="font-size:18px; font-weight:bold;">Average Rating:</span>
      <span style="color:gold; font-size:20px;">
        <?php for($i=1;$i<=5;$i++) echo ($i<=round($avg_rating))?'★':'☆'; ?>
      </span>
      <span style="margin-left:8px; font-size:14px; color:#333;">
        (<?php echo $avg_rating; ?> out of 5, <?php echo $total_reviews; ?> reviews)
      </span>
    </div>
  <?php else: ?>
    <p style="text-align:center;">No reviews yet.</p>
  <?php endif; ?>

  <div class="reviews-container">
    <?php foreach($reviews as $rev): ?>
      <div class="review-card">
        <div class="review-header">
          <strong><?php echo htmlspecialchars($rev['customer_name']); ?></strong>
          <span class="stars"><?php for($i=1;$i<=5;$i++) echo ($i<=$rev['rating'])?'★':'☆'; ?></span>
        </div>
        <div class="review-text"><p><?php echo htmlspecialchars($rev['review_text']); ?></p></div>
        <div class="review-date"><?php echo date("M d, Y", strtotime($rev['created_at'])); ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include 'footer.php'; ?>
<script>document.addEventListener('DOMContentLoaded',()=>showSlide(0));</script>
</body>
</html>
