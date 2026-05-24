<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* =========================
   FILTERS
========================= */
$dept = $_GET['department_id'] ?? '';
$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';

$where = [];

if($dept){
    $where[] = "bf.department_id='$dept'";
}

if($start && $end){
    $where[] = "DATE(bf.reported_date) BETWEEN '$start' AND '$end'";
}

$whereSQL = count($where)
? "WHERE ".implode(" AND ", $where)
: "";

/* =========================
   GET DEPARTMENT NAME
========================= */
$selectedDepartment = "All Departments";

if($dept){
    $deptQuery = mysqli_query($conn,
    "SELECT department_name FROM department WHERE id='$dept'");

    if(mysqli_num_rows($deptQuery) > 0){
        $deptRow = mysqli_fetch_assoc($deptQuery);
        $selectedDepartment = $deptRow['department_name'];
    }
}

/* =========================
   SLA RULE (FIXED)
========================= */
function isSlaBreached($reported_date, $status){

    // 🚫 NEVER breach SLA if still under investigation
    if($status == "Under Investigation"){
        return false;
    }

    $now = time();
    $reported = strtotime($reported_date);
    $diffHours = ($now - $reported) / 3600;

    return ($status != "Resolved" && $diffHours > 2);
}

/* =========================
   DATA QUERY
========================= */
$query = "
SELECT bf.*, d.department_name
FROM backup_failures bf
JOIN department d ON bf.department_id = d.id
$whereSQL
ORDER BY bf.id DESC
";

$data = mysqli_query($conn, $query);

/* =========================
   ANALYTICS
========================= */
$timeline = [];

$totalLogged = 0;
$totalResolved = 0;
$totalOpen = 0;
$totalBreached = 0;

$rows = [];

while($row = mysqli_fetch_assoc($data)){
    $rows[] = $row;

    $totalLogged++;

    if($row['status'] == "Resolved"){
        $totalResolved++;
    } else {
        $totalOpen++;
    }

    if(isSlaBreached($row['reported_date'],$row['status'])){
        $totalBreached++;
    }

    $date = date('Y-m-d', strtotime($row['reported_date']));
    $timeline[$date] = ($timeline[$date] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Failure Report</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f8ff;
    font-family:Arial;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

.container{ flex:1; }

.navbar{ background:#0d6efd; }
.navbar-brand{ color:white !important; font-weight:bold; }

.logo{
    width:220px;
    height:auto;
}

.stat-card{
    padding:6px;
    border-radius:8px;
    color:white;
    text-align:center;
}

.stat-card h6{
    font-size:11px;
    margin-bottom:2px;
}

.stat-card h5{
    font-size:14px;
    margin:0;
    font-weight:bold;
}

.chart-box{
    height:220px;
}

canvas{
    width:100% !important;
    height:100% !important;
}

.table thead{
    background:#0d6efd;
    color:white;
}

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

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
<div class="container">

<a class="navbar-brand">Service Tracking System</a>

<div class="d-flex gap-2 flex-wrap">



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

<div class="container mt-4">

<!-- FILTERS -->
<form method="GET" class="row mb-4">

<div class="col-md-4">
<select name="department_id" class="form-control">
<option value="">All Departments</option>

<?php
$deps = mysqli_query($conn,"SELECT * FROM department ORDER BY department_name ASC");
while($d = mysqli_fetch_assoc($deps)){
?>
<option value="<?php echo $d['id']; ?>"
<?php if($dept == $d['id']) echo "selected"; ?>>
<?php echo $d['department_name']; ?>
</option>
<?php } ?>
</select>
</div>

<div class="col-md-3">
<input type="date" name="start_date" class="form-control" value="<?php echo $start; ?>">
</div>

<div class="col-md-3">
<input type="date" name="end_date" class="form-control" value="<?php echo $end; ?>">
</div>

<div class="col-md-2">
<button class="btn btn-primary btn-sm w-100">Filter</button>
</div>

</form>

<!-- DOWNLOAD -->
<div class="text-end mb-3">
<button class="btn btn-success btn-sm" onclick="downloadPDF()">
Download PDF
</button>
</div>

<!-- ================= REPORT CONTENT ================= -->
<div id="reportContent">

<div class="text-center mb-3">
    <img src="logo.png" class="logo">

    <h3 class="mt-2">Failure Analytics Report</h3>

    <h6 class="text-muted">
        Department: <strong><?php echo $selectedDepartment; ?></strong>
    </h6>
    <br>
</div>

<!-- STATS -->
<div class="row text-center mb-3">

<div class="col-md-3 mb-2">
<div class="stat-card bg-primary">
<h6>Logged</h6>
<h5><?php echo $totalLogged; ?></h5>
</div>
</div>

<div class="col-md-3 mb-2">
<div class="stat-card bg-success">
<h6>Resolved</h6>
<h5><?php echo $totalResolved; ?></h5>
</div>
</div>

<div class="col-md-3 mb-2">
<div class="stat-card bg-warning">
<h6>Open</h6>
<h5><?php echo $totalOpen; ?></h5>
</div>
</div>

<div class="col-md-3 mb-2">
<div class="stat-card bg-danger">
<h6>SLA Breached</h6>
<h5><?php echo $totalBreached; ?></h5>
</div>
</div>

</div>

<!-- CHARTS -->
<div class="row">

<div class="col-md-6 mb-4">
<div class="card p-3">
<h6 class="text-primary">Ticket Status</h6>
<div class="chart-box">
<canvas id="barChart"></canvas>
</div>
</div>
</div>

<div class="col-md-6 mb-4">
<div class="card p-3">
<h6 class="text-primary">Trend Over Time</h6>
<div class="chart-box">
<canvas id="lineChart"></canvas>
</div>
</div>
</div>

</div>

<!-- TABLE -->
<div class="card p-3 mb-4">

<h6 class="text-primary mb-3">Failure Ticket History</h6>

<div class="table-responsive">

<table class="table table-bordered table-sm">

<thead>
<tr>
<th>Ticket</th>
<th>Department</th>
<th>Job</th>
<th>Status</th>
<th>SLA</th>
<th>Date</th>
</tr>
</thead>

<tbody>

<?php foreach($rows as $r){
$breached = isSlaBreached($r['reported_date'],$r['status']);
?>
<tr>

<td>
<?php echo !empty($r['ticket_no']) ? $r['ticket_no'] : "N/A"; ?>
</td>

<td><?php echo $r['department_name']; ?></td>
<td><?php echo $r['job_name']; ?></td>
<td><?php echo $r['status']; ?></td>

<td>
<?php if($breached){ ?>
<span class="badge bg-danger">BREACHED</span>
<?php } else { ?>
<span class="badge bg-success">OK</span>
<?php } ?>
</td>

<td><?php echo $r['reported_date']; ?></td>

</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

</div>

</div>

<footer>
© <?php echo date("Y"); ?> Service Tracking System
</footer>

<script>

/* BAR */
new Chart(document.getElementById('barChart'), {
type:'bar',
data:{
labels:['Logged','Resolved','Open','SLA Breached'],
datasets:[{
data:[
<?php echo $totalLogged; ?>,
<?php echo $totalResolved; ?>,
<?php echo $totalOpen; ?>,
<?php echo $totalBreached; ?>
]
}]
},
options:{responsive:true,maintainAspectRatio:false}
});

/* LINE */
new Chart(document.getElementById('lineChart'), {
type:'line',
data:{
labels:<?php echo json_encode(array_keys($timeline)); ?>,
datasets:[{
data:<?php echo json_encode(array_values($timeline)); ?>,
fill:false,
tension:0.3
}]
},
options:{responsive:true,maintainAspectRatio:false}
});

/* PDF */
function downloadPDF(){
    const element = document.getElementById('reportContent');

    html2pdf()
    .from(element)
    .set({
        margin:0.5,
        filename:'failure_report.pdf',
        html2canvas:{scale:2, scrollY:0},
        jsPDF:{format:'a4', orientation:'landscape'}
    })
    .save();
}

</script>

</body>
</html>