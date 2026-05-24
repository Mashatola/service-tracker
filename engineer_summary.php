<?php
include 'auth.php';
include 'config.php';

if(!isset($_GET['id'])){
    die("Engineer not found");
}

$engineer_id = $_GET['id'];

/* GET ENGINEER */
$eng_query = mysqli_query($conn,
"SELECT * FROM users WHERE id='$engineer_id' AND role='Engineer'");

$engineer = mysqli_fetch_assoc($eng_query);

if(!$engineer){
    die("Engineer not found");
}

$engineer_name = $engineer['full_name'];

/* FILTERS */
$dept_id = $_GET['department_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where = [];
$where[] = "service_usage.technician_name = '$engineer_name'";

if($dept_id != ""){
    $where[] = "service_usage.department_id = '$dept_id'";
}

if($start_date != "" && $end_date != ""){
    $where[] = "service_usage.service_date BETWEEN '$start_date' AND '$end_date'";
}

$where_sql = "WHERE " . implode(" AND ", $where);
?>

<!DOCTYPE html>
<html>

<head>

<title>Engineer Summary</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* PAGE LAYOUT FIX */
html, body {
    height: 100%;
}

body{
    background:#f4f8ff;
    font-family: Arial;
    display:flex;
    flex-direction:column;
}

/* NAVBAR */
.navbar{
    background: linear-gradient(90deg,#0d6efd,#0b5ed7);
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

/* CONTENT WRAPPER */
.page-content{
    flex:1;
}

/* CARD */
.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* TABLE HEADER */
.table thead{
    background:#0d6efd;
    color:white;
}

/* HEADER BOX */
.header-box{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
}

/* FOOTER (FIXED STYLE) */
footer{
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:15px;
    font-weight:500;
    margin-top:auto;
}

</style>

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <a class="navbar-brand" href="dashboard.php">
            Service Tracking System
        </a>

        <div>
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

<!-- CONTENT -->
<div class="page-content">

<div class="container mt-4">

<!-- HEADER -->
<div class="header-box">
    <h3><?php echo $engineer_name; ?> </h3>
    <p class="text-muted">Engineer Service Activity Summary</p>
</div>

<!-- FILTER -->
<form method="GET" class="row g-2 mb-4">

<input type="hidden" name="id" value="<?php echo $engineer_id; ?>">

<div class="col-md-4">
    <label>Department</label>
    <select name="department_id" class="form-control">

        <option value="">All Departments</option>

        <?php
        $depts = mysqli_query($conn, "SELECT * FROM department");
        while($d = mysqli_fetch_assoc($depts)){
        ?>
        <option value="<?php echo $d['id']; ?>"
            <?php if($dept_id == $d['id']) echo "selected"; ?>>
            <?php echo $d['department_name']; ?>
        </option>
        <?php } ?>

    </select>
</div>

<div class="col-md-3">
    <label>Start Date</label>
    <input type="date" name="start_date"
           class="form-control"
           value="<?php echo $start_date; ?>">
</div>

<div class="col-md-3">
    <label>End Date</label>
    <input type="date" name="end_date"
           class="form-control"
           value="<?php echo $end_date; ?>">
</div>

<div class="col-md-2 d-flex align-items-end">
    <button class="btn btn-primary w-100">Filter</button>
</div>

</form>

<!-- TABLE -->
<div class="card p-4">

<h5>Service Records</h5>

<table class="table table-bordered mt-3">

<thead>
<tr>
    <th>Department</th>
    <th>Date</th>
    <th>Days Used</th>
    <th>Comments</th>
</tr>
</thead>

<tbody>

<?php
$query = mysqli_query($conn,
"SELECT service_usage.*, department.department_name
 FROM service_usage
 INNER JOIN department
 ON service_usage.department_id = department.id
 $where_sql
 ORDER BY service_usage.service_date DESC");

while($row = mysqli_fetch_assoc($query)){
?>

<tr>
    <td><?php echo $row['department_name']; ?></td>
    <td><?php echo $row['service_date']; ?></td>
    <td><?php echo $row['days_used']; ?></td>
    <td><?php echo $row['comments']; ?></td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- FOOTER (FIXED AT BOTTOM, BLUE) -->
<footer>
    © <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</footer>

</body>
</html>