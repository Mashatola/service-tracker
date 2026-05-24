<?php
session_start();
include 'config.php';

/* =========================
   AUTH CHECK
========================= */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$logged_in_user = $_SESSION['name'];
$user_role = $_SESSION['role'];

$message = "";

/* =========================
   SUBMIT VISIT
========================= */
if(isset($_POST['submit_visit'])){

    $department_id = $_POST['department_id'];
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    /* ✅ ALLOW CUSTOM / BACKDATE */
    $service_date = $_POST['service_date'] ?? date("Y-m-d");

    /* =========================
       CHECK EXISTING VISIT
    ========================== */
    $check = mysqli_query($conn,
    "SELECT * FROM service_usage
     WHERE technician_name='$logged_in_user'
     AND department_id='$department_id'
     AND service_date='$service_date'");

    /* =========================
       UPDATE EXISTING
    ========================== */
    if(mysqli_num_rows($check) > 0){

        $row = mysqli_fetch_assoc($check);
        $id = $row['id'];

        mysqli_query($conn,
        "UPDATE service_usage
         SET comments='$comments'
         WHERE id='$id'");

        $message = "Department already visited on $service_date — comments updated.";

    } else {

        /* =========================
           INSERT NEW VISIT
        ========================== */
        mysqli_query($conn,
        "INSERT INTO service_usage
        (department_id, service_date, days_used, comments, technician_name)
        VALUES
        ('$department_id', '$service_date', 1, '$comments', '$logged_in_user')");

        $message = "Visit recorded successfully for $service_date.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Service Usage</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f8ff;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    font-family:Arial;
}

.container{
    flex:1;
}

.navbar{
    background:#0d6efd;
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

.nav-actions{
    display:flex;
    gap:8px;
    margin-left:auto;
    flex-wrap:wrap;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

h2{
    color:#0d6efd;
    font-weight:bold;
}

.table thead{
    background:#0d6efd;
    color:white;
}

.visit-box{
    background:#0d6efd;
    color:white;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
}

footer{
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:auto;
}

.badge-admin{
    background:#198754;
    padding:6px 12px;
    border-radius:20px;
}

.badge-engineer{
    background:#fd7e14;
    padding:6px 12px;
    border-radius:20px;
}

.logo{
    width:180px;
}

.table-responsive{
    overflow:auto;
}

</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg">

<div class="container">

<a class="navbar-brand" href="dashboard.php">
    Service Tracking System
</a>

<div class="nav-actions">

    <a href="index.php" class="btn btn-primary btn-sm">
    Home
</a>

<a href="analytics.php" class="btn btn-primary btn-sm">
    Analytics
</a>

<a href="report.php" class="btn btn-primary btn-sm">
    Reports
</a>

<a href="log_failure.php" class="btn btn-primary btn-sm">
    Log Failure
</a>

<a href="report_failure.php" class="btn btn-primary btn-sm">
    Failure Report
</a>

<a href="logout.php" class="btn btn-primary btn-sm">
    Logout
</a>
<span class="text-white me-2">
👤 <?php echo $_SESSION['name']; ?>
</span>
</div>

</div>

</nav>

<!-- ================= CONTENT ================= -->
<div class="container mt-5">

<div class="row">

<!-- ================= LEFT SIDE ================= -->
<div class="col-md-4">

<div class="card p-4">

<!-- ALERT -->
<?php if($message != ""){ ?>

<div class="alert alert-info">
    <?php echo $message; ?>
</div>

<?php } ?>

<!-- USER INFO -->
<div class="visit-box">

<div class="d-flex justify-content-between align-items-center">

<div>
Logged In As:
<br>
<strong><?php echo $logged_in_user; ?></strong>
</div>

<div>

<?php if($user_role == "Admin"){ ?>

<span class="badge-admin">
Admin
</span>

<?php } else { ?>

<span class="badge-engineer">
Engineer
</span>

<?php } ?>

</div>

</div>

</div>

<!-- LOGO -->


<h4>Daily Support Visit</h4>

<form method="POST">

<!-- DEPARTMENT -->
<div class="mb-3">

<label>Select Department</label>

<select name="department_id" class="form-control" required>

<option value="">Select Department</option>

<?php
$departments = mysqli_query($conn,
"SELECT * FROM department ORDER BY department_name ASC");

while($dept = mysqli_fetch_assoc($departments)){
?>

<option value="<?php echo $dept['id']; ?>">
    <?php echo $dept['department_name']; ?>
</option>

<?php } ?>

</select>

</div>

<!-- DATE -->
<div class="mb-3">

<label>Select Visit Date</label>

<input
type="date"
name="service_date"
class="form-control"
value="<?php echo date('Y-m-d'); ?>"
required>


</div>

<!-- COMMENTS -->
<div class="mb-3">

<label>Comments</label>

<textarea
name="comments"
class="form-control"
rows="5"
placeholder="Enter support comments..."
required></textarea>

</div>

<!-- BUTTON -->
<button
type="submit"
name="submit_visit"
class="btn btn-primary w-100">

Submit Visit

</button>

</form>

</div>

</div>

<!-- ================= RIGHT SIDE ================= -->
<div class="col-md-8">

<div class="card p-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

<h2 class="mb-2">Support History</h2>

<div class="d-flex gap-2 flex-wrap">

<a href="report.php" class="btn btn-primary btn-sm">
View Report
</a>

<a href="log_failure.php" class="btn btn-primary btn-sm">
Log Failure
</a>

<a href="report_failure.php" class="btn btn-primary btn-sm">
Failure Report
</a>

</div>

</div>

<!-- TABLE -->
<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>
<th>Date</th>
<th>Department</th>
<th>Days Used</th>
<th>Engineer</th>
<th>Comments</th>
</tr>

</thead>

<tbody>

<?php

/* =========================
   HISTORY QUERY
========================= */

if($user_role == "Admin"){

    $query = "SELECT su.*, d.department_name
              FROM service_usage su
              JOIN department d ON su.department_id = d.id
              ORDER BY su.service_date DESC, su.id DESC";

} else {

    $query = "SELECT su.*, d.department_name
              FROM service_usage su
              JOIN department d ON su.department_id = d.id
              WHERE su.technician_name='$logged_in_user'
              ORDER BY su.service_date DESC, su.id DESC";
}

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td>
<?php echo $row['service_date']; ?>
</td>

<td>
<?php echo $row['department_name']; ?>
</td>

<td>
<?php echo $row['days_used']; ?>
</td>

<td>
<?php echo $row['technician_name']; ?>
</td>

<td>
<?php echo $row['comments']; ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

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