<?php
require 'vendor/autoload.php';
include 'config.php';

use Dompdf\Dompdf;

if(!isset($_GET['id'])){
    die("Invalid request");
}

$id = $_GET['id'];

/* GET FAILURE DATA */
$query = mysqli_query($conn,
"SELECT bf.*, d.department_name
 FROM backup_failures bf
 JOIN department d ON bf.department_id = d.id
 WHERE bf.id='$id'");

$row = mysqli_fetch_assoc($query);

if(!$row){
    die("Record not found");
}

/* BUILD HTML */
$html = "
<h2>Backup Failure Report</h2>
<hr>

<p><strong>Department:</strong> {$row['department_name']}</p>
<p><strong>Job Name:</strong> {$row['job_name']}</p>
<p><strong>Failure Reason:</strong> {$row['failure_reason']}</p>

<p><strong>Status:</strong> {$row['status']}</p>
<p><strong>Reported By:</strong> {$row['reported_by']}</p>
<p><strong>Reported Date:</strong> {$row['reported_date']}</p>
<p><strong>Root Cause:</strong> {$row['root_cause']}</p>
<p><strong>Solution:</strong> {$row['solution']}</p>
<p><strong>Resolved Date:</strong> {$row['resolved_date']}</p>

<hr>
<p><em>Generated on: ".date('Y-m-d H:i:s')."</em></p>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);

/* PAPER SETTINGS */
$dompdf->setPaper('A4', 'portrait');

/* RENDER */
$dompdf->render();

/* DOWNLOAD */
$dompdf->stream("failure_report_".$id.".pdf", ["Attachment" => true]);