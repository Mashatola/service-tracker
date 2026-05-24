<?php
session_start();

// clear all session data
$_SESSION = [];

// destroy session
session_destroy();

// prevent cache issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// redirect safely
header("Location: index.php");
exit;
?>