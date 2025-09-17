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