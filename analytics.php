<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* =========================
   DATA ARRAYS
========================= */
$labels = [];
$used = [];
$remaining = [];
$failures_arr = [];

$total_system_days = 0;
$total_used_all = 0;

/* =========================
   GET DEPARTMENTS
========================= */
$deptQuery = mysqli_query($conn, "SELECT * FROM department");

while($dept = mysqli_fetch_assoc($deptQuery)){

    $id = $dept['id'];
    $name = $dept['department_name'];
    $total_days = $dept['otp_days'] + $dept['additional_days'];

    $total_system_days += $total_days;

    /* USED DAYS */
    $usedQuery = mysqli_query($conn,
    "SELECT SUM(days_used) AS total_used
     FROM service_usage
     WHERE department_id='$id'");

    $usedRow = mysqli_fetch_assoc($usedQuery);
    $usedDays = $usedRow['total_used'] ?? 0;

    $total_used_all += $usedDays;

    /* FAILURES */
    $failQuery = mysqli_query($conn,
    "SELECT COUNT(*) AS total_failures
     FROM backup_failures
     WHERE department_id='$id'");

    $failRow = mysqli_fetch_assoc($failQuery);
    $failCount = $failRow['total_failures'] ?? 0;

    /* REMAINING */
    $remainingDays = $total_days - $usedDays;

    $labels[] = $name;
    $used[] = (int)$usedDays;
    $remaining[] = (int)$remainingDays;
    $failures_arr[] = (int)$failCount;
}

/* =========================
   INSIGHTS
========================= */

/* Most used */
$topUsed = mysqli_query($conn,
"SELECT d.department_name, SUM(su.days_used) as total_used
 FROM service_usage su
 JOIN department d ON su.department_id = d.id
 GROUP BY su.department_id
 ORDER BY total_used DESC
 LIMIT 1");
$topUsedRow = mysqli_fetch_assoc($topUsed);

/* At risk */
$atRisk = mysqli_query($conn,
"SELECT d.department_name,
 (d.otp_days + d.additional_days - IFNULL(SUM(su.days_used),0)) AS remaining_days
 FROM department d
 LEFT JOIN service_usage su ON d.id = su.department_id
 GROUP BY d.id
 ORDER BY remaining_days ASC
 LIMIT 1");
$atRiskRow = mysqli_fetch_assoc($atRisk);

/* Highest failures */
$topFailure = mysqli_query($conn,
"SELECT d.department_name, COUNT(*) AS failures
 FROM backup_failures bf
 JOIN department d ON bf.department_id = d.id
 GROUP BY bf.department_id
 ORDER BY failures DESC
 LIMIT 1");
$topFailureRow = mysqli_fetch_assoc($topFailure);

?>

<!DOCTYPE html>
<html>
<head>

<title>Department Analytics</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f8ff;
    font-family: Arial;
}

/* NAVBAR */
.navbar{
    background:#0d6efd;
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

/* CARDS */
.card{
    border:none;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* SMALL CHART */
.chart-box{
    width:280px;
    margin:auto;
}

/* INSIGHT CARDS */
.insight-card{
    background:white;
    padding:15px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

.insight-title{
    font-size:13px;
    color:#666;
}

.insight-value{
    font-size:16px;
    font-weight:bold;
    color:#0d6efd;
}

/* TABLE */
.table thead{
    background:#0d6efd;
    color:white;
}

/* FOOTER */
footer{
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:40px;
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

        <div class="d-flex gap-2">

            <a href="index.php" class="btn btn-primary btn-sm">Home</a>
<a href="analytics.php" class="btn btn-primary btn-sm">Analytics</a>
<a href="report.php" class="btn btn-primary btn-sm">Reports</a>
<a href="log_failure.php" class="btn btn-primary btn-sm">Log Failure</a>
<a href="report_failure.php" class="btn btn-primary btn-sm">Failure Report</a>

            <a href="dashboard.php" class="btn btn-primary btn-sm">Dashboard</a>
            <a href="logout.php" class="btn btn-primary btn-sm">Logout</a>

        </div>

    </div>
</nav>

<!-- ================= CONTENT ================= -->
<div class="container mt-4">

<!-- ================= INSIGHTS ================= -->
<div class="row mb-4">

<div class="col-md-3">
<div class="insight-card">
<div class="insight-title">Most Used</div>
<div class="insight-value">
<?php echo $topUsedRow['department_name'] ?? 'N/A'; ?>
</div>
</div>
</div>

<div class="col-md-3">
<div class="insight-card">
<div class="insight-title">Low in Days</div>
<div class="insight-value">
<?php echo $atRiskRow['department_name'] ?? 'N/A'; ?>
</div>
</div>
</div>

<div class="col-md-3">
<div class="insight-card">
<div class="insight-title">Highest Failures</div>
<div class="insight-value">
<?php echo $topFailureRow['department_name'] ?? 'N/A'; ?>
</div>
</div>
</div>

<div class="col-md-3">
<div class="insight-card">
<div class="insight-title">Service Usage</div>
<div class="insight-value">
<?php echo $total_used_all; ?> / <?php echo $total_system_days; ?>
</div>
</div>
</div>

</div>

<!-- ================= PIE CHART ================= -->
<div class="card p-4 mb-4 text-center">

<h4 class="text-primary">Department Usage Overview</h4>

<div class="chart-box">
<canvas id="deptChart"></canvas>
</div>

</div>

<!-- ================= TABLE ================= -->
<div class="card p-4">

<h4 class="text-primary">Department Breakdown</h4>

<table class="table table-bordered mt-3">

<thead>
<tr>
    <th>Department</th>
    <th>Used Days</th>
    <th>Remaining Days</th>
    <th>Total Failures</th>
</tr>
</thead>

<tbody>

<?php for($i=0; $i<count($labels); $i++){ ?>
<tr>
    <td><?php echo $labels[$i]; ?></td>
    <td><?php echo $used[$i]; ?></td>
    <td><?php echo $remaining[$i]; ?></td>
    <td><?php echo $failures_arr[$i]; ?></td>
</tr>
<?php } ?>

</tbody>

</table>

</div>

</div>

<!-- ================= FOOTER ================= -->
<footer>
    © <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</footer>

<!-- ================= CHART ================= -->
<script>
new Chart(document.getElementById('deptChart'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($used); ?>,
            backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#20c997']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

</body>
</html>