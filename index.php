<!DOCTYPE html>
<html lang="en">

<head>

<title>Service Tracking System</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */

body{
    background:#f4f8ff;
    font-family:'Segoe UI', sans-serif;
    margin:0;
    padding:0;
}

/* ================= NAVBAR ================= */

.navbar{
    background:linear-gradient(90deg,#0d6efd,#0b5ed7);
    padding:12px 0;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand{
    color:white !important;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:12px;
    font-size:20px;
    text-decoration:none;
}

.navbar-brand img{
    width:55px;
    height:55px;
    object-fit:contain;
    background:white;
    border-radius:10px;
    padding:5px;
}

.brand-text{
    display:flex;
    flex-direction:column;
    line-height:1.2;
}

.brand-text small{
    font-size:11px;
    opacity:0.85;
    font-weight:400;
}

/* ================= HERO SECTION ================= */

.hero{
    background:linear-gradient(135deg,#0d6efd,#4a90ff);
    color:white;
    padding:60px 20px;
}

.hero-content{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
}

.hero-text{
    max-width:520px;
}

.hero-logo{
    width:85px;
    margin-bottom:15px;
}

.hero h1{
    font-size:36px;
    font-weight:800;
    margin-bottom:15px;
}

.hero p{
    font-size:16px;
    line-height:1.6;
    opacity:0.95;
}

.hero-buttons{
    margin-top:20px;
}

.hero-buttons .btn{
    padding:10px 18px;
    border-radius:10px;
    font-weight:600;
    margin-right:8px;
}

.hero-image img{
    width:280px;
    max-width:100%;
}

/* ================= FEATURES ================= */

.section-title{
    text-align:center;
    margin:45px 0 25px;
}

.section-title h2{
    font-weight:800;
    color:#0d6efd;
    font-size:30px;
}

.section-title p{
    color:#666;
    font-size:15px;
}

.feature-card{
    background:white;
    border-radius:14px;
    padding:20px;
    box-shadow:0 5px 14px rgba(0,0,0,0.08);
    height:100%;
    transition:0.3s ease;
    border:1px solid #eef2ff;
}

.feature-card:hover{
    transform:translateY(-4px);
    box-shadow:0 8px 18px rgba(0,0,0,0.10);
}

.feature-icon{
    width:50px;
    height:50px;
    background:#e8f1ff;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:15px;
}

.feature-icon i{
    font-size:22px;
    color:#0d6efd;
}

.feature-card h5{
    font-weight:700;
    margin-bottom:10px;
    font-size:18px;
}

.feature-card p{
    color:#666;
    line-height:1.5;
    font-size:14px;
}

/* ================= STATS SECTION ================= */

.stats-section{
    margin-top:45px;
    background:white;
    padding:30px 15px;
    border-radius:16px;
    box-shadow:0 5px 14px rgba(0,0,0,0.08);
}

.stat-box{
    text-align:center;
}

.stat-box h3{
    color:#0d6efd;
    font-size:30px;
    font-weight:800;
}

.stat-box p{
    color:#666;
    margin-top:5px;
    font-size:14px;
}

/* ================= FOOTER ================= */

footer{
    margin-top:50px;
    background:#0d6efd;
    color:white;
    text-align:center;
    padding:18px;
}

footer p{
    margin:0;
    font-size:14px;
}

/* ================= MOBILE ================= */

@media(max-width:768px){

    .hero{
        text-align:center;
        padding:50px 15px;
    }

    .hero h1{
        font-size:30px;
    }

    .hero-content{
        justify-content:center;
    }

    .navbar-brand{
        font-size:17px;
    }

    .navbar-brand img{
        width:45px;
        height:45px;
    }

    .hero-image img{
        width:220px;
    }
}

</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg">

<div class="container">

<a class="navbar-brand" href="#">

<img src="logo.png" alt="Logo">

<div class="brand-text">

<span>Service Tracking System</span>

<small>Department Support Management</small>

</div>

</a>

</div>

</nav>

<!-- ================= HERO SECTION ================= -->

<section class="hero">

<div class="container hero-content">

<div class="hero-text">



<h1>
Department Service Tracking System
</h1>

<p>
Track engineer visits, monitor department service usage,
manage support operations, and generate professional reports
from one centralized platform.
</p>

<div class="hero-buttons">

<a href="login.php"
class="btn btn-light">

Get Started

</a>



</a>

</div>

</div>



</div>

</section>

<!-- ================= FEATURES SECTION ================= -->

<div class="container">

<div class="section-title">

<h2>System Features</h2>

<p>
Manage and monitor department support services efficiently.
</p>

</div>

<div class="row g-4">

<!-- FEATURE 1 -->
<div class="col-md-4">

<div class="feature-card">

<div class="feature-icon">
<i class="bi bi-speedometer2"></i>
</div>

<h5>Smart Dashboard</h5>

<p>
View service usage, remaining days,
and department activity instantly.
</p>

</div>

</div>

<!-- FEATURE 2 -->
<div class="col-md-4">

<div class="feature-card">

<div class="feature-icon">
<i class="bi bi-person-check"></i>
</div>

<h5>Engineer Tracking</h5>

<p>
Track engineer visits, comments,
and daily support activities.
</p>

</div>

</div>

<!-- FEATURE 3 -->
<div class="col-md-4">

<div class="feature-card">

<div class="feature-icon">
<i class="bi bi-exclamation-triangle"></i>
</div>

<h5>Alerts System</h5>

<p>
Receive alerts when departments
are low on allocated service days.
</p>

</div>

</div>

<!-- FEATURE 4 -->
<div class="col-md-4">

<div class="feature-card">

<div class="feature-icon">
<i class="bi bi-building"></i>
</div>

<h5>Department Management</h5>

<p>
Manage departments and
allocated support days efficiently.
</p>

</div>

</div>

<!-- FEATURE 5 -->
<div class="col-md-4">

<div class="feature-card">

<div class="feature-icon">
<i class="bi bi-shield-lock"></i>
</div>

<h5>Secure Login</h5>

<p>
Role-based secure access
for Admins and Engineers.
</p>

</div>

</div>

<!-- FEATURE 6 -->
<div class="col-md-4">

<div class="feature-card">

<div class="feature-icon">
<i class="bi bi-graph-up-arrow"></i>
</div>

<h5>Reports & Analytics</h5>

<p>
Generate charts, reports,
and export professional PDFs.
</p>

</div>

</div>

</div>

<!-- ================= STATS SECTION ================= -->

<div class="stats-section">

<div class="row">

<div class="col-md-4">

<div class="stat-box">

<h3>10+</h3>

<p>Departments Managed</p>

</div>

</div>

<div class="col-md-4">

<div class="stat-box">

<h3>24/7</h3>

<p>Monitoring & Tracking</p>

</div>

</div>

<div class="col-md-4">

<div class="stat-box">

<h3>100%</h3>

<p>Real-Time Reporting</p>

</div>

</div>

</div>

</div>

</div>

<!-- ================= FOOTER ================= -->

<footer>

<p>
© 2026 Service Tracking System | All Rights Reserved
</p>

</footer>

</body>

</html>