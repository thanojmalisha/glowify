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