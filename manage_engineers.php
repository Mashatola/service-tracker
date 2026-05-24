<?php
include 'auth.php';
include 'config.php';

/* =========================
   AUTH CHECK
========================= */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['role'] !== "Admin"){
    header("Location: service_usage.php");
    exit;
}

/* =========================
   ADD ENGINEER
========================= */
if(isset($_POST['add_engineer'])){

    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn,
    "INSERT INTO users (full_name, username, password, role)
     VALUES ('$full_name', '$username', '$password', 'Engineer')");

    header("Location: manage_engineers.php");
    exit;
}

/* =========================
   DELETE ENGINEER
========================= */
if(isset($_POST['delete_engineer'])){

    $id = $_POST['id'];

    mysqli_query($conn,
    "DELETE FROM users WHERE id='$id' AND role='Engineer'");

    header("Location: manage_engineers.php");
    exit;
}

/* =========================
   LOAD EDIT DATA
========================= */
$editData = null;

if(isset($_GET['edit'])){
    $id = $_GET['edit'];

    $res = mysqli_query($conn,
    "SELECT * FROM users WHERE id='$id' AND role='Engineer'");

    $editData = mysqli_fetch_assoc($res);
}

/* =========================
   UPDATE ENGINEER
========================= */
if(isset($_POST['update_engineer'])){

    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];

    mysqli_query($conn,
    "UPDATE users SET full_name='$full_name', username='$username'
     WHERE id='$id' AND role='Engineer'");

    header("Location: manage_engineers.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Engineers</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* ================= BODY FIX (STICKY FOOTER) ================= */
body{
    background:#f4f8ff;
    font-family: Arial;

    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* HEADER */
.navbar{
    background: linear-gradient(90deg,#0d6efd,#0b5ed7);
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

/* CARDS */
.card{
    border:none;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

/* TABLE */
.table thead{
    background:#0d6efd;
    color:white;
}

/* TITLE */
h2{
    color:#0d6efd;
    font-weight:bold;
}

/* FOOTER (FIXED + COLORED) */
footer{
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:15px;

    margin-top:auto;
}

/* LINKS */
.engineer-link{
    color:#0d6efd;
    font-weight:600;
    text-decoration:none;
}

.engineer-link:hover{
    text-decoration:underline;
}

</style>

</head>

<body>

<!-- ================= HEADER ================= -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="dashboard.php">
            Service Tracking System
        </a>

        <div>
            <a href="index.php" class="btn btn-primary btn-sm">Home</a>
            <a href="analytics.php" class="btn btn-primary btn-sm">Analytics</a>
            <a href="report.php" class="btn btn-primary btn-sm">Reports</a>
            <a href="log_failure.php" class="btn btn-primary btn-sm">Log Failure</a>
            <a href="report_failure.php" class="btn btn-primary btn-sm">Failure Report</a>
            <a href="logout.php" class="btn btn-primary btn-sm">Logout</a>

            <span class="text-white me-3">
                👤 <?php echo $_SESSION['name']; ?>
            </span>
        </div>

    </div>
</nav>

<!-- ================= MAIN CONTENT WRAPPER (IMPORTANT FOR FOOTER) ================= -->
<div class="container mt-4" style="flex:1;">

<div class="row">

<!-- ================= FORM ================= -->
<div class="col-md-4">

<div class="card p-4">

<h2>
<?php echo $editData ? "Edit Engineer" : "Add Engineer"; ?>
</h2>

<form method="POST">

<?php if($editData){ ?>
<input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
<?php } ?>

<label>Full Name</label>
<input type="text" name="full_name"
class="form-control"
value="<?php echo $editData['full_name'] ?? ''; ?>"
required>

<br>

<label>Username</label>
<input type="text" name="username"
class="form-control"
value="<?php echo $editData['username'] ?? ''; ?>"
required>

<br>

<label>Email</label>
<input type="text" name="email"
class="form-control"
value="<?php echo $editData['email'] ?? ''; ?>"
required>

<br>

<?php if(!$editData){ ?>

<label>Password</label>
<input type="password" name="password" class="form-control" required>

<br>

<button type="submit" name="add_engineer" class="btn btn-primary w-100">
Add Engineer
</button>

<?php } else { ?>

<button type="submit" name="update_engineer" class="btn btn-success w-100">
Update Engineer
</button>

<?php } ?>

</form>

</div>

</div>

<!-- ================= TABLE ================= -->
<div class="col-md-8">

<div class="card p-4">

<h2>Engineers List</h2>

<table class="table table-bordered table-hover">

<thead>
<tr>
<th>Name</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
$result = mysqli_query($conn,
"SELECT * FROM users WHERE role='Engineer'");

while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td>
<a class="engineer-link"
   href="engineer_summary.php?id=<?php echo $row['id']; ?>">
   <?php echo $row['full_name']; ?>
</a>
</td>

<td><?php echo $row['username']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['role']; ?></td>

<td>

<a href="manage_engineers.php?edit=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">
Edit
</a>

<form method="POST" style="display:inline;">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<button type="submit"
name="delete_engineer"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this engineer?')">
Delete
</button>

</form>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<!-- ================= FOOTER ================= -->
<footer>
    © <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</footer>

</body>
</html>