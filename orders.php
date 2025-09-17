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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Glowify Orders</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/dashboard.css"/>
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
/* Metrics card colors */
.card-metric-total {background: linear-gradient(135deg,#3b82f6,#60a5fa); color:#fff;}
.card-metric-pending {background: linear-gradient(135deg,#facc15,#fcd34d); color:#000;}
.card-metric-delivered {background: linear-gradient(135deg,#10b981,#34d399); color:#fff;}
.card-metric-revenue {background: linear-gradient(135deg,#6366f1,#4f46e5); color:#fff;}

/* Table UI */
.card-table {background:#1e1e2f;border-radius:16px;padding:20px;box-shadow:0 10px 25px rgba(0,0,0,0.3);}
.table-hover tbody tr:hover {background:rgba(79,70,229,0.2);}
.table thead {background:#111827;color:#fff;}
.table th, .table td {vertical-align:middle;}
.badge-status {padding:5px 12px;border-radius:12px;font-weight:600;}
.badge-Pending {background:#facc15;color:#000;}
.badge-Processing {background:#3b82f6;color:#fff;}
.badge-Shipped {background:#10b981;color:#fff;}
.badge-Delivered {background:#22c55e;color:#000;}
.badge-Cancelled {background:#ef4444;color:#fff;}
.btn-glass {background:rgba(255,255,255,0.05);color:#fff;border-radius:10px;transition:0.3s;}
.btn-glass:hover {background:linear-gradient(90deg,#3b82f6,#6366f1);color:#fff;}
</style>