<?php
require_once("includes/database_conn.php");

// Add New Customer
if(isset($_POST['add_customer'])){
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO Customer (full_name,email,phone) VALUES (?,?,?)");
    $stmt->bind_param("sss",$full_name,$email,$phone);
    $stmt->execute();
    $new_customer_id = $stmt->insert_id;
    $stmt->close();
    echo "<script>location.href='orders.php?customer_added=$new_customer_id'</script>";
    exit();
}

// Add Order
if(isset($_POST['add_order'])){
    $customer_id = $_POST['customer_id'];
    if($customer_id === "new"){ // handle new customer
        $stmt = $conn->prepare("INSERT INTO Customer (full_name,email,phone) VALUES (?,?,?)");
        $stmt->bind_param("sss",$_POST['full_name'],$_POST['email'],$_POST['phone']);
        $stmt->execute();
        $customer_id = $stmt->insert_id;
        $stmt->close();
    }

    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $total_amount = 0;

    foreach($product_ids as $key=>$pid){
        $qty = $quantities[$key];
        $res = $conn->query("SELECT price FROM Product WHERE product_id=$pid");
        $price = $res->fetch_assoc()['price'];
        $total_amount += $price * $qty;
    }

    $stmt = $conn->prepare("INSERT INTO `Order` (customer_id,total_amount,status,order_date) VALUES (?,?,?,NOW())");
    $stmt->bind_param("ids",$customer_id,$total_amount,$_POST['status']);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    foreach($product_ids as $key=>$pid){
        $qty = $quantities[$key];
        $res = $conn->query("SELECT price FROM Product WHERE product_id=$pid");
        $price = $res->fetch_assoc()['price'];
        $stmt = $conn->prepare("INSERT INTO order_item (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
        $stmt->bind_param("iiid",$order_id,$pid,$qty,$price);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: orders.php");
    exit();
}

// Edit Order Status
if(isset($_POST['edit_order'])){
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE `Order` SET status=? WHERE order_id=?");
    $stmt->bind_param("si",$status,$order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit();
}

// Delete Order
if(isset($_GET['delete_order'])){
    $order_id = intval($_GET['delete_order']);
    $conn->query("DELETE FROM order_item WHERE order_id=$order_id");
    $conn->query("DELETE FROM `Order` WHERE order_id=$order_id");
    header("Location: orders.php");
    exit();
}
?>