<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['owner_id'])) {
    header("Location: owner_login.php");
    exit;
}

$owner_id = $_SESSION['owner_id'];
$owner_name = $_SESSION['owner_name'] ?? "Owner";

$today = date("Y-m-d");

// Fetch today's bookings
$booking_sql = "SELECT b.booking_id, b.booking_date, b.status_id,
                       c.name AS customer_name, c.email, c.phone,
                       s.slot_date, s.slot_time,
                       t.name AS turf_name
                FROM booking b
                JOIN slots s ON b.booking_id = s.booking_id
                JOIN customer c ON b.customer_id = c.customer_id
                JOIN turf t ON b.turf_id = t.turf_id
                WHERE s.slot_date = ? AND t.owner_id = ?
                ORDER BY s.slot_time ASC";
$stmt = $conn->prepare($booking_sql);
$stmt->bind_param("si", $today, $owner_id);
$stmt->execute();
$today_bookings = $stmt->get_result();

// Count turfs
$count_turf_sql = "SELECT COUNT(*) as total_turfs FROM turf WHERE owner_id = ? AND is_deleted = 0";
$stmt = $conn->prepare($count_turf_sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$turf_count = $stmt->get_result()->fetch_assoc()['total_turfs'];

// Count all bookings
$count_book_sql = "SELECT COUNT(*) as total_bookings 
                   FROM booking b 
                   JOIN turf t ON b.turf_id = t.turf_id 
                   WHERE t.owner_id = ?";
$stmt = $conn->prepare($count_book_sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$booking_count = $stmt->get_result()->fetch_assoc()['total_bookings'];

// Today's bookings count
$today_sql = "SELECT COUNT(*) as todays_bookings 
              FROM booking b
              JOIN turf t ON b.turf_id = t.turf_id
              JOIN slots s ON b.booking_id = s.booking_id
              WHERE DATE(s.slot_date) = ? AND t.owner_id = ?";
$stmt = $conn->prepare($today_sql);
$stmt->bind_param("si", $today, $owner_id);
$stmt->execute();
$todays_count = $stmt->get_result()->fetch_assoc()['todays_bookings'];

// Today's earnings
$earn_sql = "SELECT SUM(CASE 
                    WHEN TIME(s.slot_time) BETWEEN '06:00:00' AND '17:59:59' THEN t.price_day
                    ELSE t.price_night
                  END) as todays_earnings
            FROM booking b
            JOIN slots s ON b.booking_id = s.booking_id
            JOIN turf t ON b.turf_id = t.turf_id
            WHERE DATE(s.slot_date) = ? AND t.owner_id = ?";
$stmt = $conn->prepare($earn_sql);
$stmt->bind_param("si", $today, $owner_id);
$stmt->execute();
$earnings = $stmt->get_result()->fetch_assoc()['todays_earnings'] ?? 0;

// Fetch turfs with images
$sql = "SELECT t.*, a.city, a.state,
               (SELECT GROUP_CONCAT(image_path) FROM turf_images WHERE turf_id = t.turf_id) AS images
        FROM turf t
        JOIN tbl_address a ON t.address_id = a.address_id
        WHERE t.owner_id = ? AND t.is_deleted = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$turfs = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['turf_images'])) {
    $turf_id = intval($_POST['turf_id']);
    $targetDir = "uploads/turf_images/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // 🔹 Verify turf belongs to owner
    $checkSql = "SELECT turf_id FROM turf WHERE turf_id = ? AND owner_id = ? AND is_deleted = 0";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $turf_id, $owner_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        die("Error: Turf does not exist or does not belong to you.");
    }

    // 🔹 Count existing images
    $countSql = "SELECT COUNT(*) AS total FROM turf_images WHERE turf_id = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("i", $turf_id);
    $countStmt->execute();
    $totalImages = $countStmt->get_result()->fetch_assoc()['total'];

    // 🔹 Check how many files selected
    $filesCount = count($_FILES['turf_images']['name']);
    $allowedSlots = 10 - $totalImages;

    if ($allowedSlots <= 0) {
        die("Error: Maximum 10 images allowed for this turf.");
    }

    if ($filesCount > $allowedSlots) {
        die("Error: You can upload only {$allowedSlots} more image(s).");
    }

    // 🔹 Process uploads
    foreach ($_FILES['turf_images']['name'] as $key => $name) {
        if (!empty($_FILES['turf_images']['name'][$key])) {
            $fileName = basename($_FILES['turf_images']['name'][$key]);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowTypes = ['jpg','jpeg','png','gif','webp'];

            if (in_array($fileType, $allowTypes)) {
                $newName = time() . "_" . uniqid() . "." . $fileType;
                $targetFilePath = $targetDir . $newName;

                if (move_uploaded_file($_FILES['turf_images']['tmp_name'][$key], $targetFilePath)) {
                    // Save to DB
                    $sql = "INSERT INTO turf_images (turf_id, image_path) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $turf_id, $targetFilePath);
                    $stmt->execute();
                }
            }
        }
    }

    header("Location: owner_dashboard.php");
    exit;
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Owner Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  margin: 0; padding: 0; display: flex; height: 100vh;
  background: #f4f6f9;
}
.sidebar { width: 240px; background: linear-gradient(180deg,#000,#002402); color: white; height: 100vh;
  display: flex; flex-direction: column; position: fixed; left: 0; top: 0; padding: 20px 0; box-shadow: 2px 0 6px rgba(0,0,0,0.15); overflow-y:auto;
}
.sidebar-header { display:flex; align-items:center; justify-content:center; gap:10px; padding:10px 20px; margin-bottom:20px; }
.sidebar-header .icon { background:#a3e635; color:#000; font-weight:bold; font-size:18px; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
.sidebar-header span { font-weight:bold; font-size:16px; }
.menu-card { padding: 12px 20px; cursor:pointer; transition: all 0.3s; display:flex; justify-content:space-between; align-items:center; font-size:15px; border-left:4px solid transparent; }
.menu-card:hover, .menu-card.active-card { background: rgba(255,255,255,0.1); border-left:4px solid #fff; color: #a3e635; }
.menu-card span { font-weight:bold; }
.turf-list { margin-top:4px; display:none; flex-direction:column; gap:4px; }
.turf-list.show { display:flex; }
.turf-list button { background:none; border:none; color:#fff; text-align:left; padding:8px 20px; cursor:pointer; border-left:4px solid transparent; transition: all 0.2s; font-size:14px; }
.turf-list button:hover, .turf-list button.active { background: rgba(255,255,255,0.1); border-left:4px solid #a3e635; }
.content { margin-left: 240px; flex:1; padding:20px; overflow-y:auto; }
.turf-card { display:flex; flex-direction:column; background:#fff; border-radius:14px; padding:16px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
.turf-card h3 { margin:10px 0 6px;color:#1b5e20; }
.turf-card p { margin:4px 0; }
.actions { margin-top:12px; display:flex; gap:6px; flex-wrap:wrap; }
.action-btn, .actions a { background:#2e7d32; color:#fff; padding:8px 12px; border-radius:6px; text-decoration:none; cursor:pointer; font-size:13px; }
.action-btn:hover, .actions a:hover { background:#1b5e20; }

/* Today's Bookings Table */
#today table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
#today th { background: #2e7d32; color: #fff; padding: 12px; text-align: left; font-size: 14px; }
#today td { padding: 10px 12px; font-size: 14px; border-bottom: 1px solid #e0e0e0; }
#today tr:nth-child(even) { background: #f9f9f9; }
#today tr:hover { background: #f1f8e9; }
#today .action-btn { background: #388e3c; border: none; padding: 6px 12px; font-size: 13px; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
#today .action-btn:hover { background: #1b5e20; }
.status-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; text-align: center; }
.status-pending { background: #fff3cd; color: #856404; }
.status-verified { background: #d4edda; color: #155724; }

/* Responsive Table */
@media (max-width: 768px) {
  #today table, #today thead, #today tbody, #today th, #today td, #today tr { display: block; }
  #today thead tr { display: none; }
  #today tr { margin-bottom: 12px; border-bottom: 2px solid #e0e0e0; padding: 8px; border-radius: 8px; background: #fff; }
  #today td { padding: 8px; text-align: right; position: relative; }
  #today td::before { content: attr(data-label); position: absolute; left: 12px; font-weight: bold; color: #555; text-align: left; }
}

/* Image Slider */
.image-slider {
  position: relative;
  width: 100%;
  max-width: 500px;
  height: 300px;
  overflow: hidden;
  border-radius: 10px;
}

.slides {
  position: relative;
  width: 100%;
  height: 100%;
}

.slide {
  display: none;
  width: 100%;
  height: 100%;
}

.slide.active {
  display: block;
}

.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}

.prev, .next {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  font-size: 20px;
  padding: 10px;
  cursor: pointer;
  border-radius: 50%;
  z-index: 10;
}

.prev { left: 10px; }
.next { right: 10px; }

.dots {
  text-align: center;
  position: absolute;
  bottom: 10px;
  width: 100%;
}

.dot {
  height: 12px;
  width: 12px;
  margin: 0 4px;
  background-color: rgba(255,255,255,0.6);
  border-radius: 50%;
  display: inline-block;
  cursor: pointer;
}

.dot.active {
  background-color: #00cc66;
}


/* Modal Styles */
.modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; 
         background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; }
.modal-content { background:#fff; padding:20px; border-radius:10px; width:90%; max-width:400px; }
.close { float:right; font-size:20px; cursor:pointer; }
.modal-content h3 { margin-top:0; color:#1b5e20; }
.modal-content button { margin-top:10px; padding:8px 12px; background:#2e7d32; color:#fff; border:none; border-radius:6px; cursor:pointer; }
.modal-content button:hover { background:#1b5e20; }

/* Fixed Logout Button */
.logout-btn {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: #d32f2f;
    color: #fff;
    padding: 12px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    transition: background 0.3s, transform 0.2s;
    z-index: 1000;
}
.logout-btn:hover {
    background: #b71c1c;
    transform: translateY(-2px);
}

</style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-header">
    <div class="icon">👤</div>
    <span><?php echo htmlspecialchars($owner_name); ?></span>
  </div>
<div class="menu-card" onclick="location.href='turf_register.php'">
  <span>Add New Turf</span>
</div>


  <div class="menu-card" onclick="toggleTurfs(this)">
    <span>Total Turfs</span><span><?php echo $turf_count;?></span>
  </div>
  <div class="turf-list" id="turfList"></div>

  <div class="menu-card" onclick="showSection('bookings', this)">
    <span>Total Bookings</span><span><?php echo $booking_count;?></span>
  </div>
  <div class="menu-card" onclick="showSection('today', this)">
    <span>Today's Bookings</span><span><?php echo $todays_count;?></span>
  </div>
  <div class="menu-card" onclick="showSection('earnings', this)">
    <span>Today's Earnings</span><span>₹<?php echo number_format($earnings,2);?></span>
  </div>
</div>

<div class="content">
  <h2>Welcome, <?php echo htmlspecialchars($owner_name);?></h2>

  <div id="turfs" class="section active">
    <div id="turfDisplay"></div>
  </div>

  <div id="bookings" class="section" style="display:none;">
    <h3>Total Bookings</h3>
    <p>You have <b><?php echo $booking_count;?></b> bookings across all your turfs.</p>
  </div>

  <div id="today" class="section" style="display:none;">
    <h3>Today's Bookings (<?php echo $today;?>)</h3>
    <?php if($today_bookings->num_rows>0): ?>
    <table border="1" cellspacing="0" cellpadding="6">
      <tr>
        <th>Booking ID</th>
        <th>Turf</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Slot Date</th>
        <th>Slot Time</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php while($b=$today_bookings->fetch_assoc()): ?>
        <tr>
          <td><?php echo $b['booking_id']; ?></td>
          <td><?php echo htmlspecialchars($b['turf_name']);?></td>
          <td><?php echo htmlspecialchars($b['customer_name']);?></td>
          <td><?php echo htmlspecialchars($b['email']);?></td>
          <td><?php echo htmlspecialchars($b['phone']);?></td>
          <td><?php echo htmlspecialchars($b['slot_date']);?></td>
          <td><?php echo htmlspecialchars(substr($b['slot_time'],0,5));?></td>
          <td>
            <?php 
              if ($b['status_id'] == 1) { echo "Pending"; } 
              elseif ($b['status_id'] == 2) { echo "Verified ✅"; } 
              else { echo "Unknown"; }
            ?>
          </td>
          <td data-label="Status" id="status-<?php echo $b['booking_id']; ?>">
            <?php if ($b['status_id'] == 1): ?>
              <button type="button" class="action-btn verify-btn" data-id="<?php echo $b['booking_id']; ?>">Verify</button>
            <?php elseif ($b['status_id'] == 2): ?>
              <span class="status-badge status-verified">Verified ✅</span>
            <?php else: ?>
              <span class="status-badge">Unknown</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
    <?php else: ?>
      <p>No bookings today.</p>
    <?php endif; ?>
  </div>

  <div id="earnings" class="section" style="display:none;">
    <h3>Today's Earnings</h3>
    <p>You have earned <b>₹<?php echo number_format($earnings,2);?></b> from today's bookings.</p>
  </div>
</div>
 <!-- Upload Images Modal -->
<div id="imageModal" class="modal" style="display:none;" >
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Upload Turf Images</h3>
    <form id="uploadForm" method="post" enctype="multipart/form-data">
      <input type="hidden" name="turf_id" id="modalTurfId">
      <input type="file" name="turf_images[]" multiple required>
      <button type="submit">Upload</button>
    </form>
  </div>
</div>
<form action="logout.php" method="POST">
    <button type="submit" class="logout-btn">Logout</button>
</form>

<script>
const sections = document.querySelectorAll('.section');

function openModal(turfId){
  document.getElementById("modalTurfId").value = turfId;
  document.getElementById("imageModal").style.display = "flex";
}
function closeModal(){
  document.getElementById("imageModal").style.display = "none";
}
function showSection(id, el) {
  // Hide all sections
  sections.forEach(s => s.style.display = "none");

  // Show requested section
  const section = document.getElementById(id);
  if(section) section.style.display = "block";

  // Update active menu
  document.querySelectorAll('.menu-card').forEach(c => c.classList.remove('active-card'));
  if(el) el.classList.add('active-card');

  // Special: show turf list only when 'turfs' section is active
  if(id === 'turfs'){
    document.getElementById('turfList').classList.add('show');
  } else {
    document.getElementById('turfList').classList.remove('show');
  }
}

// Toggle turfs list without hiding section
let turfListVisible = false; // track turf list state

function toggleTurfs(el) {
    const turfList = document.getElementById('turfList');

    // Toggle visibility
    turfListVisible = !turfListVisible;
    if (turfListVisible) {
        turfList.classList.add('show');
        showSection('turfs', el); // show turf section when opening
    } else {
        turfList.classList.remove('show');
    }

    // Toggle active menu card
    document.querySelectorAll('.menu-card').forEach(c => c.classList.remove('active-card'));
    if(turfListVisible) el.classList.add('active-card');
}



document.addEventListener('DOMContentLoaded', function(){
  const turfs = <?php
    $turfs->data_seek(0);
    $list=[];
    while($row=$turfs->fetch_assoc()){
        $row['images'] = $row['images'] ? explode(',', $row['images']) : [];
        $list[]=$row;
    }
    echo json_encode($list);
  ?>;

  const turfListEl = document.getElementById('turfList');
  const turfDisplay = document.getElementById('turfDisplay');

  turfs.forEach(t=>{
    const btn=document.createElement('button');
    btn.textContent=t.name;
    btn.id='turfBtn-'+t.turf_id;
    btn.onclick=()=>showTurf(t.turf_id);
    turfListEl.appendChild(btn);
  });


function showTurf(turfId) {
  turfDisplay.innerHTML = '';
  const turf = turfs.find(t => t.turf_id == turfId);

  // Images for this turf
  const images = turf.images.length > 0 ? turf.images : [turf.image_path || 'default_turf.jpg'];
  let sliderHtml = `
    <div class="image-slider" id="slider-${turfId}">
      <div class="slides">
  `;

  images.forEach((img, idx) => {
    sliderHtml += `<div class="slide${idx === 0 ? ' active' : ''}">
                     <img src="${img}">
                   </div>`;
  });

  sliderHtml += `
      </div>
      <button class="prev" onclick="changeSlide(${turfId}, -1)">❮</button>
      <button class="next" onclick="changeSlide(${turfId}, 1)">❯</button>
      <div class="dots" id="dots-${turfId}">
  `;

  images.forEach((_, idx) => {
    sliderHtml += `<span class="dot${idx === 0 ? ' active' : ''}" onclick="goToSlide(${turfId}, ${idx})"></span>`;
  });

  sliderHtml += `</div></div>`;

  const div = document.createElement('div');
  div.className = 'turf-card';
  div.innerHTML = `
    ${sliderHtml}
    <h3>${turf.name}</h3>
    <p>Category: ${turf.category}</p>
    <p>Size: ${turf.size} sq ft | Grass: ${turf.grass_type}</p>
    <p>Location: ${turf.city}, ${turf.state}</p>
    <p>Price: Day ₹${parseFloat(turf.price_day).toFixed(2)} / Night ₹${parseFloat(turf.price_night).toFixed(2)}</p>
    <div class="actions">
      <a class="action-btn" href="javascript:void(0)" onclick="openModal(${turf.turf_id})">Add / Update Image</a>
      <a class="action-btn" href="add_slot.php?turf_id=${turf.turf_id}">Edit Slots</a>
      <a class="action-btn" href="view_bookings.php?turf_id=${turf.turf_id}">View Bookings</a>
      <a class="action-btn" href="edit_turf.php?turf_id=${turf.turf_id}">Edit Turf</a>
    </div>
  `;
  turfDisplay.appendChild(div);

  turfs.forEach(t => document.getElementById('turfBtn-' + t.turf_id).classList.remove('active'));
  document.getElementById('turfBtn-' + turfId).classList.add('active');

  // Initialize index for this turf
  window['currentSlide_' + turfId] = 0;
}
function changeSlide(turfId, direction) {
  const slides = document.querySelectorAll(`#slider-${turfId} .slide`);
  const dots = document.querySelectorAll(`#dots-${turfId} .dot`);
  let currentIndex = window['currentSlide_' + turfId] || 0;

  slides[currentIndex].classList.remove('active');
  dots[currentIndex].classList.remove('active');

  currentIndex = (currentIndex + direction + slides.length) % slides.length;

  slides[currentIndex].classList.add('active');
  dots[currentIndex].classList.add('active');

  window['currentSlide_' + turfId] = currentIndex;
}

function goToSlide(turfId, index) {
  const slides = document.querySelectorAll(`#slider-${turfId} .slide`);
  const dots = document.querySelectorAll(`#dots-${turfId} .dot`);
  let currentIndex = window['currentSlide_' + turfId] || 0;

  slides[currentIndex].classList.remove('active');
  dots[currentIndex].classList.remove('active');

  slides[index].classList.add('active');
  dots[index].classList.add('active');

  window['currentSlide_' + turfId] = index;
}



  if(turfs.length>0){
    document.getElementById('turfList').classList.add('show');
    showTurf(turfs[0].turf_id);
    document.getElementById('turfBtn-'+turfs[0].turf_id).classList.add('active');
  }

  document.querySelectorAll('.verify-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const bookingId = this.getAttribute('data-id');
      fetch('verify_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'booking_id=' + bookingId
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
          const cell = document.getElementById('status-' + bookingId);
          cell.innerHTML = '<span class="status-badge status-verified">Verified ✅</span>';
        } else {
          alert(data.message || 'Verification failed');
        }
      })
      .catch(err => console.error(err));
    });
  });
window.changeSlide = changeSlide;
window.goToSlide = goToSlide;
});


</script>
</body>
</html>
