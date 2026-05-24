<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$user_query = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$user = mysqli_fetch_assoc($user_query);

$last_change = $user['password_changed_at'];

if($last_change){

    $days = (time() - strtotime($last_change)) / 86400;

    if($days > 30){
        header("Location: change_password.php?force=1");
        exit;
    }
}
?>