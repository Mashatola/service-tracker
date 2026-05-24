<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

$notifs = mysqli_query($conn,
"SELECT * FROM notifications WHERE user_id=$user_id AND status='pending'");
?>

<h2>Notifications</h2>

<?php while($n = mysqli_fetch_assoc($notifs)){ ?>

    <p><?php echo $n['message']; ?></p>

    <a href="accept_reset.php?id=<?php echo $n['id']; ?>">Accept</a>
    <a href="reject_reset.php?id=<?php echo $n['id']; ?>">Reject</a>

<?php } ?>