<?php
session_start();
include 'config.php';

$user_id = $_SESSION['temp_user'];

if(isset($_POST['change'])){

    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn,
    "UPDATE users 
     SET password='$new_password',
         password_changed_at=NOW()
     WHERE id=$user_id");

    unset($_SESSION['temp_user']);

    echo "Password updated. Please login again.";

    header("refresh:2; url=login.php");
}
?>

<h2>Password Expired (30 Days Rule)</h2>

<form method="POST">

    <input type="password"
           name="password"
           placeholder="New Password"
           required>

    <button name="change">Update Password</button>

</form>