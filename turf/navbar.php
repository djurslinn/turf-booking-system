<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html>
<head>
<style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f8f9fa;
      color: #222;
      padding-top: 70px; /* Space for fixed navbar */
    }

    .custom-navbar {
      background: #1a1a1a;
      color: white;
      padding: 15px 30px;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }

    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      overflow-x: auto;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
    }

    .nav-links {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
    }

    .nav-links a:hover {
      color: #a3e635;
    }

    .nav-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: white;
    }

    @media (max-width: 900px) {
      .nav-links {
        display: none;
        flex-direction: column;
        background: #1a1a1a;
        width: 100%;
        padding: 10px 0;
      }

      .nav-links.active {
        display: flex;
      }

      .nav-toggle {
        display: block;
      }
    }
</style>
</head>
<nav class="custom-navbar">
  <div class="nav-container">
    <div class="logo">TurfZone</div>
    <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')">☰</button>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>

      <?php if (isset($_SESSION['username'])): ?>
        <a href="customer_profile.php"> <?php echo htmlspecialchars($_SESSION['username']); ?></a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>

    </div>
  </div>
</nav>
</html>
