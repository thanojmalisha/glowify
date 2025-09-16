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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Glowify Login/Register</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<div class="mode-toggle" id="modeToggle"><i class="fas fa-sun"></i></div>

<!-- SIGN IN -->
<div class="container" id="signIn">
<h1>Sign In</h1>
<form method="post">
    <div class="form-group">
        <input type="email" name="email" placeholder=" " required>
        <label>Email</label>
        <?php if($login_error_email) echo '<div class="error-msg">'.$login_error_email.'</div>'; ?>
    </div>
    <div class="form-group">
        <input type="password" name="password" placeholder=" " required>
        <label>Password</label>
        <?php if($login_error_password) echo '<div class="error-msg">'.$login_error_password.'</div>'; ?>
    </div>
    <input type="submit" class="btn" value="Sign In" name="signIn">
</form>
<div class="links"><p>Don't have an account?</p><button id="signUpButton">Sign Up</button></div>
</div>

<!-- SIGN UP -->
<div class="container" id="signup">
<h1>Register</h1>
<form method="post">
    <div class="form-group"><input type="text" name="full_name" placeholder=" " required><label>Full Name</label></div>
    <div class="form-group"><input type="email" name="email" placeholder=" " required><label>Email</label></div>
    <div class="form-group"><input type="text" name="phone" placeholder=" " required><label>Phone Number</label></div>
    <div class="form-group"><input type="text" name="address" placeholder=" " required><label>Address</label></div>
    <div class="form-group"><input type="password" name="password" placeholder=" " required><label>Password</label></div>
    <?php if($register_error) echo '<div class="error-msg">'.$register_error.'</div>'; ?>
    <input type="submit" class="btn" value="Sign Up" name="signUp">
</form>
<div class="links"><p>Already have an account?</p><button id="signInButton">Sign In</button></div>
</div>

<script>
const signUpButton = document.getElementById('signUpButton');
const signInButton = document.getElementById('signInButton');
const signUpForm=document.getElementById('signup');
const signInForm=document.getElementById('signIn');

signUpButton.addEventListener('click',()=>{signInForm.style.display='none';signUpForm.style.display='block';});
signInButton.addEventListener('click',()=>{signUpForm.style.display='none';signInForm.style.display='block';});

// Dark / Light mode toggle
const modeToggle=document.getElementById('modeToggle');
modeToggle.addEventListener('click',()=>{
    document.body.classList.toggle('light-mode');
    if(document.body.classList.contains('light-mode')){
        document.body.style.background="#fff url('uploads/background.jpg') center/cover no-repeat";
        document.body.style.color="#111";
        modeToggle.innerHTML='<i class="fas fa-moon"></i>';
    }else{
        document.body.style.background="url('uploads/background.jpg') center/cover no-repeat";
        document.body.style.color="#fff";
        modeToggle.innerHTML='<i class="fas fa-sun"></i>';
    }
});
</script>
</body>
</html>

