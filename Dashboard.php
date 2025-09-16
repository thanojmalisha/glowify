<?php

require_once("includes/database_conn.php");

function send_json($arr){
    header("Content-Type: application/json");
    echo json_encode($arr); exit;
}

if(isset($_GET['action'])){
    $action = $_GET['action'];

    if($action==="read"){
        $res = $conn->query("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity,
                             p.category_id, p.image_url, c.name AS category
                             FROM Product p LEFT JOIN Category c ON p.category_id=c.category_id
                             ORDER BY p.product_id DESC");
        $rows = [];
        while($r = $res->fetch_assoc()) $rows[] = $r;
        send_json(["success"=>true,"data"=>$rows]);
    }
    if($action==="create" || $action==="update"){
        $id = $_POST['product_id'] ?? null;
        $name = $_POST['name'] ?? "";
        $desc = $_POST['description'] ?? "";
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock_quantity'] ?? 0);
        $cat = $_POST['category_id'] ?? null;
        $img = $_POST['image_url'] ?? "";

        if($action==="create"){
            $stmt = $conn->prepare("INSERT INTO Product (name, description, price, stock_quantity, category_id, image_url) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssdiis",$name,$desc,$price,$stock,$cat,$img);
            $stmt->execute();
            send_json(["success"=>true,"id"=>$conn->insert_id]);
        } else {
            $stmt = $conn->prepare("UPDATE Product SET name=?, description=?, price=?, stock_quantity=?, category_id=?, image_url=? WHERE product_id=?");
            $stmt->bind_param("ssdiisi",$name,$desc,$price,$stock,$cat,$img,$id);
            $stmt->execute();
            send_json(["success"=>true]);
        }
    }

    if($action==="delete"){
        $id = $_POST['product_id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM Product WHERE product_id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        send_json(["success"=>true]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Glowify Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/dashboard.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="dark">

<!-- Sidebar -->
<div class="sidebar">
  <h4>Glowify Admin</h4>
  <a href="dashboard.php">Dashboard</a>
  <a href="index.php">Home</a>
  <a href="orders.php">Orders</a>   <!-- linked to orders.php -->
  <a href="users.php">Users</a>     <!-- linked to users.php -->
  <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <div class="topbar">
    <h3>Dashboard</h3>
    <div>
      <button id="btnTheme" class="btn btn-secondary btn-sm btn-theme">Toggle Theme</button>
      <button id="btnAdd" class="btn btn-primary btn-sm">+ Add Product</button>
    </div>
  </div>

 <div class="container-fluid mt-3">
    <!-- Metric Cards -->
    <div class="row g-3 mb-3">
      <div class="col-md-4"><div class="card card-metric"><h6>Total Products</h6><h3 id="totalProducts">0</h3></div></div>
      <div class="col-md-4"><div class="card card-metric"><h6>Total Stock</h6><h3 id="totalStock">0</h3></div></div>
      <div class="col-md-4"><div class="card card-metric"><h6>Total Revenue</h6><h3 id="totalRevenue">Rs 0</h3></div></div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-3">
      <div class="col-md-8"><div class="card p-3 chart-container"><canvas id="stockChart"></canvas></div></div>
      <div class="col-md-4"><div class="card p-3 chart-container"><canvas id="categoryChart"></canvas></div></div>
    </div> 