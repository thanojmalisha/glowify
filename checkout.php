<?php
session_start();

require_once("includes/database_conn.php");

$customer_id = 1; // Simulated login

// Fetch cart items
$cart_items=[];
$grand=0;
$res = $conn->query("SELECT ct.cart_id, ct.quantity, p.product_id, p.name, p.price 
                     FROM Cart ct JOIN Product p ON ct.product_id=p.product_id 
                     WHERE ct.customer_id=$customer_id");
while($row=$res->fetch_assoc()){ 
    $row['subtotal']=$row['price']*$row['quantity'];
    $grand+=$row['subtotal'];
    $cart_items[]=$row;
}

// Handle checkout
$message="";
if(isset($_POST['place_order'])){
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $payment = $conn->real_escape_string($_POST['payment']);

    // Insert order
    $stmt = $conn->prepare("INSERT INTO `Order`(customer_id,order_date,total_amount,status) VALUES(?,NOW(),?,'Pending')");
    $stmt->bind_param("id",$customer_id,$grand);
    $stmt->execute();
    $order_id=$stmt->insert_id;

    // Insert items
    foreach($cart_items as $c){
        $pid=$c['product_id']; $qty=$c['quantity']; $price=$c['price'];
        $stmt2=$conn->prepare("INSERT INTO Order_Item(order_id,product_id,quantity,price) VALUES(?,?,?,?)");
        $stmt2->bind_param("iiid",$order_id,$pid,$qty,$price);
        $stmt2->execute();
    }

    // Clear cart
    $conn->query("DELETE FROM Cart WHERE customer_id=$customer_id");

    // Payment-specific messages
    if($payment=="Card"){
        $message="✅ Payment successful via Credit/Debit Card!";
    }elseif($payment=="Bank"){
        // Handle slip upload
        if(isset($_FILES['slip']) && $_FILES['slip']['error']==0){
            $uploadDir="uploads/";
            if(!is_dir($uploadDir)) mkdir($uploadDir);
            $fileName=uniqid()."_".basename($_FILES['slip']['name']);
            move_uploaded_file($_FILES['slip']['tmp_name'],$uploadDir.$fileName);
            $message="✅ Bank transfer slip uploaded successfully. Payment confirmed!";
        }else{
            $message="❌ Please upload your bank transfer slip.";
        }
    }elseif($payment=="COD"){
        $message="🚚 Cash on Delivery selected. You can get your product within 5 days.";
    }

    // Auto redirect after 5s
    echo "<script>alert('$message'); setTimeout(()=>{window.location='index.php';},3000);</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Glowify</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--primary:#ff69b4;--dark:#0f1720;--light:#ffffff;--glass-bg:rgba(255,255,255,0.07);}
body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#0f1720,#1a1f2b);color:var(--light);margin:0;min-height:100vh;display:flex;flex-direction:column;}
.navbar{background:linear-gradient(90deg,#4f46e5,#ff69b4);box-shadow:0 6px 20px rgba(0,0,0,0.4);}
.navbar-brand{font-weight:700;color:#fff !important;letter-spacing:1px;}
.nav-link{color:#fff !important;font-weight:500;}
.nav-link:hover{color:#ffea00 !important;}
.container{flex:1;}
.checkout-card{background:var(--glass-bg);backdrop-filter:blur(18px);border-radius:25px;padding:30px;box-shadow:0 8px 25px rgba(0,0,0,0.5);}
h2{font-weight:700;background:linear-gradient(90deg,#ff69b4,#4f46e5);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.form-control,.form-select{background:#1f2735;border:none;border-radius:15px;color:#fff;padding:12px;}
.form-control:focus,.form-select:focus{outline:none;box-shadow:0 0 0 2px var(--primary);}
.hidden{display:none;}
.order-summary{background:rgba(255,255,255,0.07);border-radius:20px;padding:20px;box-shadow:0 6px 15px rgba(0,0,0,0.4);}
.order-item{display:flex;justify-content:space-between;margin-bottom:12px;font-size:0.95rem;}
.summary-total{font-size:1.5rem;font-weight:700;color:#ffea00;text-shadow:0 0 10px rgba(255,234,0,0.7);}
.btn-success{background:linear-gradient(90deg,#22c55e,#16a34a);border:none;border-radius:18px;padding:14px 28px;font-size:1.1rem;transition:all .3s ease;box-shadow:0 6px 16px rgba(0,0,0,0.4);}
.btn-success:hover{background:linear-gradient(90deg,#16a34a,#15803d);transform:translateY(-2px) scale(1.02);}
footer{background:#111827;border-radius:30px 30px 0 0;padding:18px;text-align:center;font-size:0.9rem;color:#ccc;margin-top:auto;}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg px-4">
  <a class="navbar-brand" href="#">Glowify</a>
  <div class="ms-auto">
    <a href="cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Cart</a>
  </div>
</nav>

<div class="container my-5">
  <div class="checkout-card">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Checkout</h2>
    <div class="row g-4">
      
      <!-- Form -->
      <div class="col-md-6">
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Shipping Address</label>
            <textarea name="address" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment" id="payment" class="form-select" required>
              <option value="">-- Select --</option>
              <option value="Card">💳 Credit / Debit Card</option>
              <option value="COD">💵 Cash on Delivery</option>
              <option value="Bank">🏦 Bank Transfer</option>
            </select>
          </div>
          
          <!-- Card Details -->
          <div id="cardFields" class="hidden">
            <div class="mb-3"><label class="form-label">Card Holder Name</label><input type="text" class="form-control" name="card_name"></div>
            <div class="mb-3"><label class="form-label">Card Number</label><input type="text" class="form-control" name="card_number" maxlength="16"></div>
            <div class="row">
              <div class="col"><label class="form-label">Expiry Date</label><input type="text" class="form-control" name="expiry" placeholder="MM/YY"></div>
              <div class="col"><label class="form-label">CVV</label><input type="text" class="form-control" name="cvv" maxlength="3"></div>
            </div>
          </div>
          
          <!-- Bank Transfer Slip -->
          <div id="bankFields" class="hidden">
            <div class="mb-3">
              <label class="form-label">Upload Bank Transfer Slip</label>
              <input type="file" class="form-control" name="slip" accept="image/*,application/pdf">
            </div>
          </div>
          
          <button type="submit" name="place_order" class="btn btn-success w-100 mt-3">
            <i class="fas fa-lock"></i> Confirm & Pay
          </button>
        </form>
      </div>
      
      <!-- Order Summary -->
      <div class="col-md-6">
        <div class="order-summary">
          <h5 class="mb-3"><i class="fas fa-receipt"></i> Order Summary</h5>
          <?php foreach($cart_items as $c): ?>
            <div class="order-item">
              <span><?= htmlspecialchars($c['name']) ?> (x<?= $c['quantity'] ?>)</span>
              <strong>Rs <?= number_format($c['subtotal'],2) ?></strong>
            </div>
          <?php endforeach; ?>
          <hr>
          <div class="d-flex justify-content-between">
            <span class="fw-bold">Total:</span>
            <span class="summary-total">Rs <?= number_format($grand,2) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer>© 2025 Glowify. All Rights Reserved.</footer>

<script>
// Toggle payment sections
const paymentSelect=document.getElementById('payment');
const cardFields=document.getElementById('cardFields');
const bankFields=document.getElementById('bankFields');

paymentSelect.addEventListener('change',()=>{
  cardFields.classList.add('hidden');
  bankFields.classList.add('hidden');
  if(paymentSelect.value==="Card") cardFields.classList.remove('hidden');
  if(paymentSelect.value==="Bank") bankFields.classList.remove('hidden');
});
</script>
</body>
</html>
