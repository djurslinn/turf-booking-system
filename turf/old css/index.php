<?php
// index.php - Welcome page for Turf Booking Website
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Turf Booking - Welcome</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<style> body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f4f4;
    }
    .hero {
      background: linear-gradient(to right, #00b09b, #96c93d);
      color: white;
      padding: 120px 0;
      text-align: center;
    }
    .hero h1 {
      font-size: 3.5rem;
      font-weight: bold;
    }
    .hero p {
      font-size: 1.25rem;
    }
    .btn-cta {
      background-color: #ffffff;
      color: #00b09b;
      border-radius: 30px;
      padding: 12px 30px;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
    }
    .btn-cta:hover {
      background-color: #e9f5e9;
    }
    .section {
      padding: 60px 0;
    }
    .section-title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 40px;
      text-align: center;
    }
    .feature-card {
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }
    .feature-card:hover {
      transform: translateY(-5px);
    }
    .testimonial-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }
    footer {
      background-color: #fff;
      padding: 20px;
      text-align: center;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1050;
    }
    body {
      padding-top: 70px; /* Adjust based on navbar height */
    }</style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold text-success" href="#">TurfZone</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1>Book Your Turf Anytime, Anywhere</h1>
      <p>Fast. Easy. Reliable Turf Booking at Your Fingertips</p>
      <a href="home.php" class="btn btn-cta mt-4">Book Your Spot</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="section bg-light" id="features">
    <div class="container">
      <div class="section-title">Why Choose TurfZone?</div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card text-center">
            <h5 class="fw-bold">Easy Booking</h5>
            <p>Book your favorite turf in just a few clicks with real-time availability.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <h5 class="fw-bold">Multiple Locations</h5>
            <p>Access a network of turfs across your city for maximum flexibility.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <h5 class="fw-bold">Instant Confirmation</h5>
            <p>Get immediate booking confirmation to save time and avoid stress.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="section bg-light" id="testimonials">
    <div class="container">
      <div class="section-title">What Players Say</div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="testimonial-card">
            <p>"Great platform to find turfs and book slots hassle-free. Highly recommended!"</p>
            <small>- Rahul M.</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial-card">
            <p>"User-friendly interface and instant confirmation. Love using TurfZone!"</p>
            <small>- Neha S.</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial-card">
            <p>"Best booking experience I've had. Lots of locations to choose from."</p>
            <small>- Arjun T.</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Us Section -->
  <section class="section" id="about">
    <div class="container">
      <div class="section-title">About Us</div>
      <p class="text-center w-75 mx-auto">TurfZone is a modern platform built to simplify your turf booking experience. Whether you're a casual player or a regular athlete, we provide the easiest way to find and reserve quality turfs near you. Our mission is to make sports more accessible, enjoyable, and hassle-free for everyone.</p>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="section bg-light" id="contact">
    <div class="container">
      <div class="section-title">Contact Us</div>
      <p class="text-center w-75 mx-auto">Have any questions or suggestions? Reach out to our support team at <strong>support@turfzone.com</strong> or call us at <strong>+91 98765 43210</strong>. We’d love to hear from you!</p>
    </div>
  </section>

  <!-- Footer -->
 <footer>
  <h3>Contact & Support</h3>
  <p>Email: <a href="mailto:support@turfbooker.com">support@turfbooker.com</a></p>
  <p>Phone: +91 98765 43210</p>
  <p>&copy; 2025 Turf Booker. All rights reserved.</p>
</footer>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
