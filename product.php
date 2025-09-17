<?php
session_start();

// DB Connection
require_once("includes/database_conn.php");

$customer_id = 1; // Simulated login

// AJAX Add to Cart
if(isset($_POST['action']) && $_POST['action']=='add_to_cart'){
    $pid = intval($_POST['product_id'] ?? 0);
    $qty = intval($_POST['quantity'] ?? 1);

    $check = $conn->prepare("SELECT cart_id, quantity FROM Cart WHERE customer_id=? AND product_id=?");
    $check->bind_param("ii",$customer_id,$pid);
    $check->execute();
    $res = $check->get_result();
    if($res->num_rows){
        $row = $res->fetch_assoc();
        $newQty = $row['quantity'] + $qty;
        $upd = $conn->prepare("UPDATE Cart SET quantity=? WHERE cart_id=?");
        $upd->bind_param("ii",$newQty,$row['cart_id']);
        $upd->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO Cart(customer_id, product_id, quantity) VALUES(?,?,?)");
        $ins->bind_param("iii",$customer_id,$pid,$qty);
        $ins->execute();
    }
    echo "success"; exit;
}

// Fetch products
$products = [];
$res = $conn->query("SELECT p.*, c.name AS category FROM Product p LEFT JOIN Category c ON p.category_id=c.category_id ORDER BY p.product_id DESC");
while($row=$res->fetch_assoc()) $products[]=$row;

// Fetch cart count
$countRes = $conn->query("SELECT COUNT(*) AS cnt FROM Cart WHERE customer_id=$customer_id");
$cartCount = $countRes->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Glowify - Products</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/css/product.css"/>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
<div class="container">
  <a class="navbar-brand" href="#">Glowify</a>
  <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="nav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
      <li class="nav-item"><a href="product.php" class="nav-link active">Products</a></li>
      <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
      <li class="nav-item"><a href="contact.php" class="nav-link active">Contact</a></li>
      <li class="nav-item"><a href="cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Cart (<span id="cart-count"><?= $cartCount ?></span>)</a></li>
    </ul>
  </div>
</div>
</nav>
<!-- Hero -->
<section class="hero-section">
  <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between">
    <div class="hero-text text-lg-start text-center mb-4 mb-lg-0">
      <span class="badge bg-dark px-3 py-2 mb-2">✨ New Arrival</span>
      <h1 class="hero-title">Glowify — <br>Luxury Meets <span style="color:#ffea00;">Science</span></h1>
      <p class="hero-sub mt-3">Premium skincare made with the purest ingredients.</p>
    </div>
    <div class="hero-image text-center">
      <img src="uploads/product.png" alt="Glowify Product">
    </div>
  </div>
</section>

<!-- Products -->
<section class="container my-5">
<h2 class="section-title">Our Products</h2>
<div class="row g-4">
<?php foreach($products as $p): ?>
<div class="col-md-3 d-flex align-items-stretch">
<div class="card w-100">
<img src="<?= $p['image_url'] ?: 'https://via.placeholder.com/300' ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
<div class="card-body d-flex flex-column justify-content-between">
<div>
<h5><?= htmlspecialchars($p['name']) ?></h5>
<p class="price">Rs <?= number_format($p['price'],2) ?></p>
<p><?= htmlspecialchars(substr($p['description'],0,60)) ?>...</p>
</div>
<div class="d-flex justify-content-center align-items-center mt-2">
<input type="number" min="1" value="1" class="form-control qty" data-id="<?= $p['product_id'] ?>">
<button class="add-cart" data-id="<?= $p['product_id'] ?>"><i class="fas fa-cart-plus"></i> Add to Cart</button>
</div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</section>

<footer>
  &copy; <?= date("Y") ?> Glowify. All Rights Reserved.
</footer>

<script>
document.querySelectorAll('.add-cart').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        let pid=btn.dataset.id;
        let qty=parseInt(btn.previousElementSibling.value);
        fetch('product.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=add_to_cart&product_id='+pid+'&quantity='+qty
        }).then(r=>r.text()).then(res=>{
            if(res.trim()=='success') location.reload();
        });
    });
});
</script>
</body>
</html>



