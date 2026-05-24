
<?php

include 'auth.php';

if($_SESSION['role'] !== "Admin"){
    header("Location: service_usage.php");
    exit;
}


include 'config.php';

/* TOTAL COUNTS */
$dept_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM department"))['total'];

$engineer_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='Engineer'"))['total'];

$visit_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM service_usage"))['total'];

$days_used = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(days_used) as total FROM service_usage"))['total'];
?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f8ff;
}

.navbar{
    background:#0d6efd;
}

.navbar-brand{
    color:white !important;
    font-weight:bold;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.stat-card{
    background:white;
    padding:20px;
    border-radius:15px;
    text-align:center;
}

.stat-number{
    font-size:28px;
    font-weight:bold;
    color:#0d6efd;
}

.section-title{
    color:#0d6efd;
    font-weight:bold;
    margin-top:30px;
}

.alert-low{
    background:#fff3cd;
    border-left:5px solid #ffc107;
    padding:10px;
    border-radius:8px;
}

</style>

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">

    <div class="container">

        <a class="navbar-brand" href="#">
            Admin Dashboard
        </a>

        <div>

            <a href="index.php" class="btn btn-primary btn-sm">Home</a>
<a href="analytics.php" class="btn btn-primary btn-sm">Analytics</a>
<a href="report.php" class="btn btn-primary btn-sm">Reports</a>
<a href="log_failure.php" class="btn btn-primary btn-sm">Log Failure</a>
<a href="report_failure.php" class="btn btn-primary btn-sm">Failure Report</a>
<a href="logout.php" class="btn btn-primary btn-sm">Logout</a>
<span class="text-white me-3">👤 <?php echo $_SESSION['name']; ?></span>

        </div>

    </div>

</nav>

<div class="container mt-4">

    <!-- STATS -->
    <div class="row">

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?php echo $dept_count; ?></div>
                <p>Departments</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?php echo $engineer_count; ?></div>
                <p>Engineers</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?php echo $visit_count; ?></div>
                <p>Total Visits</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?php echo $days_used; ?></div>
                <p>Days Used</p>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <h4 class="section-title">Quick Actions</h4>

    <div class="row">

        <div class="col-md-4">
            <a href="departments.php" class="btn btn-primary w-100">
                Manage Departments
            </a>
        </div>

        <div class="col-md-4">
            <a href="manage_engineers.php" class="btn btn-success w-100">
                Manage Engineers
            </a>
        </div>

        <div class="col-md-4">
            <a href="report.php" class="btn btn-warning w-100">
                Generate Reports
            </a>
        </div>

    </div>

    <!-- RECENT ACTIVITY -->
    <h4 class="section-title">Recent Activity</h4>

    <div class="card p-3">

        <table class="table table-bordered">

            <thead class="table-primary">

                <tr>
                    <th>Engineer</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Comments</th>
                </tr>

            </thead>

            <tbody>

            <?php

            $recent = mysqli_query($conn,
            "SELECT service_usage.*, department.department_name
            FROM service_usage
            INNER JOIN department
            ON service_usage.department_id = department.id
            ORDER BY service_usage.id DESC LIMIT 5");

            while($r = mysqli_fetch_assoc($recent)){
            ?>

            <tr>
                <td><?php echo $r['technician_name']; ?></td>
                <td><?php echo $r['department_name']; ?></td>
                <td><?php echo $r['service_date']; ?></td>
                <td><?php echo $r['comments']; ?></td>
            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>