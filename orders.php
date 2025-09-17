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