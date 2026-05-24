<?php include 'auth.php'; ?>
<?php
include 'config.php';

/* ADD DEPARTMENT */
if (isset($_POST['add_department'])) {

    $department_name = $_POST['department_name'];
    $otp_days = $_POST['otp_days'];
    $additional_days = $_POST['additional_days'];

    $sql = "INSERT INTO department
    (department_name, otp_days, additional_days)
    VALUES
    ('$department_name', '$otp_days', '$additional_days')";

    mysqli_query($conn, $sql);

    header("Location: departments.php");
    exit;
}

/* DELETE DEPARTMENT */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM department WHERE id=$id");

    header("Location: departments.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Departments</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        /* ✅ FIX FOOTER ISSUE */
        html, body {
            height: 100%;
        }

        body{
            background-color:#f4f8ff;
            display:flex;
            flex-direction:column;
        }

        .content{
            flex:1;
        }

        /* NAVBAR */
        .navbar{
            background-color:#0d6efd;
        }

        .navbar-brand{
            color:white !important;
            font-weight:bold;
        }

        .nav-link{
            color:white !important;
        }

        /* CARDS */
        .card{
            border:none;
            border-radius:15px;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
        }

        h2{
            color:#0d6efd;
            font-weight:bold;
        }

        .btn-primary{
            background-color:#0d6efd;
            border:none;
        }

        .table thead{
            background-color:#0d6efd;
            color:white;
        }

        /* FOOTER */
        footer{
            background:#0d6efd;
            color:white;
            text-align:center;
            padding:12px;
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

        <div class="d-flex gap-2">

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

<!-- CONTENT WRAPPER -->
<div class="content">

<div class="container mt-5">

    <div class="row">

        <!-- ADD FORM -->
        <div class="col-md-4">

            <div class="card p-4">

                <h2 class="mb-4">Add Department</h2>

                <form method="POST">

                    <div class="mb-3">
                        <label>Department Name</label>
                        <input type="text" name="department_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>OTP Days</label>
                        <input type="number" name="otp_days" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Additional Days</label>
                        <input type="number" name="additional_days" class="form-control" required>
                    </div>

                    <button type="submit" name="add_department" class="btn btn-primary w-100">
                        Add Department
                    </button>

                </form>

            </div>

        </div>

        <!-- TABLE -->
        <div class="col-md-8">

            <div class="card p-4">

                <h2 class="mb-4">Departments List</h2>

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>OTP Days</th>
                            <th>Additional Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM department");

                    while($row = mysqli_fetch_assoc($result)) {
                    ?>

                        <tr>
                            <td><?php echo $row['department_name']; ?></td>
                            <td><?php echo $row['otp_days']; ?></td>
                            <td><?php echo $row['additional_days']; ?></td>

                            <td>
                                <a href="departments.php?delete=<?php echo $row['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this department?')">
                                    Delete
                                </a>
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

<!-- FOOTER -->
<footer>
    © <?php echo date("Y"); ?> Service Tracking System | All Rights Reserved
</footer>

</body>
</html>