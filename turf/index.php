<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TurfZone - Book Your Turf</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f8f9fa;
      color: #222;
      padding-top: 0;
    }

    /* Navbar */
    .custom-navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
      transition: all 0.3s ease;
      background: transparent;
    }

    .custom-navbar.scrolled {
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(8px);
      padding: 12px 40px;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: white;
      transition: color 0.3s ease;
    }

    .custom-navbar.scrolled .logo {
      color: #a3e635;
    }

.nav-links {
  display: flex;
  align-items: center; /* optional for vertical alignment */
  gap: 40px; /* increased spacing between menu items */
}


.navbar-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 40px; /* match height with nav links */
  padding: 8px 18px;
  border: 2px solid white;
  border-radius: 20px;
  background: transparent;
  color: white;
  font-weight: 500;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s ease;
  text-decoration: none; /* if converted to <a> */
  line-height: 1;
}

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      position: relative;
      transition: color 0.3s ease;
    }

    .custom-navbar.scrolled .nav-links a {
      color: #f1f5f9;
    }

    .nav-links a:hover {
  color: #a3e635;
}

    .nav-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 1.8rem;
      color: white;
    }

    @media (max-width: 900px) {
      .nav-links {
        display: none;
        flex-direction: column;
        background: rgba(0,0,0,0.85);
        position: absolute;
        top: 100%;
        right: 0;
        width: 200px;
        padding: 15px;
        border-radius: 0 0 8px 8px;
      }

      .nav-links.active {
        display: flex;
      }

      .nav-toggle {
        display: block;
      }
    }

    /* Hero */
    .hero {
  background: url('images/bg.png') no-repeat center center;
  background-size: cover;
  background-attachment: fixed;
  color: white;
  min-height: 100vh; /* makes it fill the viewport height */
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
  padding: 0 20px; /* optional horizontal padding */
}


 .hero::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(255, 255, 255, 0.08); /* semi-transparent white */
  /* Safari support */
  border: 1px solid rgba(255, 255, 255, 0.15); /* optional border for depth */
  z-index: 1;
}
.hero .container {
  position: relative;
  z-index: 2;
  max-width: 800px;
  background: rgba(0, 0, 0, 0.35); /* subtle dark overlay */
  padding: 40px 20px;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
  
}



    .hero h1 {
      font-size: clamp(2rem, 5vw, 3rem);
      margin-bottom: 15px;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 25px;
    }

    .btn-primary {
      background: #16a34a;
      padding: 12px 25px;
      color: white;
      border: none;
      border-radius: 25px;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 10px;
      text-decoration: none;
      display: inline-block;
      transition: background 0.3s ease;
    }

    .btn-primary:hover {
      background: #15803d;
    }

    /* Sections */
    .section {
      padding: 80px 20px;
    }

    .section-title {
      text-align: center;
      margin-bottom: 40px;
      font-size: 2rem;
    }

    .grid-3 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }

    .card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .alt-bg {
      background: #f1f5f9;
    }

    #register-turf {
      background: #ecfdf5;
      padding: 70px 20px;
      text-align: center;
      border-radius: 10px;
      margin: 40px auto;
      max-width: 900px;
    }

    #register-turf a {
      display: inline-block;
      background: #16a34a;
      color: white;
      padding: 12px 30px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }

    #register-turf a:hover {
      background: #15803d;
    }

    .footer {
      background: #111827;
      color: white;
      text-align: center;
      padding: 30px 20px;
    }

    .footer a {
      color: #a3e635;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    /* Login Popup */
    .login-popup {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 2000;
      align-items: center;
      justify-content: center;
    }

    .popup-content {
      background: rgba(30, 41, 59, 0.9);
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      position: relative;
      min-width: 300px;
      box-shadow: 0 0 20px rgba(0,0,0,0.4);
      color: white;
    }

    .popup-content h3 {
      margin-bottom: 20px;
      font-size: 1.4rem;
    }

    .popup-content button {
      margin: 10px;
      padding: 12px 24px;
      background: transparent;
      color: white;
      border: 2px solid white;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
    }

    .popup-content button:hover {
      color: #a3e635;
      border-color: #a3e635;
    }

    .close-popup {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.5rem;
      cursor: pointer;
      color: #ccc;
    }

    .close-popup:hover {
      color: white;
    }
    .feature-card {
  text-align: center;
  padding: 30px 20px;
}

.feature-icon {
  font-size: 3rem;
  color: #16a34a; /* TurfZone green */
  margin-bottom: 15px;
  transition: transform 0.3s ease, color 0.3s ease;
}

.feature-card:hover .feature-icon {
  transform: scale(1.15);
  color: #15803d; /* darker green on hover */
}
.about-container {
  max-width: 850px;
  margin: 0 auto;
  text-align: center;
}

.about-text p {
  margin-bottom: 18px;
  line-height: 1.8;
  font-size: 1.05rem;
  color: #333;
}

.about-list {
  list-style: none;
  padding: 0;
  margin: 30px 0 0;
  display: inline-block; /* keeps it centered */
  text-align: left;
}

.about-list li {
  margin-bottom: 12px;
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.about-list i {
  color: #16a34a; /* TurfZone green */
  font-size: 1.2rem;
}
.contact-icon {
  font-size: 2.2rem;
  color: #16a34a; /* TurfZone green */
  transition: transform 0.3s ease, color 0.3s ease;
}

.contact-icon:hover {
  color: #15803d; /* Darker green on hover */
  transform: scale(1.2);
}


 

.navbar-button:hover {
  border-color: #a3e635;
  color: #a3e635;
}

.custom-navbar.scrolled .navbar-button {
  border-color: #f1f5f9;
  color: #f1f5f9;
}

.custom-navbar.scrolled .navbar-button:hover {
  border-color: #a3e635;
  color: #a3e635;
}

  .text-center {
  text-align: center;
  max-width: 600px; /* optional, to limit width */
  margin: 0 auto; /* centers the block horizontally */
}
  .custom-navbar.scrolled .nav-links a:hover {
  color: #a3e635;
}

  </style>
</head>
<body>

<!-- Navbar -->
<nav class="custom-navbar" id="navbar">
  <div class="logo">TurfZone</div>
  <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')">☰</button>
  <div class="nav-links">
    <a href="#">Home</a>
    <a href="#features">Features</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#about">About</a>
    <a href="#contact">Contact</a>
     <?php if (isset($_SESSION['username'])): ?>
      <a href="customer_profile.php"> <?php echo htmlspecialchars($_SESSION['username']); ?></a>
    <?php else: ?>
<button class="navbar-button" onclick="openLoginPopup()">Login</button>

    <?php endif; ?>
    
    <?php if (isset($_SESSION['owner_id'])): ?>
      <a href="#register-turf">Register Turf</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  
  <div class="container">
    <h1>Book Your Turf Anytime, Anywhere</h1>
    <p>Fast. Easy. Reliable Turf Booking at Your Fingertips</p>
    <a href="home.php" class="btn-primary">Book Your Spot</a>
    
  </div>
</section>

<!-- Sections -->
<!-- Why Choose Us -->
<section class="section" id="features">
  <div class="container">
    <h2 class="section-title">Why Choose TurfZone?</h2>
    <div class="grid-3">

      <div class="card feature-card">
        <i class="bi bi-calendar-check feature-icon"></i>
        <h3>Easy Booking</h3>
        <p>Book your favorite turf in just a few clicks with real-time availability.</p>
      </div>

      <div class="card feature-card">
        <i class="bi bi-geo-alt feature-icon"></i>
        <h3>Multiple Locations</h3>
        <p>Access a network of turfs across your city for maximum flexibility.</p>
      </div>

      <div class="card feature-card">
        <i class="bi bi-check-circle feature-icon"></i>
        <h3>Instant Confirmation</h3>
        <p>Get immediate booking confirmation to save time and avoid stress.</p>
      </div>

    </div>
  </div>
</section>



<section id="register-turf">
  <h2>Own a Turf? List It Now!</h2>
  <p>Reach more players and manage your bookings effortlessly. TurfZone helps turf owners grow faster.</p>
  <a href="owner_login.php">Register Your Turf</a>
</section>

<section class="section alt-bg" id="testimonials">
  <div class="container">
    <h2 class="section-title">What Players Say</h2>
    <div class="grid-3">
      <div class="card">
        <p>"Great platform to find turfs and book slots hassle-free. Highly recommended!"</p>
        <small>- Rahul M.</small>
      </div>
      <div class="card">
        <p>"User-friendly interface and instant confirmation. Love using TurfZone!"</p>
        <small>- Neha S.</small>
      </div>
      <div class="card">
        <p>"Best booking experience I've had. Lots of locations to choose from."</p>
        <small>- Arjun T.</small>
      </div>
    </div>
  </div>
</section>

<!-- About Us -->
<!-- About Us -->
<section class="section" id="about">
  <div class="container about-container">
    <h2 class="section-title">About Us</h2>

    <div class="about-text">
      <p>
        Welcome to <strong>TurfZone</strong> – the ultimate destination where sports dreams come to life! 
        Whether you’re a turf owner or a passionate player, our platform brings convenience, efficiency, and excitement to every game.
      </p>

      <p>
        At TurfZone, we don’t just offer turf bookings – we create experiences. 
        Every match deserves the perfect turf, and our platform ensures it’s just a click away.
      </p>
    </div>
  </div>
</section>



<section class="section alt-bg" id="contact">
  <div class="container">
    <h2 class="section-title">Contact Us</h2>
    <p class="text-center">
      Have questions or suggestions? Reach out to us anytime!
    </p>
    <div class="contact-icons" style="text-align:center; margin-top:20px; display:flex; justify-content:center; gap:30px; flex-wrap:wrap;">
      <a href="mailto:support@turfzone.com" target="_blank" class="contact-icon">
        <i class="bi bi-envelope"></i>
      </a>
      <a href="https://www.facebook.com/YourTurfZonePage" target="_blank" class="contact-icon">
        <i class="bi bi-facebook"></i>
      </a>
      <a href="https://www.instagram.com/_.d_j_u_r_s_l_i_n_n._" target="_blank" class="contact-icon">
        <i class="bi bi-instagram"></i>
      </a>
      <a href="https://wa.me/917559947412" target="_blank" class="contact-icon">
        <i class="bi bi-whatsapp"></i>
      </a>
    </div>
  </div>
</section>


<footer class="footer">
  <p>&copy; 2025 Turf Booker. All rights reserved.</p>
  <p><a href="mailto:support@turfbooker.com">support@turzone.com</a> | +91 7559947412</p>
</footer>

<!-- Login Popup -->
<div id="login-popup" class="login-popup">
  <div class="popup-content">
    <span class="close-popup" onclick="closeLoginPopup()">×</span>
    <h3>Select Login Type</h3>
    <button onclick="window.location.href='login.php'">User Login</button>
    <button onclick="window.location.href='owner_login.php'">Owner Login</button>
    <button onclick="window.location.href='admin_login.php'">Admin Login</button>
  </div>
</div>

<script>
  function openLoginPopup() {
    document.getElementById('login-popup').style.display = 'flex';
  }

  function closeLoginPopup() {
    document.getElementById('login-popup').style.display = 'none';
  }

  window.addEventListener('click', function(e) {
    const popup = document.getElementById('login-popup');
    if (e.target === popup) closeLoginPopup();
  });

  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
  
    const canvas = document.getElementById('grassCanvas');
    const ctx = canvas.getContext('2d');


    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;

    // Increased density for more realistic grass
    const numBlades = 3000;
    const blades = [];
    let mouse = { x: -9999, y: -9999 };
    let windGust = 0;
    let interactionIntensity = 5;

    class GrassBlade {
      constructor(x, baseY) {
        this.baseX = x;
        this.baseY = baseY;
        this.height = 15 + Math.random() * 25;
        this.segments = 5;
        this.segmentLength = this.height / this.segments;

        this.points = [];
        for (let i = 0; i <= this.segments; i++) {
          this.points.push({ x: x, y: baseY - i * this.segmentLength });
        }

        // More natural color variation
        const hueVariation = 10 + Math.random() * 20;
        const saturation = 50 + Math.random() * 20;
        const lightness = 15 + Math.random() * 15;
        this.color = `hsl(${120 - hueVariation}, ${saturation}%, ${lightness}%)`;
        
        // Thinner blades for realism
        this.thickness = 0.5 + Math.random() * 1;
        this.windFactor = 0.3 + Math.random() * 0.5;
        
        // Natural slight curvature
        this.curve = (Math.random() - 0.5) * 3;
      }

      update(time) {
        // Keep base stable
        this.points[0].x = this.baseX;
        this.points[0].y = this.baseY;

        for (let i = 1; i < this.points.length; i++) {
          let prev = this.points[i - 1];
          let point = this.points[i];

          // Pull back toward vertical (horizontal axis only)
          let dx = point.x - this.baseX;
          point.x -= dx * 0.08;

          // Mouse interaction force
          let dxM = point.x - mouse.x;
          let dyM = point.y - mouse.y;
          let dist = Math.sqrt(dxM * dxM + dyM * dyM);
          
          // Increased interaction radius with intensity
          const radius = 50 + (interactionIntensity * 10);
          if (dist < radius) {
            let force = (radius - dist) / radius;
            // Apply intensity to the force
            point.x += (dxM / dist) * force * (interactionIntensity / 2);
          }

          // Wind sway (subtle)
          let sway = Math.sin(time * 0.002 + this.baseX * 0.01 + i) * this.windFactor * 0.8;
          // Add natural curve to the sway
          sway += this.curve * (i/this.segments);
          point.x += sway + (windGust * (i/this.segments));

          // Spring constraints between points
          let dxS = point.x - prev.x;
          let dyS = point.y - prev.y;
          let distS = Math.sqrt(dxS * dxS + dyS * dyS);
          let diff = this.segmentLength - distS;
          let percent = diff / distS / 2;

          let offsetX = dxS * percent;
          point.x += offsetX;
          prev.x -= offsetX;

          // Re-anchor base point
          this.points[0].x = this.baseX;
          this.points[0].y = this.baseY;
        }
      }

      draw(ctx) {
        ctx.beginPath();
        ctx.moveTo(this.points[0].x, this.points[0].y);

        // Use quadratic curves for smooth bending
        for (let i = 1; i < this.points.length - 1; i++) {
          let cpX = this.points[i].x;
          let cpY = this.points[i].y;
          let endX = (this.points[i].x + this.points[i + 1].x) / 2;
          let endY = (this.points[i].y + this.points[i + 1].y) / 2;
          ctx.quadraticCurveTo(cpX, cpY, endX, endY);
        }
        
        // Last segment line
        const last = this.points[this.points.length - 1];
        ctx.lineTo(last.x, last.y);

        ctx.strokeStyle = this.color;
        ctx.lineWidth = this.thickness;
        ctx.lineCap = 'round';
        ctx.stroke();
      }
    }

    function initBlades() {
      blades.length = 0;
      for (let i = 0; i < numBlades; i++) {
        let x = Math.random() * width;
        let baseY = height + (Math.random() * 20);
        blades.push(new GrassBlade(x, baseY));
      }
      
      // Sort blades by y position so overlapping looks more natural
      blades.sort((a, b) => a.baseY - b.baseY);
    }

    function animate(time = 0) {
      ctx.clearRect(0, 0, width, height);

      // Draw a subtle gradient at the base for depth
      let gradient = ctx.createLinearGradient(0, height - 50, 0, height);
      gradient.addColorStop(0, 'rgba(40, 80, 40, 0.3)');
      gradient.addColorStop(1, 'rgba(20, 40, 20, 0.7)');
      ctx.fillStyle = gradient;
      ctx.fillRect(0, height - 50, width, 50);

      for (const blade of blades) {
        blade.update(time);
        blade.draw(ctx);
      }

      windGust *= 0.92;
      requestAnimationFrame(animate);
    }

    // Mouse interaction
    canvas.addEventListener('mousemove', e => {
      mouse.x = e.clientX;
      mouse.y = e.clientY;
    });

    canvas.addEventListener('mouseleave', () => {
      mouse.x = -9999;
      mouse.y = -9999;
    });

    canvas.addEventListener('click', () => {
      windGust = 3;
    });

   canvas.addEventListener('mousemove', e => {
  mouse.x = e.clientX;
  mouse.y = e.clientY;
});

   

    // Window resize handling
    window.addEventListener('resize', () => {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
      initBlades();
    });

    // Initialize and start animation
    initBlades();
    animate();
</script>

</body>
</html>
