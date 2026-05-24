<?php
include 'config.php';

$password = password_hash("admin123", PASSWORD_DEFAULT);

mysqli_query($conn,
"INSERT INTO users (full_name, username, email, password, role, password_changed_at)
VALUES ('System Admin', 'admin', 'admin@local.com', '$password', 'Admin', NOW())");

echo "Admin created successfully";
?>