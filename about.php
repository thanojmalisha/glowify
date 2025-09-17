<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Glowify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="assets/css/about.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Glowify</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="product.php" class="nav-link">Products</a></li>
        <li class="nav-item"><a href="about.php" class="nav-link active">About</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link active">contact</a></li>
        <li class="nav-item"><a href="index.php#testimonials" class="nav-link">Reviews</a></li>
        <li class="nav-item"><a href="index.php#contact" class="nav-link">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="about-hero">
  <div class="hero-content">
    <h1>Why Choose Glowify?</h1>
    <p>Where beauty meets care with innovation, trust, and love.</p>
  </div>
</section>

<!-- About Us Section -->
<section class="about-section container">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h2>Our Story</h2>
      <p>
        Glowify started with a simple mission: to make self-care accessible, affordable, and effective for everyone.  
        From humble beginnings in Colombo, we have grown into a trusted beauty brand serving thousands of happy customers across Sri Lanka.  
        With a focus on <strong>natural ingredients</strong>, <strong>eco-friendly packaging</strong>, and <strong>scientific innovation</strong>, we are redefining beauty for the modern world.
      </p>
      <p>
        Our products are crafted with love, tested with care, and delivered with passion—because your glow is our pride.  
      </p>
    </div>
    <div class="col-md-5">
      <img src="uploads/group.jpg" alt="Glowify Team" class="img-fluid rounded-4 shadow">
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-3 stat">
        <h3>10+</h3>
        <p>Years of Experience</p>
      </div>
      <div class="col-md-3 stat">
        <h3>50K+</h3>
        <p>Happy Customers</p>
      </div>
      <div class="col-md-3 stat">
        <h3>120+</h3>
        <p>Products Launched</p>
      </div>
      <div class="col-md-3 stat">
        <h3>100%</h3>
        <p>Cruelty-Free</p>
      </div>
    </div>
  </div>
</section>

<!-- Our Team -->
<section class="team">
  <div class="container">
    <h2>Meet Our Experts</h2>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="team-card">
          <img src="uploads/MYNEW4TO.png" alt="Dermatologist">
          <h5>Chathuka Edirisinghe</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="team-card">
          <img src="uploads/kavishi.jpg" alt="Dermatologist">
          <h5>Kavishi Hirunodhya</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="team-card">
          <img src="uploads/thanoj1.jpg" alt="Product Designer">
          <h5>Thanoj Maleesha</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="team-card">
          <img src="uploads/imesha.jpg" alt="Marketing">
          <h5>Sewmini imesha </h5>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <h5>Glowify</h5>
        <p>Your trusted brand for beauty & self-care products.</p>
      </div>
      <div class="col-md-3">
        <h5>Quick Links</h5>
        <a href="index.php">Home</a>
        <a href="product.php">Products</a>
        <a href="about.php">Why Us</a>
      </div>
      <div class="col-md-3">
        <h5>Support</h5>
        <a href="#">FAQ</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms & Conditions</a>
      </div>
      <div class="col-md-3 text-center">
        <h5>Follow Us</h5>
        <div class="social">
          <a href="https://www.facebook.com"><i class="fab fa-facebook"></i></a>
          <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
          <a href="https://www.twitter.com"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>
    <hr class="mt-4" style="border-color:rgba(255,255,255,0.1);">
    <p class="text-center">&copy; <?= date("Y") ?> Glowify. All Rights Reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
