<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'config.php';

$user_id = $_SESSION['user_id'];
$user_res = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = '$user_id' LIMIT 1");
$user = mysqli_fetch_assoc($user_res);

$sql = "
    SELECT 
        b.booking_id,
        b.booking_date,
        b.status_id,
        t.turf_id,
        t.name AS turf_name,
        t.price_day,
        t.price_night
    FROM booking b
    JOIN turf t ON b.turf_id = t.turf_id
    WHERE b.customer_id = '$user_id'
      AND b.is_deleted = 0
    ORDER BY b.booking_date DESC
";
$res = mysqli_query($conn, $sql);

$bookings = [];
while ($row = mysqli_fetch_assoc($res)) {
$stmt = $conn->prepare("SELECT slot_date, slot_time FROM slots WHERE booking_id = ?");
$stmt->bind_param("s", $row['booking_id']);
$stmt->execute();
$slotRes = $stmt->get_result();



    $slots = [];
    $slot_date = null;
    $total = 0;
    while($s = mysqli_fetch_assoc($slotRes)){ 
        $slots[] = $s['slot_time']; 
        $slot_date = $s['slot_date']; 
    }
    $row['slots'] = $slots;
    $row['slot_date'] = $slot_date;

    // Fetch review if exists
$stmt2 = $conn->prepare("SELECT rating, review_text FROM tbl_reviews WHERE booking_id = ? LIMIT 1");
$stmt2->bind_param("s", $row['booking_id']);
$stmt2->execute();
$reviewRes = $stmt2->get_result();
$review = mysqli_fetch_assoc($reviewRes);
$row['review'] = $review; // null if no review yet


    $total = 0;
    foreach($slots as $st){
      $hour = intval(substr($st, 0, 2));
      if($hour >= 6 && $hour < 18){
        $total += $row['price_day'];
      } else {
        $total += $row['price_night'];
      }
    }
    $row['total_price'] = $total;
    $bookings[] = $row;
}

$today = strtotime(date('Y-m-d'));
$totalCount = count($bookings);
$upcoming = [];
$past = [];
foreach ($bookings as $b) {
    if (strtotime($b['slot_date']) >= $today) {
        $upcoming[] = $b;
    } else {
        $past[] = $b;
    }
}
$upcomingCount = count($upcoming);
$pastCount = count($past);

function e($str){ return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
// Fetch all reviews submitted by this customer
$myReviewStmt = $conn->prepare("
    SELECT r.rating, r.review_text, b.booking_id, t.name AS turf_name, b.booking_date, s.slot_date
    FROM tbl_reviews r
    JOIN booking b ON r.booking_id = b.booking_id
    JOIN turf t ON r.turf_id = t.turf_id
    JOIN slots s ON s.booking_id = b.booking_id
    WHERE r.customer_id = ?
    GROUP BY r.review_id
    ORDER BY b.booking_date DESC
");
$myReviewStmt->bind_param("i", $user_id);
$myReviewStmt->execute();
$myReviewRes = $myReviewStmt->get_result();
$myReviews = [];
while($row = mysqli_fetch_assoc($myReviewRes)){
    $myReviews[] = $row;
}
$myReviewCount = count($myReviews);
?>

<?php
if(isset($_POST['submit_review'])){
    $booking_id = $_POST['booking_id'];
    $turf_id = $_POST['turf_id'];
    $user_id = $_SESSION['user_id']; // assuming user is logged in
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO tbl_reviews (booking_id, turf_id, customer_id, rating,review_text) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiis", $booking_id, $turf_id, $user_id, $rating, $comment);
    if($stmt->execute()){
        echo "<script>alert('Review submitted successfully'); window.location='';</script>";
    } else {
        echo "<script>alert('Failed to submit review');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Segoe UI",sans-serif;background:rgba(193,217,255,0.9);display:flex;min-height:100vh;color:#fff;flex-direction:column}
:root{
  --accent:#16a34a;
  --accent-dark:#15803d;
  --surface:#1e293b;
  --muted:#cbd5e1;
  --danger:#dc2626;
  --danger-dark:#b91c1c;
}
/* Sidebar */
.sidebar{width:250px;background:#0f172a;color:#fff;display:flex;flex-direction:column;padding:20px;position:fixed;top:0;bottom:0;left:0}
.sidebar h2{text-align:center;color:var(--accent);margin-bottom:30px;font-size:1.4rem}
.sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin:6px 0;border-radius:8px;transition:background .2s;white-space:nowrap}
.sidebar a:hover,.sidebar a.active{background:var(--accent)}
.sidebar .logout{margin-top:auto;background:rgba(255, 255, 255, 0.1);text-align:center}
/* Main */
.main{flex:1;margin-left:250px;padding:20px;transition:margin-left .3s}
.topbar{background:var(--surface);padding:15px 20px;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,.3);display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px}
.topbar h2{font-size:1.2rem}
.btn{background:var(--accent);color:#fff;padding:8px 16px;border:none;border-radius:25px;text-decoration:none;cursor:pointer;font-size:.9rem}
.btn:hover{background:var(--accent-dark)}
/* Cards */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:20px}
.card{background:var(--surface);border-radius:12px;padding:16px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,.3)}
.card h3{color:var(--accent);margin-bottom:6px;font-size:1rem}
.card p{font-size:1.2rem;font-weight:bold}
/* Panels */
.panel{display:none;background:var(--surface);border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.3)}
.panel.active{display:block}
.section-title{text-align:center;font-size:1.2rem;margin-bottom:14px}
/* Ticket */
.ticket{display:flex;flex-direction:row;justify-content:space-between;background:#fff;color:#000;margin:16px auto;border-radius:12px;max-width:520px;box-shadow:0 4px 8px rgba(0,0,0,.3);cursor:pointer;overflow:hidden;position:relative}
.ticket:before,.ticket:after{content:"";position:absolute;top:50%;width:24px;height:24px;background:rgba(30,41,59,0.9);border-radius:50%;transform:translateY(-50%)}
.ticket:before{left:-12px}.ticket:after{right:-12px}
.ticket-left,.ticket-right{padding:16px}
.ticket-left{flex:3;background:#f8fafc}
.ticket-right{flex:1;background:#16a34a;display:flex;justify-content:center;align-items:center}
.ticket-left h4{margin-bottom:6px;color:#111;font-size:1rem}
.ticket-left a{color:#111;text-decoration:none;font-weight:bold}
.ticket-left a:hover{color:#16a34a;text-decoration:underline}
.ticket-left span{display:block;color:#555;font-size:.85rem;margin-top:4px}
.ticket-left .price{color:#16a34a;font-weight:bold;margin-top:8px}
.ticket-right img{width:70px;height:70px;max-width:100%}
/* Modal */
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.6);justify-content:center;align-items:center;z-index:1000;padding:10px}
.modal-content{background:#fff;color:#000;padding:20px;border-radius:12px;max-width:420px;width:100%;box-shadow:0 4px 12px rgba(0,0,0,.5);overflow-y:auto;max-height:90vh}
.modal h3{color:#16a34a;margin-bottom:10px;text-align:center;font-size:1.1rem}
.modal p{margin:6px 0;text-align:center;font-size:.9rem}
.slot-list{margin:10px 0;text-align:center}
.slot-list span{display:inline-block;background:#f0fdf4;color:#000;padding:6px 12px;margin:4px;border-radius:6px;border:1px solid #16a34a;font-size:.85rem}
.modal-actions{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-top:15px}
.cancel-btn,.close-btn{flex:1;min-width:120px;text-align:center;padding:10px;border:none;border-radius:6px;cursor:pointer;font-size:.9rem}
.cancel-btn{background:var(--danger);color:#fff}
.cancel-btn:hover{background:var(--danger-dark)}
.close-btn{background:#6b7280;color:#fff}
.close-btn:hover{background:#374151}
/* Responsive */
@media(max-width:900px){
  body{flex-direction:column}
  .sidebar{position:relative;width:100%;flex-direction:row;overflow-x:auto;align-items:center;justify-content:space-around;gap:6px;padding:10px}
  .sidebar h2{margin-bottom:0;font-size:1rem}
  .main{margin-left:0}
  .ticket{flex-direction:column;align-items:center;text-align:center}
  .ticket-left,.ticket-right{width:100%;padding:12px}
  .ticket-right img{width:60px;height:60px}
}
@media(max-width:500px){
  .topbar h2{font-size:1rem}
  .btn{padding:6px 12px;font-size:.8rem}
  .card p{font-size:1rem}
  .ticket-left h4{font-size:.9rem}
}


.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px 20px;
  margin-bottom: 20px;
}
.sidebar-header .icon {
  background: #a3e635;
  color: #000;
  font-weight: bold;
  font-size: 18px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.sidebar-header span {
  font-weight: bold;
  font-size: 16px;
}
</style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <h2>TurfZone</h2>
 <div class="sidebar-header">
  <div class="icon">👤</div>
  <span><?php echo htmlspecialchars($user['name'] ?? 'Customer'); ?></span>
  <hr style="margin:10px 0; border:none; height:1px; background:rgba(255,255,255,0.3);">
</div>


  <a id="link-book" class="active" href="javascript:void(0)" onclick="showPanel('book')">Book Turf</a>
  <a id="link-upcoming" href="javascript:void(0)" onclick="showPanel('upcoming')">Upcoming</a>
  <a id="link-history" href="javascript:void(0)" onclick="showPanel('history')">History</a>
  <a id="link-notifications" href="javascript:void(0)" onclick="showPanel('notifications')">Notifications</a>
  <a id="link-myreviews" href="javascript:void(0)" onclick="showPanel('myreviews')">My Reviews</a>


  <a class="logout" href="logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <h2>Welcome, <?php echo e($user['name']); ?></h2>
  </div>


  <!-- Panels -->
  <div id="book" class="panel active">
    <h2 class="section-title">Total Bookings  <?php echo $totalCount; ?> </h2>
    <p style="text-align:center">Find and book your favorite turf easily.</p>
    <div style="text-align:center;margin-top:12px;">
      <a href="home.php" class="btn">Book Now</a>
    </div>
  </div>
<div id="upcoming" class="panel">
  <h2 class="section-title"><?php echo $upcomingCount . "  "; ?>Upcoming Bookings</h2>
  <?php if (empty($upcoming)): ?>
    <p style="text-align:center;color:var(--muted)">No upcoming bookings.</p>
  <?php else: foreach ($upcoming as $u): ?>
    
    <!-- Booking wrapper -->
    <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:16px; border-bottom:1px solid #ccc; padding-bottom:12px;">
      
      <!-- Ticket -->
      <div class="ticket" style="cursor:default; flex:none; width:520px; display:flex; flex-direction:row;">
        <div class="ticket-left" style="flex:3; padding:16px; background:#f8fafc; border-radius:12px 0 0 12px;">
          <h4><?php echo e($u['turf_name']); ?></h4>
          <span><strong>User:</strong> <?php echo e($user['name']); ?></span>
          <span><strong>Booking ID:</strong> <strong><?php echo e($u['booking_id']); ?></strong></span>
          <span><strong>Booking Date:</strong> <?php echo e($u['booking_date']); ?></span>
          <span><strong>Slot Date:</strong> <?php echo e($u['slot_date']); ?></span>
          <?php if(!empty($u['slots'])): ?>
            <span><strong>Slot Time:</strong> <?php echo e(implode(", ", $u['slots'])); ?></span>
          <?php endif; ?>
          <div class="price"><strong>Total:</strong> ₹<?php echo e($u['total_price']); ?></div>
        </div>

        <div class="ticket-right" style="display:flex; justify-content:center; align-items:center; background:#16a34a; color:#fff; font-weight:bold; font-size:18px; border-radius:0 12px 12px 0; padding:16px;">
          TurfZone
        </div>
      </div>

      <!-- Buttons next to ticket -->
      <div style="display:flex; flex-direction:column; gap:6px; min-width:140px; margin-left:0;">
        <!-- Download Invoice (top) -->
        <a href="invoice.php?booking_id=<?php echo $u['booking_id']; ?>" target="_blank" class="btn" style="width:140px; text-align:center;">Download Invoice</a>

        <!-- Cancel Booking (below) -->
        <form method="POST" action="cancel_booking.php" onsubmit="return confirm('Cancel this booking?');">
          <input type="hidden" name="booking_id" value="<?php echo $u['booking_id']; ?>">
          <button type="submit" class="cancel-btn" style="width:140px;">Cancel Booking</button>
        </form>
      </div>

    </div>
    
  <?php endforeach; endif; ?>
</div>


  <div id="history" class="panel">
  <h2 class="section-title"><?php echo $pastCount."  "; ?>Past Bookings</h2>
  <?php if(empty($past)): ?>
    <p style="text-align:center;color:var(--muted)">No past bookings found.</p>
  <?php else: foreach($past as $p): ?>
    
    <!-- Past Booking Ticket -->
    <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:16px; border-bottom:1px solid #ccc; padding-bottom:12px;">
      
      <div class="ticket" style="cursor:default; flex:none; width:520px; display:flex; flex-direction:row;">
        <div class="ticket-left" style="flex:3; padding:16px; background:#f8fafc; border-radius:12px 0 0 12px;">
          <h4><?php echo e($p['turf_name']); ?></h4>
          <span><strong>User:</strong> <?php echo e($user['name']); ?></span>
          <span><strong>Booking ID:</strong> <?php echo e($p['booking_id']); ?></span>
          <span><strong>Booking Date:</strong> <?php echo e($p['booking_date']); ?></span>
          <span><strong>Slot Date:</strong> <?php echo e($p['slot_date']); ?></span>
          <?php if(!empty($p['slots'])): ?>
            <span><strong>Slot Time:</strong> <?php echo e(implode(", ", $p['slots'])); ?></span>
          <?php endif; ?>
          <div class="price"><strong>Total:</strong> ₹<?php echo e($p['total_price']); ?></div>

          <!-- Review Section -->
          <div style="margin-top:8px;">
            <?php if($p['review']): ?>
              <div class="stars-display" style="display:flex; gap:4px; font-size:18px;">
                <?php
                  for($i=1;$i<=5;$i++){
                    echo '<span style="color:'.($i<=$p['review']['rating']?'gold':'#ccc').'">★</span>';
                  }
                ?>
              </div>
              <p style="margin-top:4px; font-size:14px;"><?php echo e($p['review']['review_text']); ?></p>
            <?php else: ?>
              <button class="btn review-btn" data-booking="<?php echo e($p['booking_id']); ?>" data-turf="<?php echo e($p['turf_id']); ?>">Leave Review</button>
            <?php endif; ?>
          </div>
        </div>

          <div class="ticket-right" style="display:flex; justify-content:center; align-items:center; background:#16a34a; color:#fff; font-weight:bold; font-size:18px; border-radius:0 12px 12px 0; padding:16px;">
          TurfZone
        </div>
      </div>

    </div>

  <?php endforeach; endif; ?>
</div>
<div id="notifications" class="panel">
  <h2 class="section-title">Notifications</h2>

  <?php
  // Fetch notifications for logged-in customer
  $stmt = $conn->prepare("
      SELECT n.message, n.created_at, n.sender_type, 
             CASE 
               WHEN n.sender_type='customer' THEN c.name
               WHEN n.sender_type='owner' THEN o.name
               ELSE 'System' 
             END AS sender_name
      FROM notifications n
      LEFT JOIN customer c ON n.sender_type='customer' AND n.sender_id = c.customer_id
      LEFT JOIN owner o ON n.sender_type='owner' AND n.sender_id = o.owner_id
      WHERE n.receiver_type = 'customer' AND n.receiver_id = ?
      ORDER BY n.created_at DESC
  ");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $notifRes = $stmt->get_result();
  ?>

  <div style="max-width:500px; margin:0 auto; display:flex; flex-direction:column; gap:12px;">
    <?php if(mysqli_num_rows($notifRes) > 0): ?>
      <?php while($n = mysqli_fetch_assoc($notifRes)): ?>
        <div style="background:#f8fafc; color:#000; border-radius:12px; padding:12px 16px; box-shadow:0 2px 6px rgba(0,0,0,0.15); display:flex; flex-direction:column;">
          <div style="font-size:0.85rem; color:#555; margin-bottom:6px;">
            <strong><?php echo htmlspecialchars($n['sender_name']); ?></strong> 
            <em style="font-size:0.75rem; color:#999;">(<?php echo htmlspecialchars(ucfirst($n['sender_type'])); ?>)</em>
          </div>
          <div style="font-size:0.95rem; margin-bottom:6px;"><?php echo htmlspecialchars($n['message']); ?></div>
          <div style="font-size:0.7rem; color:#888; align-self:flex-end;">
            <?php echo date("d M Y H:i", strtotime($n['created_at'])); ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; color:#666; font-size:14px;">No notifications found.</p>
    <?php endif; ?>
  </div>
</div>

<div id="myreviews" class="panel">
  <h2 class="section-title" style="color:white;">My Reviews (<?php echo $myReviewCount; ?>)</h2>
  <?php if(empty($myReviews)): ?>
    <p style="text-align:center;color:var(--muted)">You haven't submitted any reviews yet.</p>
  <?php else: foreach($myReviews as $r): ?>
    <div style="display:flex; flex-direction:column; gap:6px; background:white; color:black; padding:12px 16px; border-radius:12px; margin-bottom:12px; box-shadow:0 2px 6px rgba(0,0,0,0.2);">
      <h4 style="color:black;"><?php echo e($r['turf_name']); ?></h4>
    
      <div class="stars-display" style="display:flex; gap:4px; font-size:18px;">
        <?php
          for($i=1;$i<=5;$i++){
            echo '<span style="color:'.($i <= $r['rating'] ? 'gold' : '#ccc').'">★</span>';
          }
        ?>
      </div>
      <p style="font-size:14px; margin-top:4px;"><?php echo e($r['review_text']); ?></p>
    </div>
  <?php endforeach; endif; ?>
</div>


  
<div id="reviewModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Leave Your Review</h3>
    <form id="reviewForm" method="post" action="">
      <input type="hidden" name="booking_id" id="booking_id">
      <input type="hidden" name="turf_id" id="turf_id">

      <!-- Star Rating -->
      <div class="rating-box">
        <label>Rating:</label>
        <div class="stars">
          <input type="radio" name="rating" value="5" id="star5" required>
          <label for="star5">★</label>
          <input type="radio" name="rating" value="4" id="star4">
          <label for="star4">★</label>
          <input type="radio" name="rating" value="3" id="star3">
          <label for="star3">★</label>
          <input type="radio" name="rating" value="2" id="star2">
          <label for="star2">★</label>
          <input type="radio" name="rating" value="1" id="star1">
          <label for="star1">★</label>
        </div>
      </div>

      <!-- Comment Box -->
      <div class="comment-box">
        <textarea name="comment" placeholder="Write your feedback..." required></textarea>
      </div>

      <button type="submit" name="submit_review">Submit Review</button>
    </form>
  </div>
</div>

<style>
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000; }
.modal-content { background:#fff; color:#000; padding:20px; border-radius:12px; max-width:420px; width:100%; box-shadow:0 4px 12px rgba(0,0,0,0.5); overflow-y:auto; max-height:90vh; position:relative; }
.close { position:absolute; top:10px; right:15px; font-size:22px; cursor:pointer; color:#333; }
.stars { display:flex; flex-direction:row-reverse; justify-content:center; }
.stars input { display:none; }
.stars label { font-size:28px; color:#ccc; cursor:pointer; transition:color 0.2s; }
.stars input:checked ~ label, .stars label:hover, .stars label:hover ~ label { color:gold; }
.comment-box textarea { width:100%; min-height:80px; resize:none; border-radius:8px; border:1px solid #ddd; padding:10px; font-size:14px; margin-top:10px; }
.comment-box textarea:focus { border:1px solid #28a745; outline:none; }
button[type="submit"] { width:100%; background:#28a745; border:none; padding:12px; border-radius:8px; font-size:16px; color:#fff; cursor:pointer; margin-top:15px; }
button[type="submit"]:hover { background:#218838; }
</style>


<!-- Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <div id="modal-body"></div>
    <div class="modal-actions" id="modal-actions"></div>
  </div>
</div>

<script>
function showPanel(id){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  document.querySelectorAll('.sidebar a').forEach(a=>a.classList.remove('active'));
  document.getElementById('link-'+id).classList.add('active');
}
function openModal(data){
  const d = JSON.parse(data);
  let slotHtml = '';
  if(d.slots && d.slots.length){
    slotHtml = '<div class="slot-list">'+d.slots.map(s=>`<span>${s}</span>`).join('')+'</div>';
  }
  document.getElementById('modal-body').innerHTML = `
    <h3>${d.turf_name}</h3>
    <p><strong>Booking Date:</strong> ${d.booking_date}</p>
    <p><strong>Slot Date:</strong> ${d.slot_date}</p>
    ${slotHtml}
    <p><strong>Total Price:</strong> ₹${d.total_price}</p>
  `;
  document.getElementById('modal-actions').innerHTML = `
    <form method="POST" action="cancel_booking.php" onsubmit="return confirm('Cancel this booking?');">
      <input type="hidden" name="booking_id" value="${d.booking_id}">
      <button type="submit" class="cancel-btn">Cancel Booking</button>
    </form>
    <button type="button" class="close-btn" onclick="closeModal()">Close</button>
  `;
  document.getElementById('modal').style.display='flex';
}
function closeModal(){ document.getElementById('modal').style.display='none'; }

const modal = document.getElementById('reviewModal');
const closeBtn = modal.querySelector('.close');

document.querySelectorAll('.review-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('booking_id').value = btn.dataset.booking;
    document.getElementById('turf_id').value = btn.dataset.turf;
    modal.style.display = 'block';
  });
});

closeBtn.onclick = () => modal.style.display = 'none';
window.onclick = e => { if(e.target == modal) modal.style.display = 'none'; }


const reviewModal = document.getElementById('reviewModal');
const reviewClose = reviewModal.querySelector('.close');

// Open modal on button click
document.querySelectorAll('.review-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('booking_id').value = btn.dataset.booking;
    document.getElementById('turf_id').value = btn.dataset.turf;
    reviewModal.style.display = 'flex';
  });
});

// Close modal
reviewClose.addEventListener('click', () => { reviewModal.style.display = 'none'; });
window.addEventListener('click', e => { if(e.target === reviewModal) reviewModal.style.display = 'none'; });
</script>
</body>
</html>
