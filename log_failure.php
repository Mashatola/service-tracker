<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* =========================
   GENERATE TICKET NUMBER
========================= */
function generateTicket($conn){
    do {
        $ticket = "#" . rand(1000, 9999) . "#";

        $check = mysqli_query($conn,
        "SELECT id FROM backup_failures WHERE ticket_no='$ticket'");

    } while(mysqli_num_rows($check) > 0);

    return $ticket;
}

/* =========================
   ADD FAILURE LOG
========================= */
if(isset($_POST['add_failure'])){

    $department_id = $_POST['department_id'];
    $job_name = $_POST['job_name'];
    $failure_reason = $_POST['failure_reason'];
    $reported_by = $_SESSION['name'];

    $ticket_no = generateTicket($conn);

    mysqli_query($conn,
    "INSERT INTO backup_failures
    (ticket_no, department_id, job_name, failure_reason, status, reported_by, reported_date)
    VALUES
    ('$ticket_no', '$department_id', '$job_name', '$failure_reason', 'Open', '$reported_by', NOW())");

    header("Location: log_failure.php");
    exit;
}

/* =========================
   UPDATE FAILURE LOG
========================= */
if(isset($_POST['update_failure'])){

    $id = $_POST['id'];
    $status = $_POST['status'];
    $root_cause = $_POST['root_cause'];
    $solution = $_POST['solution'];

    mysqli_query($conn,
    "UPDATE backup_failures SET
        status='$status',
        root_cause='$root_cause',
        solution='$solution',
        resolved_date = IF('$status'='Resolved', NOW(), resolved_date)
     WHERE id='$id'");

    header("Location: log_failure.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Backup Failure Log</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f8ff;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    font-family:Arial;
}
.container{ flex:1; }

.navbar{ background:#0d6efd; }
.navbar-brand{ color:white !important; font-weight:bold; }

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
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
    margin-top:auto;
}

.ticket{
    font-weight:bold;
    color:#0d6efd;
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

<div class="d-flex align-items-center">



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
<span class="text-white me-3">👤 <?php echo $_SESSION['name']; ?></span>



</div>

</div>
</nav>

<div class="container mt-4">

<div class="row">

<!-- FORM -->
<div class="col-md-4">

<div class="card p-4">

<h4 class="text-primary">Log Failure</h4>

<form method="POST">

<div class="mb-2">
<label>Department</label>
<select name="department_id" class="form-control" required>
<option value="">Select Department</option>

<?php
$depts = mysqli_query($conn,"SELECT * FROM department");
while($d = mysqli_fetch_assoc($depts)){
?>
<option value="<?php echo $d['id']; ?>">
<?php echo $d['department_name']; ?>
</option>
<?php } ?>
</select>
</div>

<div class="mb-2">
<input type="text" name="job_name" class="form-control" placeholder="Job Name" required>
</div>

<div class="mb-2">
<textarea name="failure_reason" class="form-control" rows="4" placeholder="Failure Reason" required></textarea>
</div>

<button type="submit" name="add_failure" class="btn btn-primary w-100">
Log Failure
</button>

</form>

</div>

</div>

<!-- TABLE -->
<div class="col-md-8">

<div class="card p-4">

<h4 class="text-primary">Failure History</h4>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>
    <!-- REPORT BUTTON -->
<a href="report_failure.php" class="btn btn-warning btn-sm me-2">
Failure Report 
</a>
<br>
<tr>
    <th>Ticket</th>
    <th>Department</th>
    <th>Job</th>
    <th>Reason</th>
    <th>Status</th>
    <th>Root Cause</th>
    <th>Solution</th>
    <th>Reported By</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php
$result = mysqli_query($conn,
"SELECT bf.*, d.department_name
 FROM backup_failures bf
 JOIN department d ON bf.department_id = d.id
 ORDER BY bf.id DESC");

while($row = mysqli_fetch_assoc($result)){
?>

<tr>
<form method="POST">

<td class="ticket"><?php echo $row['ticket_no']; ?></td>
<td><?php echo $row['department_name']; ?></td>
<td><?php echo $row['job_name']; ?></td>
<td><?php echo $row['failure_reason']; ?></td>

<td>
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<select name="status" class="form-control">
<option <?php if($row['status']=="Open") echo "selected"; ?>>Open</option>
<option <?php if($row['status']=="Investigating") echo "selected"; ?>>Investigating</option>
<option <?php if($row['status']=="Resolved") echo "selected"; ?>>Resolved</option>
</select>
</td>

<td><textarea name="root_cause" class="form-control"><?php echo $row['root_cause']; ?></textarea></td>
<td><textarea name="solution" class="form-control"><?php echo $row['solution']; ?></textarea></td>

<td><?php echo $row['reported_by']; ?></td>

<td>
<button class="btn btn-success btn-sm" name="update_failure">
Update
</button>
</td>

</form>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<!-- FOOTER -->
<footer>
© <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</footer>

</body>
</html>