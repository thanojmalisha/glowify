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
</head>
<body class="dark">

<!-- Sidebar -->
<div class="sidebar">
  <h4>Glowify Admin</h4>
  <a href="dashboard.php">Dashboard</a>
  <a href="index.php">Home</a>
  <a href="orders.php">Orders</a>
  <a href="users.php">Users</a>
  <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <div class="topbar">
    <h3>Orders Management</h3>
    <div>
      <button id="btnTheme" class="btn btn-secondary btn-sm btn-theme">Toggle Theme</button>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOrderModal">+ Add Order</button>
    </div>
  </div>

  <!-- Metrics -->
  <div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card card-metric card-metric-total text-center">
      <h6>Total Orders</h6>
      <h3><?php $r=$conn->query("SELECT COUNT(*) AS cnt FROM `Order`"); echo $r->fetch_assoc()['cnt'];?></h3>
    </div></div>
    <div class="col-md-3"><div class="card card-metric card-metric-pending text-center">
      <h6>Pending</h6>
      <h3><?php $r=$conn->query("SELECT COUNT(*) AS cnt FROM `Order` WHERE status='Pending'"); echo $r->fetch_assoc()['cnt'];?></h3>
    </div></div>
    <div class="col-md-3"><div class="card card-metric card-metric-delivered text-center">
      <h6>Delivered</h6>
      <h3><?php $r=$conn->query("SELECT COUNT(*) AS cnt FROM `Order` WHERE status='Delivered'"); echo $r->fetch_assoc()['cnt'];?></h3>
    </div></div>
    <div class="col-md-3"><div class="card card-metric card-metric-revenue text-center">
      <h6>Total Revenue</h6>
      <h3>Rs <?php $r=$conn->query("SELECT SUM(total_amount) AS sum FROM `Order`"); echo number_format($r->fetch_assoc()['sum'],2);?></h3>
    </div></div>
  </div>

  <!-- Orders Table -->
  <div class="card-table">
    <table class="table table-hover table-borderless text-light" id="ordersTable">
      <thead>
        <tr>
          <th>ID</th><th>Customer</th><th>Email</th><th>Phone</th><th>Date</th>
          <th>Products</th><th>Total</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql="SELECT o.order_id,o.customer_id,o.order_date,o.total_amount,o.status,c.full_name,c.email,c.phone
            FROM `Order` o LEFT JOIN Customer c ON o.customer_id=c.customer_id ORDER BY o.order_date DESC";
      $res=$conn->query($sql);
      while($order=$res->fetch_assoc()){
          $prod_res = $conn->query("SELECT p.name,oi.quantity,oi.price FROM order_item oi JOIN Product p ON oi.product_id=p.product_id WHERE oi.order_id=".$order['order_id']);
          $prod_list = "";
          while($p=$prod_res->fetch_assoc()){
              $prod_list .= $p['name']." x".$p['quantity']." (Rs ".$p['price'].")<br>";
          }
          echo "<tr>
                  <td>{$order['order_id']}</td>
                  <td>{$order['full_name']}</td>
                  <td>{$order['email']}</td>
                  <td>{$order['phone']}</td>
                  <td>".date('d M Y, H:i',strtotime($order['order_date']))."</td>
                  <td>{$prod_list}</td>
                  <td>Rs ".number_format($order['total_amount'],2)."</td>
                  <td><span class='badge-status badge-{$order['status']}'>{$order['status']}</span></td>
                  <td>
                    <button class='btn btn-sm btn-warning edit-btn' data-id='{$order['order_id']}' data-bs-toggle='modal' data-bs-target='#editOrderModal'>Edit</button>
                    <a href='?delete_order={$order['order_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete this order?')\">Delete</a>
                  </td>
                </tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label>Customer</label>
          <select name="customer_id" class="form-select" id="customerSelect" required>
            <option value="">Select Customer</option>
            <?php $cus=$conn->query("SELECT customer_id,full_name FROM Customer");
            while($c=$cus->fetch_assoc()){ echo "<option value='{$c['customer_id']}'>{$c['full_name']}</option>"; } ?>
            <option value="new">-- Add New Customer --</option>
          </select>
        </div>
        <div id="newCustomerFields" style="display:none;">
            <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name">
            <input type="email" name="email" class="form-control mb-2" placeholder="Email">
            <input type="text" name="phone" class="form-control mb-2" placeholder="Phone">
        </div>
        <div id="productContainer">
          <label>Products</label>
          <div class="row mb-2 product-row">
            <div class="col-md-6">
              <select name="product_id[]" class="form-select" required>
                <option value="">Select Product</option>
                <?php $prods=$conn->query("SELECT product_id,name,price FROM Product");
                while($p=$prods->fetch_assoc()){ 
                    echo "<option value='{$p['product_id']}'>{$p['name']} (Rs {$p['price']})</option>"; 
                } ?>
              </select>
            </div>
            <div class="col-md-3"><input type="number" name="quantity[]" class="form-control" value="1" min="1"></div>
            <div class="col-md-3"><button type="button" class="btn btn-danger remove-product">Remove</button></div>
          </div>
        </div>
        <button type="button" class="btn btn-primary mb-2" id="addProductBtn">Add Product</button>
        <div class="mb-3"><label>Status</label>
          <select name="status" class="form-select">
            <option>Pending</option><option>Processing</option><option>Shipped</option><option>Delivered</option><option>Cancelled</option>
          </select>
        </div>
      </div>
      <div class="modal-footer"><button type="submit" name="add_order" class="btn btn-primary">Add Order</button></div>
    </form>
  </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Edit Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="order_id" id="editOrderId">
        <label>Status</label>
        <select name="status" id="editStatus" class="form-select">
          <option>Pending</option><option>Processing</option><option>Shipped</option><option>Delivered</option><option>Cancelled</option>
        </select>
      </div>
      <div class="modal-footer"><button type="submit" name="edit_order" class="btn btn-primary">Update</button></div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#ordersTable').DataTable({ "order":[[4,"desc"]] });

    $('#customerSelect').change(function(){
        if($(this).val()=='new'){ $('#newCustomerFields').show(); } else { $('#newCustomerFields').hide(); }
    });
    $('#addProductBtn').click(function(){
        var row = $('.product-row:first').clone();
        row.find('select').val('');
        row.find('input').val(1);
        $('#productContainer').append(row);
    });
    $(document).on('click','.remove-product',function(){
        if($('.product-row').length>1){ $(this).closest('.product-row').remove(); }
    });
    $('.edit-btn').click(function(){
        $('#editOrderId').val($(this).data('id'));
    });
});
</script>
</body>
</html>
