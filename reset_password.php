<?php
session_start();
include 'config.php';
include 'mail_config.php';

$message = "";
$step = $_SESSION['step'] ?? 1;

/* =========================
   STEP 1: SEND OTP
========================= */
if(isset($_POST['send_otp'])){

    $username = trim($_POST['username']);

    $check = mysqli_query($conn,
    "SELECT * FROM users WHERE username='$username'");

    if(mysqli_num_rows($check) > 0){

        $user = mysqli_fetch_assoc($check);

        $email = $user['email'];

        // ✅ FIX: ensure email exists
        if(empty($email)){
            $message = "No email is linked to this account. Contact admin to update your email.";
            $step = 1;

        } else {

            $otp = rand(100000, 999999);

            $_SESSION['reset_user'] = $username;
            $_SESSION['otp'] = $otp;
            $_SESSION['step'] = 2;

            $step = 2;

            mysqli_query($conn,
            "INSERT INTO otp_verifications (username, otp)
            VALUES ('$username', '$otp')");

            $sent = sendOTPEmail($email, $otp);

            if($sent){
                $message = "OTP has been sent to your email.";
            } else {
                $message = "Failed to send OTP. Please try again.";
                $_SESSION['step'] = 1;
                $step = 1;
            }
        }

    } else {
        $message = "User not found!";
    }
}

/* =========================
   STEP 2: VERIFY OTP
========================= */
if(isset($_POST['verify_otp'])){

    $entered = trim($_POST['otp']);

    if(isset($_SESSION['otp']) && $entered == $_SESSION['otp']){

        $_SESSION['step'] = 3;
        $step = 3;

        $message = "OTP verified successfully.";

    } else {
        $message = "Invalid OTP!";
        $step = 2;
    }
}

/* =========================
   STEP 3: RESET PASSWORD
========================= */
if(isset($_POST['reset_password'])){

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $username = $_SESSION['reset_user'];

    mysqli_query($conn,
    "UPDATE users 
     SET password='$password'
     WHERE username='$username'");

    session_destroy();

    header("Location: login.php?reset=success");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Forgot Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

html, body{
    height:100%;
}

body{
    background:#f4f8ff;
    display:flex;
    flex-direction:column;
}

.navbar{
    background:#0d6efd;
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

.container{
    flex:1;
}

.card{
    margin-top:60px;
    border:none;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.info-box{
    background:#e7f1ff;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
}

.step{
    text-align:center;
    font-weight:bold;
    color:#0d6efd;
    margin-bottom:10px;
}

.footer{
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:auto;
}

</style>

</head>

<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            Service Tracking System
        </a>

        <div>
            <a href="index.php" class="btn btn-light btn-sm me-2">Home</a>
            <a href="login.php" class="btn btn-info btn-sm">Login</a>
        </div>

    </div>
</nav>

<!-- CONTENT -->
<div class="container">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card p-4">

<h3 class="text-center text-primary">Forgot Password</h3>

<?php if($message != ""){ ?>
<div class="alert alert-info">
    <?php echo $message; ?>
</div>
<?php } ?>

<div class="step">
    Step <?php echo $step; ?> / 3
</div>

<?php if(isset($_SESSION['reset_user'])){ ?>
<div class="info-box">
    <strong>User:</strong> <?php echo $_SESSION['reset_user']; ?>
</div>
<?php } ?>

<!-- STEP 1 -->
<?php if($step == 1){ ?>
<form method="POST">
    <label>Username</label>
    <input type="text" name="username" class="form-control" required>
    <br>
    <button name="send_otp" class="btn btn-primary w-100">Send OTP</button>
</form>
<?php } ?>

<!-- STEP 2 -->
<?php if($step == 2){ ?>
<form method="POST">
    <label>Enter OTP</label>
    <input type="text" name="otp" class="form-control" required>
    <br>
    <button name="verify_otp" class="btn btn-success w-100">Verify OTP</button>
</form>
<?php } ?>

<!-- STEP 3 -->
<?php if($step == 3){ ?>
<form method="POST">
    <label>New Password</label>
    <input type="password" name="password" class="form-control" required>
    <br>
    <button name="reset_password" class="btn btn-primary w-100">Reset Password</button>
</form>
<?php } ?>

</div>

</div>

</div>

</div>

<!-- FOOTER -->
<div class="footer">
    © <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</div>

</body>
</html>