<?php
session_start();

require_once("includes/database_conn.php");

$login_error_email = "";
$login_error_password = "";
$register_error = "";

// =====================
// REGISTER
// =====================
if(isset($_POST['signUp'])){
    $full_name=trim($_POST['full_name']);
    $email=trim($_POST['email']);
    $phone=trim($_POST['phone']);
    $address=trim($_POST['address']);
    $password=trim($_POST['password']);

    // Check if email exists
    $stmt=$conn->prepare("SELECT customer_id FROM Customer WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res=$stmt->get_result();

    if($res->num_rows>0){
        $register_error="Email already registered!";
    } else {
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $stmt=$conn->prepare("INSERT INTO Customer (full_name,email,phone,address,password) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss",$full_name,$email,$phone,$address,$hash);
        if($stmt->execute()){
            $_SESSION['user_id']=$stmt->insert_id;
            $_SESSION['username']=$full_name;
            $_SESSION['role']='customer';
            header("Location:index.php");
            exit;
        } else $register_error="Registration failed!";
    }
}

// =====================
// LOGIN
// =====================
if(isset($_POST['signIn'])){
    $email=trim($_POST['email']);
    $password=trim($_POST['password']);

    $user_found=false;

    // Check admin first
    $stmt=$conn->prepare("SELECT * FROM Admin WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res=$stmt->get_result();

    if($res->num_rows===1){
        $user=$res->fetch_assoc();
        $user_found=true;
        if(password_verify($password,$user['password'])){
            $_SESSION['user_id']=$user['admin_id'];
            $_SESSION['username']=$user['full_name'];
            $_SESSION['role']=$user['role'];
            header("Location:Dashboard.php"); // for admin
            exit;
        } else {
            $login_error_password="Invalid password!";
        }
    } else {
        // Check customer
        $stmt=$conn->prepare("SELECT * FROM Customer WHERE email=? LIMIT 1");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res=$stmt->get_result();
        if($res->num_rows===1){
            $user=$res->fetch_assoc();
            $user_found=true;
            if(password_verify($password,$user['password'])){
                $_SESSION['user_id']=$user['customer_id'];
                $_SESSION['username']=$user['full_name'];
                $_SESSION['role']='customer';
                header("Location:index.php");
                exit;
            } else {
                $login_error_password="Invalid password!";
            }
        }
    }

    if(!$user_found){
        $login_error_email="No account found with this email!";
    }
}