
<?php
require_once 'config.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$size = $_GET['size'] ?? '';
$grass_type = $_GET['grass_type'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT turf.*, tbl_address.city, tbl_address.state 
        FROM turf 
        JOIN tbl_address ON turf.address_id = tbl_address.address_id 
        WHERE turf.is_deleted = 0 AND turf.is_approved = 1";

if (!empty($search)) {
  $search_escaped = mysqli_real_escape_string($conn, $search);
  $sql .= " AND (turf.name LIKE '%$search_escaped%' OR tbl_address.city LIKE '%$search_escaped%')";
}
if (!empty($category)) {
  $category_escaped = mysqli_real_escape_string($conn, $category);
  $sql .= " AND turf.category = '$category_escaped'";
}
if (!empty($grass_type)) {
  $grass_type_escaped = mysqli_real_escape_string($conn, $grass_type);
  $sql .= " AND turf.grass_type = '$grass_type_escaped'";
}
if (!empty($size)) {
  $size_escaped = mysqli_real_escape_string($conn, $size);
  $sql .= " AND turf.size = '$size_escaped'";
}
if ($sort == 'asc') {
  $sql .= " ORDER BY turf.price_day ASC";
} elseif ($sort == 'desc') {
  $sql .= " ORDER BY turf.price_day DESC";
} elseif ($sort == 'rating_asc') {
  $sql .= " ORDER BY (SELECT AVG(rating) FROM tbl_reviews WHERE tbl_reviews.turf_id = turf.turf_id) ASC";
} elseif ($sort == 'rating_desc') {
  $sql .= " ORDER BY (SELECT AVG(rating) FROM tbl_reviews WHERE tbl_reviews.turf_id = turf.turf_id) DESC";
}

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $turf_id = $row['turf_id'];
    $avgRes = mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM tbl_reviews WHERE turf_id = $turf_id");
    $avgData = mysqli_fetch_assoc($avgRes);
    $avg_rating = round($avgData['avg_rating'], 1);
    $review_count = $avgData['review_count'];

    $stars_html = '';
    $fullStars = floor($avg_rating);
    $halfStar = ($avg_rating - $fullStars) >= 0.5 ? true : false;
    for ($i = 1; $i <= 5; $i++) {
      if ($i <= $fullStars) {
        $stars_html .= '<span style="color:gold;font-size:1.2vw;min-font-size:14px;">★</span>';
      } elseif ($i == $fullStars + 1 && $halfStar) {
        $stars_html .= '<span style="color:gold;font-size:1.2vw;min-font-size:14px;">☆</span>';
      } else {
        $stars_html .= '<span style="color:#ccc;font-size:1.2vw;min-font-size:14px;">★</span>';
      }
    }

    echo '
<div class="card">
  <img src="' . htmlspecialchars($row['image_path']) . '" alt="Turf Image">
  <div class="card-content">
    <h3>' . htmlspecialchars($row['name']) . '</h3>
    <p><i class="bi bi-controller" style="color:#16a34a;"></i> <strong>Category:</strong> ' . htmlspecialchars($row['category']) . '</p>
    <p><i class="bi bi-tree" style="color:#16a34a;"></i> <strong>Grass:</strong> ' . htmlspecialchars($row['grass_type']) . '</p>
    <p><i class="bi bi-people" style="color:#16a34a;"></i> <strong>Size:</strong> ' . htmlspecialchars($row['size']) .'\'s</p>
    <p><i class="bi bi-geo-alt" style="color:#16a34a;"></i> <strong>Location:</strong> ' . htmlspecialchars($row['city']) . ', ' . htmlspecialchars($row['state']) . '</p>
    <p><i class="bi bi-currency-rupee" style="color:#16a34a;"></i> <strong>Day Price:</strong> ₹' . htmlspecialchars($row['price_day']) . '</p>
    <p><i class="bi bi-star-fill" style="color:#16a34a;"></i> <strong>Ratings:</strong> ' . $stars_html . ' <span style="font-size:clamp(0.8rem,0.9vw,1rem);color:#555;">(' . $review_count . ')</span></p>
    <a href="booking.php?turf_id=' . $row['turf_id'] . '" class="btn-primary">Book Now</a>
  </div>
</div>';

  }
} else {
  echo '<p style="text-align:center;font-size:clamp(1rem,1.2vw,1.5rem);">No turfs found matching your filters.</p>';
}

mysqli_close($conn);
?>