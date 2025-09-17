<?php
require_once("includes/database_conn.php");

// Handle Delete
if(isset($_GET['delete_id']) && isset($_GET['type'])){
    $id = intval($_GET['delete_id']);
    $type = $_GET['type'];
    if($type=='Customer'){
        $conn->query("DELETE FROM Customer WHERE customer_id=$id");
    }else{
        $conn->query("DELETE FROM Admin WHERE admin_id=$id");
    }
    header("Location: users.php");
    exit;
}

// Handle Add/Edit
if($_SERVER['REQUEST_METHOD']=='POST'){
    $id = $_POST['user_id']??0;
    $type = $_POST['user_type'];
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone']??NULL;
    $role = $_POST['role']??NULL;
    $password = $_POST['password']??NULL;

    if($id){ // Edit
        if($type=='Customer'){
            $pass_sql = $password ? ", password='".password_hash($password,PASSWORD_DEFAULT)."'" : "";
            $conn->query("UPDATE Customer SET full_name='$name', email='$email', phone='$phone' $pass_sql WHERE customer_id=$id");
        }else{
            $pass_sql = $password ? ", password='".password_hash($password,PASSWORD_DEFAULT)."'" : "";
            $conn->query("UPDATE Admin SET full_name='$name', email='$email', role='$role' $pass_sql WHERE admin_id=$id");
        }
    } else { // Add
        if($type=='Customer'){
            $pass = password_hash($password,PASSWORD_DEFAULT);
            $conn->query("INSERT INTO Customer(full_name,email,phone,password) VALUES('$name','$email','$phone','$pass')");
        }else{
            $pass = password_hash($password,PASSWORD_DEFAULT);
            $conn->query("INSERT INTO Admin(full_name,email,password,role) VALUES('$name','$email','$pass','$role')");
        }
    }
    header("Location: users.php");
    exit;
}

// Fetch all users
$customer_sql = "SELECT customer_id AS id, full_name, email, phone, 'Customer' AS type, NULL AS role, created_at FROM Customer";
$admin_sql = "SELECT admin_id AS id, full_name, email, NULL AS phone, 'Admin' AS type, role, created_at FROM Admin";
$users_res = $conn->query("($customer_sql) UNION ALL ($admin_sql) ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users - Glowify Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
body.dark{background:#0f1720;color:#e6eef8;}
.sidebar {width:220px;height:100vh;position:fixed;top:0;left:0;background:#1e1e2f;color:#fff;padding:20px;}
.sidebar h4 {color:#ff69b4;margin-bottom:20px;}
.sidebar a {display:block;padding:10px;margin:5px 0;color:#fff;text-decoration:none;border-radius:6px;transition:0.3s;}
.sidebar a.active, .sidebar a:hover {background:#4f46e5;}
.main {margin-left:240px;padding:20px;}
.card-table {background:#1e1e2f;border-radius:16px;padding:20px;box-shadow:0 10px 25px rgba(0,0,0,0.3);}
.table-hover tbody tr:hover {background:rgba(79,70,229,0.2);}
.table thead {background:#111827;color:#fff;}
.table th, .table td {vertical-align:middle;}
.badge-role {padding:5px 12px;border-radius:12px;font-weight:600;}
.badge-Customer {background:#3b82f6;color:#fff;}
.badge-Admin {background:#fbbf24;color:#000;}
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
  <a href="users.php" class="active">Users</a>
  <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <h3 class="mb-4">Users Management</h3>
  <div class="card-table">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">+ Add User</button>
    <div class="table-responsive">
      <table class="table table-hover table-borderless text-light" id="usersTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if($users_res->num_rows>0): ?>
          <?php while($user=$users_res->fetch_assoc()): ?>
            <tr>
              <td><?= $user['id'] ?></td>
              <td><span class="badge-role badge-<?= $user['type'] ?>"><?= $user['type'] ?></span></td>
              <td><?= htmlspecialchars($user['full_name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= $user['phone']??'-' ?></td>
              <td><?= $user['role']??'-' ?></td>
              <td><?= $user['created_at']??'-' ?></td>
              <td>
                <button class="btn btn-sm btn-info" onclick='viewUser(<?= json_encode($user) ?>)'>View</button>
                <button class="btn btn-sm btn-warning" onclick='editUser(<?= json_encode($user) ?>)'>Edit</button>
                <a href="users.php?delete_id=<?= $user['id'] ?>&type=<?= $user['type'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8">No users found</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content" id="userForm">
      <div class="modal-header">
        <h5 class="modal-title">Add / Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="user_id" id="user_id">
        <select class="form-control mb-2" name="user_type" id="user_type" required>
          <option value="">Select Type</option>
          <option value="Customer">Customer</option>
          <option value="Admin">Admin</option>
        </select>
        <input type="text" class="form-control mb-2" name="full_name" id="full_name" placeholder="Full Name" required>
        <input type="email" class="form-control mb-2" name="email" id="email" placeholder="Email" required>
        <input type="text" class="form-control mb-2" name="phone" id="phone" placeholder="Phone">
        <select class="form-control mb-2" name="role" id="role">
          <option value="">Role (Admin only)</option>
          <option value="super_admin">Super Admin</option>
          <option value="manager">Manager</option>
          <option value="editor">Editor</option>
        </select>
        <input type="password" class="form-control mb-2" name="password" id="password" placeholder="Password">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#usersTable').DataTable({ "order":[[6,"desc"]] });
});

function resetForm(){
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
}

function viewUser(user){
    alert(`User Info:\nType: ${user.type}\nName: ${user.full_name}\nEmail: ${user.email}\nPhone: ${user.phone??'-'}\nRole: ${user.role??'-'}`);
}

function editUser(user){
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
    document.getElementById('user_id').value = user.id;
    document.getElementById('user_type').value = user.type;
    document.getElementById('full_name').value = user.full_name;
    document.getElementById('email').value = user.email;
    document.getElementById('phone').value = user.phone??'';
    document.getElementById('role').value = user.role??'';
}
</script>
</body>
</html>
