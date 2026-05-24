<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

/* =========================
   CHECK LOGIN
========================= */

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$logged_in_user = $_SESSION['name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'User';

/* =========================
   FILTERS
========================= */

$dept_id = $_GET['department_id'] ?? '';
$filter = $_GET['filter'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where = [];

/* ROLE FILTER */
if($user_role != "Admin"){
    $where[] = "service_usage.technician_name = '$logged_in_user'";
}

/* DEPARTMENT */
if($dept_id != ""){
    $where[] = "service_usage.department_id = '$dept_id'";
}

/* QUICK FILTERS */
if($filter == "week"){
    $where[] = "service_usage.service_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
}

if($filter == "month"){
    $where[] = "service_usage.service_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

/* CUSTOM DATE */
if($start_date != "" && $end_date != ""){
    $where[] = "service_usage.service_date BETWEEN '$start_date' AND '$end_date'";
}

$where_sql = "";

if(count($where) > 0){
    $where_sql = "WHERE " . implode(" AND ", $where);
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Service Report</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:white;
    padding:20px;
    font-family:Arial;
}

.signature-box{
    margin-top:60px;
    display:flex;
    justify-content:space-between;
}

.sig{
    width:40%;
    text-align:center;
}

</style>

</head>

<body>

<!-- =========================
     HEADER NAV (OPTIONAL ADD)
========================= -->
<nav class="navbar navbar-dark bg-primary mb-4">

<div class="container">

    <a class="navbar-brand" href="dashboard.php">
        Service Tracking System
    </a>

    <div>

        
 <!-- HOME -->
        <a href="index.php" class="btn btn-primary btn-sm">Home</a>
<a href="analytics.php" class="btn btn-primary btn-sm">Analytics</a>
<a href="report.php" class="btn btn-primary btn-sm">Reports</a>
<a href="log_failure.php" class="btn btn-primary btn-sm">Log Failure</a>
<a href="report_failure.php" class="btn btn-primary btn-sm">Failure Report</a>
<a href="logout.php" class="btn btn-primary btn-sm">Logout</a>
<span class="text-white me-2">
👤 <?php echo $_SESSION['name']; ?>
</span>
    </div>

</div>

</nav>

<!-- FILTER -->
<form method="GET" class="mb-4">

<div class="row">

<div class="col-md-3">
<label>Department</label>

<select name="department_id" class="form-control">
<option value="">All Departments</option>

<?php
$depts = mysqli_query($conn,"SELECT * FROM department");
while($d = mysqli_fetch_assoc($depts)){
?>
<option value="<?php echo $d['id']; ?>"
<?php if($dept_id == $d['id']) echo "selected"; ?>>
<?php echo $d['department_name']; ?>
</option>
<?php } ?>

</select>
</div>

<div class="col-md-2">
<label>Filter</label>
<select name="filter" class="form-control">
<option value="">Select</option>
<option value="week" <?php if($filter=="week") echo "selected"; ?>>Past Week</option>
<option value="month" <?php if($filter=="month") echo "selected"; ?>>Past Month</option>
</select>
</div>

<div class="col-md-2">
<label>Start Date</label>
<input type="date" name="start_date" value="<?php echo $start_date; ?>" class="form-control">
</div>

<div class="col-md-2">
<label>End Date</label>
<input type="date" name="end_date" value="<?php echo $end_date; ?>" class="form-control">
</div>

<div class="col-md-3 d-flex align-items-end">

<button type="submit" class="btn btn-primary me-2">Filter</button>

<button type="button" onclick="downloadPDF()" class="btn btn-success">
Download PDF
</button>

</div>

</div>
</form>

<!-- REPORT -->
<div id="report">

<!-- HEADER -->
<div style="text-align:center; margin-bottom:20px;">

<img src="logo.png" style="width:260px;">

<p><strong>VEEAM CONFIGURATION, MONITORING AND ONSITE SUPPORT</strong></p>

<p>Date Generated: <?php echo date("Y-m-d"); ?></p>

</div>

<!-- TABLE -->
<table class="table table-bordered">

<thead class="table-primary">
<tr>
<th>Department</th>
<th>Engineer</th>
<th>Date</th>
<th>Comments</th>
</tr>
</thead>

<tbody>

<?php

$query = "
SELECT service_usage.*, department.department_name
FROM service_usage
INNER JOIN department
ON service_usage.department_id = department.id
$where_sql
ORDER BY service_usage.service_date DESC
";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)){
?>

<tr>
<td><?php echo $row['department_name']; ?></td>
<td><?php echo $row['technician_name']; ?></td>
<td><?php echo $row['service_date']; ?></td>
<td><?php echo $row['comments']; ?></td>
</tr>

<?php } ?>

</tbody>

</table>

<!-- SIGNATURE -->
<div class="signature-box">

<div class="sig">
<p><strong>AFROCENTRIC</strong></p>
<p>Name: ____________________</p>
<p>Date: ____________________</p>
</div>

<div class="sig">
<p><strong>DEPARTMENT</strong></p>
<p>Name: ____________________</p>
<p>Date: ____________________</p>
</div>

</div>

</div>

<!-- PDF -->
<script>
function downloadPDF(){

    const element = document.getElementById("report");

    html2pdf()
    .set({
        margin: 0.5,
        filename: 'Service_Report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
    })
    .from(element)
    .save();
}
</script>

</body>
</html>