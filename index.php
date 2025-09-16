<?php
session_start();

// DB Connection
require_once("includes/database_conn.php");

// Fetch products
$products = [];
$res = $conn->query("SELECT p.*, c.name AS category FROM Product p LEFT JOIN Category c ON p.category_id=c.category_id ORDER BY p.product_id DESC LIMIT 8");
while($row = $res->fetch_assoc()) $products[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Glowify - Beauty & Care</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/index.css"/>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#">Glowify</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="#" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="product.php" class="nav-link">Products</a></li>
        <li class="nav-item"><a href="#features" class="nav-link">Why Us</a></li>
        <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
        <li class="nav-item"><a href="#testimonials" class="nav-link">Reviews</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
          <li class="nav-item"><a href="Dashboard.php" class="nav-link"><i class="fas fa-cogs"></i> Admin</a></li>
          <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php elseif(isset($_SESSION['username'])): ?>
          <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a href="login.php" class="nav-link"><i class="fas fa-user"></i> Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div class="hero-content">
    <h1>Glow Inside & Out</h1>
    <p>Discover premium skincare, haircare, and wellness products for your daily routine.</p>
    <a href="product.php" class="btn">Explore Collection</a>
    <a href="#features" class="btn">Why Choose Us?</a>
  </div>
</section>


<!-- Products Section -->
<section id="products" class="container my-5">
  <div class="section-title">
    <h2>Our Best Sellers</h2>
    <p>Top picks loved by our customers</p>
  </div>
  <div class="row g-4">
    <?php foreach($products as $p): ?>
    <div class="col-md-3">
      <div class="card h-100">
        <img src="<?= $p['image_url'] ?: 'https://via.placeholder.com/300' ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <div class="card-body text-center">
          <h5><?= htmlspecialchars($p['name']) ?></h5>
          <p class="price">Rs <?= number_format($p['price'],2) ?></p>
          <p><?= htmlspecialchars(substr($p['description'],0,60)) ?>...</p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Video Section -->
<section class="video-section my-5">
  <div class="container text-center">
    <h2 class="mb-4">Watch Glowify in Action</h2>
    <p class="mb-4">Discover how our products bring out your natural glow.</p>
    <div class="video-wrapper">
      <video autoplay muted loop playsinline>
        <source src="uploads/homeV.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
  </div>
</section>


<!-- Features Section -->
<section id="features" class="container my-5 text-center">
  <div class="section-title">
    <h2>Why Choose Glowify?</h2>
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="features">
        <div class="icon"><i class="fas fa-leaf"></i></div>
        <h5>Natural Ingredients</h5>
        <p>We craft products with safe, cruelty-free and eco-friendly formulas.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="features">
        <div class="icon"><i class="fas fa-shipping-fast"></i></div>
        <h5>Fast Delivery</h5>
        <p>Get your beauty essentials delivered at your doorstep quickly & safely.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="features">
        <div class="icon"><i class="fas fa-star"></i></div>
        <h5>Trusted by Many</h5>
        <p>Join thousands of happy customers glowing with our products.</p>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section id="testimonials" class="container my-5">
  <div class="section-title">
    <h2>What Our Customers Say</h2>
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="testimonial">
        <p>"Glowify products transformed my skin! Highly recommend."</p>
        <h6>- Amanda, Colombo</h6>
      </div>
    </div>
    <div class="col-md-4">
      <div class="testimonial">
        <p>"Amazing quality and fast delivery. I’m a loyal customer now."</p>
        <h6>- Nimesh, Kandy</h6>
      </div>
    </div>
    <div class="col-md-4">
      <div class="testimonial">
        <p>"Affordable and natural products that actually work."</p>
        <h6>- Shalini, Galle</h6>
      </div>
    </div>
  </div>
</section>

<!-- Newsletter -->
<section class="why-us my-5">
  <div class="container text-center">
    <h3 class="mb-4">Why Choose Our Brand? 💎</h3>
    <div class="row g-4">
      
      <div class="col-md-4">
        <div class="p-4 rounded shadow text-white" style="background: linear-gradient(135deg, #ff6ec7, #d500f9);">
          <h5>🌱 100% Natural</h5>
          <p>Made with pure botanical ingredients.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 rounded shadow text-white" style="background: linear-gradient(135deg, #ff6ec7, #d500f9);">
          <h5>🐰 Cruelty-Free</h5>
          <p>No animal testing, ever.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 rounded shadow text-white" style="background: linear-gradient(135deg, #ff6ec7, #d500f9)">
          <h5>✨ Proven Results</h5>
          <p>Loved by thousands of happy customers.</p>
        </div>
      </div>

    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


