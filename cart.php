<?php
session_start();

// DB Connection
$DB_HOST = "127.0.0.1";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "glowify";
$DB_PORT = 3306;

$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
if($conn->connect_error) die("DB Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

$customer_id = 1; // Simulated login

// AJAX Handlers
if(isset($_POST['action'])){
    $cid = intval($_POST['cart_id'] ?? 0);
    $qty = intval($_POST['quantity'] ?? 1);

    if($_POST['action']=='remove_cart'){
        $conn->query("DELETE FROM Cart WHERE cart_id=$cid AND customer_id=$customer_id");
        echo "success"; exit;
    }
    if($_POST['action']=='update_cart'){
        $conn->query("UPDATE Cart SET quantity=$qty WHERE cart_id=$cid AND customer_id=$customer_id");
        echo "success"; exit;
    }
}

// Fetch cart items
$cart_items=[];
$res = $conn->query("SELECT ct.cart_id, ct.quantity, p.product_id, p.name, p.price, p.image_url 
                     FROM Cart ct JOIN Product p ON ct.product_id=p.product_id 
                     WHERE ct.customer_id=$customer_id");
while($row=$res->fetch_assoc()) $cart_items[]=$row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart - Glowify</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/css/cart.css"/>
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
      <li class="nav-item"><a href="product.php" class="nav-link">Products</a></li>
      <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
      <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
      <li class="nav-item"><a href="cart.php" class="nav-link active"><i class="fas fa-shopping-cart"></i> Cart</a></li>
    </ul>
  </div>
</div>
</nav>

<!-- Cart Section -->
<div class="container my-5">
<div class="cart-card">
<h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Your Cart</h2>

<?php $grand=0; foreach($cart_items as $c): $subtotal=$c['price']*$c['quantity']; $grand+=$subtotal; ?>
  <div class="cart-item" data-cartid="<?= $c['cart_id'] ?>">
    <img src="<?= $c['image_url'] ?: 'https://via.placeholder.com/80' ?>" class="cart-img">
    <div class="item-info">
      <div class="item-name"><?= htmlspecialchars($c['name']) ?></div>
      <div class="item-price">Rs <?= number_format($c['price'],2) ?></div>
    </div>
    <input type="number" class="qty-update" min="1" value="<?= $c['quantity'] ?>">
    <div class="subtotal ms-3">Rs <?= number_format($subtotal,2) ?></div>
    <button class="btn btn-danger btn-sm remove-cart ms-3"><i class="fas fa-trash"></i></button>
  </div>
<?php endforeach; ?>

<div class="summary-box mt-4 d-flex justify-content-between align-items-center">
  <div class="summary-total">Total: Rs <?= number_format($grand,2) ?></div>
  <a href="checkout.php" class="btn btn-success"><i class="fas fa-credit-card"></i> Proceed to Checkout</a>
</div>

</div>
</div>

<footer>
  &copy; <?= date("Y") ?> Glowify. All Rights Reserved.
</footer>

<script>
// Update cart UI
function updateCartUI(){
    let total=0;
    document.querySelectorAll('.cart-item').forEach(item=>{
        let priceText = item.querySelector('.item-price').innerText;
        let price = parseFloat(priceText.replace(/Rs\s*/,'').replace(/,/g,''));
        let qty = parseInt(item.querySelector('.qty-update').value);
        let subtotal = price * qty;
        item.querySelector('.subtotal').innerText = 'Rs ' + subtotal.toFixed(2);
        total += subtotal;
    });
    document.querySelector('.summary-total').innerText='Total: Rs '+total.toFixed(2);
}

// Remove item
document.querySelectorAll('.remove-cart').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        let item=btn.closest('.cart-item');
        let cid=item.dataset.cartid;
        fetch('cart.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=remove_cart&cart_id='+cid
        }).then(r=>r.text()).then(res=>{
            if(res.trim()=='success'){
                item.remove();
                updateCartUI();
            }
        });
    });
});

// Update qty
document.querySelectorAll('.qty-update').forEach(input=>{
    input.addEventListener('change', ()=>{
        let item=input.closest('.cart-item');
        let cid=item.dataset.cartid;
        let qty=input.value;
        if(qty<1) { input.value=1; qty=1; }
        fetch('cart.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=update_cart&cart_id='+cid+'&quantity='+qty
        }).then(r=>r.text()).then(res=>{
            if(res.trim()=='success') updateCartUI();
        });
    });
});
</script>
</body>
</html>