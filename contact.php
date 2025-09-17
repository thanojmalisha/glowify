<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Glowify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/contact.css">
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
        <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link active">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="contact-hero">
  <div class="hero-content">
    <h1>Let’s Connect</h1>
    <p>We’re here to help you glow inside & out.</p>
  </div>
</section>

<!-- Contact Options -->
<section class="contact-section container">
  <div class="row g-4">
    <!-- Left Side -->
    <div class="col-md-5">
      <div class="contact-box">
        <h3 class="mb-4">Get In Touch</h3>
        
        <!-- WhatsApp -->
        <a href="https://wa.me/94770522297" target="_blank" class="contact-option text-decoration-none text-dark">
          <i class="fab fa-whatsapp"></i>
          <div>
            <h6 class="mb-0">WhatsApp</h6>
            <small>Chat with us instantly</small>
          </div>
        </a>
        
        <!-- Phone -->
        <a href="tel:+94770522297" class="contact-option text-decoration-none text-dark">
          <i class="fas fa-phone"></i>
          <div>
            <h6 class="mb-0">Call Us</h6>
            <small>077 052 2297</small>
          </div>
        </a>
        
        <!-- Email -->
        <a href="mailto:support@glowify.lk" class="contact-option text-decoration-none text-dark">
          <i class="fas fa-envelope"></i>
          <div>
            <h6 class="mb-0">Email</h6>
            <small>support@glowify.lk</small>
          </div>
        </a>
        
        <!-- Location -->
        <div class="contact-option">
          <a href="https://maps.app.goo.gl/wSrPyFo1TGBNd9bb7?g_st=aw"class="contact-option text-decoration-none text-dark">
          <i class="fas fa-map-marker-alt"></i>
          <div>
            <h6 class="mb-0">Visit Us</h6>
            <small>Colombo 05, Sri Lanka</small>
          </div>
        </a>
        </div>
      </div>
    </div>
    
    <!-- Right Side Form -->
    <div class="col-md-7">
      <div class="contact-box">
        <h3 class="mb-4">Send Us a Message</h3>
        <form class="contact-form" method="post" action="#">
          <input type="text" name="name" placeholder="Your Name" required>
          <input type="email" name="email" placeholder="Your Email" required>
          <input type="text" name="subject" placeholder="Subject">
          <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
          <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>
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
        <a href="contact.php">Contact</a>
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
