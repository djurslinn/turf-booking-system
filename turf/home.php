<?php 
session_start(); 
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {     
    header("Location: login.php?error=login_required");     
    exit(); 
}  
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Available Turfs - TurfZone</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    color: #444;
    background: url('images/bg.png') no-repeat center center fixed;
    background-size: cover;
    line-height: 1.5;
    padding-top: 80px; /* Space for fixed navbar */
  }

  /* Navbar */
  .custom-navbar {
    background: #1a1a1a;
    color: white;
    padding: 15px 30px;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
  }
  .logo {
    font-size: 1.8rem;
    font-weight: bold;
    color: #a3e635;
    letter-spacing: 1px;
    text-decoration: none;
  }
  .nav-links {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
  }
  .nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    transition: background 0.2s, color 0.2s;
  }
  .nav-links a:hover, .nav-links a.active {
    background: #222;
    color: #4ade80;
  }

  /* Filters */
  .filter-bar {
    display: flex;
    gap: 1vw;
    flex-wrap: wrap;
    align-items: center;
    background: #222;
    border-radius: 2rem;
    padding: 0.5rem 1rem;
    margin-top: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .filter-bar .input-group {
    position: relative;
    display: flex;
    align-items: center;
    width: 180px;
    min-width: 120px;
  }
 .filter-bar .input-group i {
  position: absolute;
  left: 12px;
  font-size: 18px;
  color: #4ade80;
  pointer-events: none;
}

  .filter-bar .input-group input,
  .filter-bar .input-group select {
    width: 100%;
    padding: 0.7rem 1vw 0.7rem 38px;
    font-size: 1rem;
    border: none;
    border-radius: 1rem;
    outline: none;
    background: #f9f9f9;
    color: #222;
    transition: all 0.3s;
    box-sizing: border-box;
  }
  .filter-bar .input-group input:focus,
  .filter-bar .input-group select:focus {
    background: #fff;
    box-shadow: 0 0 0.8vw rgba(34, 197, 94, 0.2);
  }
  .filter-bar .input-group input::placeholder { color: #666; }

  .filter-bar button, .filter-bar .reset-btn {
    padding: 0.7rem 1.5vw;
    border: none;
    border-radius: 1rem;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    font-weight: bold;
    text-align: center;
    display: inline-block;
    min-width: 120px;
    box-sizing: border-box;
    background: #4ade80;
    color: #222;
    transition: background 0.2s, color 0.2s;
  }
  .filter-bar .input-group.wide-input {
  flex-grow: 2; /* Makes it grow more than the others */
  min-width: 250px; /* Ensures it's not too narrow on small screens */
}
.filter-bar .input-group.wide-input input {
  width: 100%;
}

  .filter-bar button:hover, .filter-bar .reset-btn:hover {
    background: #22c55e;
    color: #fff;
  }
  .filter-bar .reset-btn {
    background: transparent;
    border: 1px solid #4ade80;
    color: #4ade80;
  }

  /* Section */
  .container {
    width: 100%;
    margin: 0 auto;
    padding: 2vw;
    animation: fadeIn 1s ease-in;
    box-sizing: border-box;
  }
  .section-title {
    text-align: center;
    font-size: clamp(1.5rem, 2vw, 2.5rem);
    font-weight: 700;
    margin-bottom: 2vh;
    color: #29914fff;
    letter-spacing: 0.05em;
  }
  @keyframes fadeIn {
    from {opacity: 0; transform: translateY(15px);}
    to {opacity: 1; transform: translateY(0);}
  }

  .turfs-section {
    background: rgba(255,255,255,0.95);
    padding: 2vw;
    border-radius: 1vw;
    margin-bottom: 3vh;
    box-shadow: 0 0.5vw 2vw rgba(0,0,0,0.1);
    width: 100%;
    box-sizing: border-box;
    min-height: 50vh;
  }

  /* Grid */
  .grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2vw;
    justify-items: center;
  }

  /* Card */
  .card {
    background: #fff;
    border-radius: 1vw;
    overflow: hidden;
    box-shadow: 0 0.5vw 1vw rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeIn 1.4s ease-in;
    width: 100%;
    max-width: 320px;
    min-height: 380px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .card:hover {
    transform: translateY(-0.5vh) scale(1.02);
    box-shadow: 0 1vw 2vw rgba(0,0,0,0.2);
  }
  .card img {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
  }
  .card-content {
    padding: 1.2vw;
  }
  .card-content h3 {
    margin: 0 0 0.5vh;
    font-size: clamp(1rem, 1.2vw, 1.5rem);
    font-weight: bold;
    color: #4ade80;
  }
  .card-content p {
    margin: 0.3vh 0;
    font-size: clamp(0.8rem, 0.9vw, 1rem);
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5vw;
  }
  .btn-primary {
    display: inline-block;
    margin: 1vh auto 1.5vh;
    padding: 0.8vh 1.5vw;
    background: #4ade80;
    color: #000;
    text-decoration: none;
    border-radius: 0.5vw;
    font-weight: bold;
    font-size: clamp(0.9rem, 1vw, 1.1rem);
    transition: background 0.3s;
    text-align: center;
  }
  .btn-primary:hover { background: #22c55e; color:#fff; }

  /* Spinner */
  .spinner {
    text-align: center;
    padding: 20px;
    font-size: 1rem;
    color: #4ade80;
  }

  .empty-message {
    text-align: center;
    padding: 20px;
    font-size: 1rem;
    color: #666;
  }

  @media (max-width: 900px) {
    .nav-container { flex-direction: column; align-items: stretch; }
    .filter-bar { flex-direction: column; gap: 1vh; margin-left: 0; margin-top: 1vh;}
  }
  @media (max-width: 600px) {
    .container { padding: 4vw; padding-top: 22vh; }
    .filter-bar { width: 100%; }
  }
</style>
</head>
<body>
  <div class="custom-navbar">
    <div class="nav-container">
      <a href="index.php" class="logo">TurfZone</a>
      <div class="nav-links">
        <a href="index.php" class="active">Home</a>
        <?php if (isset($_SESSION['username'])): ?>
<a href="customer_profile.php" style="display: flex; align-items: center; gap: 0.4rem;">
    <i class="bi bi-person-circle" style="font-size: 1.2rem; color: #4ade80;"></i>
    <?php echo htmlspecialchars($_SESSION['username']); ?>
  </a>        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Filters -->
    <form id="searchForm" class="filter-bar" onsubmit="return false;">
     <div class="input-group wide-input">
  <i class="bi bi-search" aria-hidden="true"></i>
  <input type="text" name="search" id="searchInput" placeholder="Search by name or city">
</div>

<div class="input-group">
  <i class="bi bi-tags" aria-hidden="true"></i>
  <select name="category" id="categorySelect">
    <option value="">All Categories</option>
    <option value="Football">Football</option>
    <option value="Cricket">Cricket</option>
    <option value="Multi_Sport">Multi-sport</option>
  </select>
</div>
<div class="input-group">
  <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
  <select name="size" id="sizeSelect">
    <option value="">All Sizes</option>
    <option value="5">5-a-side</option>
    <option value="7">7-a-side</option>
  </select>
</div>
<div class="input-group">
  <i class="bi bi-sort-up-alt" aria-hidden="true"></i>
  <select name="sort" id="sortSelect">
    <option value="">Sort by</option>
    <option value="asc">Price: Low to High</option>
    <option value="desc">Price: High to Low</option>
    <option value="rating_asc">Rating: Low to High</option>
    <option value="rating_desc">Rating: High to Low</option>
  </select>
</div>

      <button type="submit" id="searchBtn">Search</button>
      <button class="reset-btn" id="viewAllBtn">View All</button>
    </form>
  </div>

<section>
  <div class="container">
    <div class="turfs-section">
      <div style="width:100%;">
        <h2 class="section-title">Available Turfs</h2>
        <div class="grid-3" id="turfsGrid">
          <!-- Turf cards loaded here -->
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('searchForm');
  const turfsGrid = document.getElementById('turfsGrid');
  const viewAllBtn = document.getElementById('viewAllBtn');

  let debounceTimer;

  function fetchTurfs() {
    turfsGrid.innerHTML = '<div class="spinner">Loading...</div>';
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    fetch('ajax_turf_search.php?' + params)
      .then(res => res.text())
      .then(html => {
        turfsGrid.innerHTML = html.trim() ? html : '<p class="empty-message">No turfs found. Try changing filters.</p>';
      });
  }

  // Initial load
  fetchTurfs();

  // Debounce input events
  form.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchTurfs, 300);
  });

  form.addEventListener('change', fetchTurfs);
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    fetchTurfs();
  });

  viewAllBtn.addEventListener('click', function(e) {
    e.preventDefault();
    form.reset();
    fetchTurfs();
  });
});
</script>
</body>
</html>
