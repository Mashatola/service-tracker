<?php
session_start();
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = mysqli_query($conn,
    "SELECT * FROM users WHERE username='$username'");

    if($query && mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] == "Admin"){
                header("Location: dashboard.php");
                exit;
            }

            if($user['role'] == "Engineer"){
                header("Location: service_usage.php");
                exit;
            }

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f8ff;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    font-family: Arial;
}

/* HEADER */
.navbar{
    background:#0d6efd;
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

/* CARD */
.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* PASSWORD TOGGLE */
.toggle-pass{
    cursor:pointer;
}

/* FOOTER (NOT FLOATING) */
footer{
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:auto;
}

</style>

</head>

<body>

<!-- ================= HEADER ================= -->
<nav class="navbar navbar-expand-lg">
<div class="container">

<a class="navbar-brand" href="index.php">
Service Tracking System
</a>

<div>
    <a href="index.php" class="btn btn-light btn-sm">Home</a>
</div>

</div>
</nav>

<!-- ================= LOGIN ================= -->
<div class="container">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card p-4 mt-5">

<h3 class="text-center text-primary">Login</h3>

<?php if($error != ""){ ?>
<div class="alert alert-danger">
    <?php echo $error; ?>
</div>
<?php } ?>

<form method="POST">

<label>Username</label>
<input type="text" name="username" class="form-control mb-3" required>

<label>Password</label>

<div class="input-group mb-3">

<input type="password" name="password" id="password" class="form-control" required>

<span class="input-group-text toggle-pass" onclick="togglePassword()">
👁
</span>

</div>

<button type="submit" name="login" class="btn btn-primary w-100">
Login
</button>

</form>

<hr>

<!-- FORGOT PASSWORD -->
<div class="text-center mb-2">
    <a href="reset_password.php">Forgot Password?</a>
</div>

</div>

</div>

</div>

</div>

<!-- ================= FOOTER ================= -->
<footer>
    © <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</footer>

<script>
function togglePassword(){
    let pass = document.getElementById("password");
    pass.type = (pass.type === "password") ? "text" : "password";
}
</script>

</body>
</html>