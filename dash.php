<?php
include('User3.php');
//include('auth.php:');
//require 'protect.php';
include('header2.php');
$user = new User();
// $_SESSION['userid'] = $user_id;
if (!isset($_SESSION['userid'])) {
    die("Please log in.");
}

$user_id = $_SESSION['userid'];

$user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM site_intake_details WHERE id = $user_id"));
$notes = mysqli_query($db, "SELECT * FROM site_intake_details WHERE id = $user_id ORDER BY created DESC");
$submissions = mysqli_query($db, "SELECT * FROM site_submissions WHERE id = $user_id ORDER BY id DESC");
$messages = mysqli_query($db, "SELECT * FROM site_messages WHERE user_id = $user_id ORDER BY created DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
* { box-sizing:border-box; }

body {
    margin:0;
    font-family:'Inter', sans-serif;
    background:linear-gradient(135deg, #eef2ff, #f8fafc);
    display:flex;
}

/* SIDEBAR */
.sidebar {
    margin-top: 25px;
    border: 2px #f8db19 solid;
    border-radius: 52px;
    width:240px;
    background:rgba(17,24,39,0.95);
    backdrop-filter: blur(10px);
    color:#fff;
    padding:20px;
}

.sidebar h2 {
    font-size:18px;
    margin-bottom:30px;
}

.sidebar a {
    display:block;
    padding:10px;
    border-radius:8px;
    color:#cbd5e1;
    text-decoration:none;
    margin-bottom:8px;
    transition:0.2s;
}

.sidebar a:hover {
    background:#1f2937;
    color:#fff;
}

/* MAIN */
.main {
    flex:1;
    padding:30px;
    margin: -350px 475px;
}

/* TOPBAR */
.topbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

/* CARDS */
.card {
    background:rgba(255,255,255,0.7);
    backdrop-filter: blur(12px);
    border-radius:16px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    transition:0.2s;
}

.card:hover {
    transform:translateY(-4px);
}

/* GRID */
.grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
    gap:20px;
}

/* TABLE */
.table {
    width:100%;
    border-collapse:collapse;
}

.table th, .table td {
    padding:12px;
    border-bottom:1px solid #eee;
}

/* BADGES */
.badge {
    padding:5px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}
/*
.pending { background:#fef3c7; }
.completed { background:#dcfce7; }
.unpaid { background:#fee2e2; }
.paid { background:#d1fae5; }*/

/* BUTTON */
.btn {
    background:linear-gradient(135deg, #6366f1, #2563eb);
    color:#fff;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
}
.status { font-weight:bold; }
.paid { color:green; }
.unpaid { color:red; }
.completed { color:green; }
.pending { color:orange; }
textarea { width:100%; height:80px; 
}

/* DROPZONE */
.dropzone {
    border:2px dashed #cbd5e1;
    padding:40px;
    text-align:center;
    border-radius:12px;
    background:#fff;
}

/* MESSAGE */
.message {
    background:#fff;
    padding:12px;
    border-radius:10px;
    margin-bottom:10px;
}

.card:hover {
    transform: translateY(-3px);
    transition: 0.2s;
}

@media (max-width: 768px) {
   /* .sidebar { display:none; }*/
   .main {
    margin: 10px -15px;
    display: contents;
}
.dashb {
    overflow-x: auto;
}
}
</style>

<script>
function showTab(tab) {
    document.querySelectorAll('.section').forEach(el => el.style.display='none');
    document.getElementById(tab).style.display='block';
}

setInterval(() => {
    fetch('fetch_messages.php')
    .then(res => res.text())
    .then(data => document.getElementById('messagesBox').innerHTML = data);
}, 5000);
</script>

</head>

<body>


<div class="dashb">		
		<div class="sectionTitle fadeInDown hline">
            <h2 class="themecolor">Sites For Trends</h2></strong>
        </div>	

<div class="sidebar">
<div id="particles-js"></div>
    <h2>🚀 Client Panel</h2>
    <a onclick="showTab('dashboard')">Dashboard</a>
    <a onclick="showTab('projects')">Projects</a>
    <a onclick="showTab('uploads')">Uploads</a>
    <a onclick="showTab('messages')">Messages</a>
</div>

<div class="main">

<div class="topbar">
    <h2>Welcome, <?php echo $user['full_name']; ?></h2>
</div>
    <h3><span><?php echo $user['email']; ?></span></h3><br />
<!-- DASHBOARD -->
<div id="dashboard" class="section">

<div class="grid">
    <div class="card">
        <h4>Total Projects</h4>
        <h2><?php echo mysqli_num_rows($submissions); ?></h2>
    </div>

    <div class="card">
        <h4>Messages</h4>
        <h2><?php echo mysqli_num_rows($messages); ?></h2>
    </div>
</div>

</div>

<!-- PROJECTS -->
<div id="projects" class="section" style="display:none;">

<div class="card">
<h3>Your Projects</h3>

<table class="table">
<tr>
<th>Project</th>
<th>Status</th>
<th>Payment</th>
<th></th>
</tr>

<?php while ($row = mysqli_fetch_assoc($notes)): ?>
<tr>
<td><?php echo $row['project_name'] ?: "Project #".$row['id']; ?></td>

<td><span class="badge <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>

<td><span class="badge <?php echo $row['payment_status']; ?>"><?php echo $row['payment_status']; ?></span></td>

<td>
<?php if ($row['status'] === 'completed'): ?>
<a class="btn" href="download_zip.php?id=<?php echo $row['id']; ?>">Download</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>

</table>
</div>

</div>

<!-- UPLOAD -->
<div id="uploads" class="section" style="display:none;">
<div class="card">
<h3>Upload Files</h3>

<form action="process_intake.php" method="POST" enctype="multipart/form-data">
<div class="dropzone">Drag & Drop Files</div><br>
<input type="file" name="brand_files[]" multiple><br><br>
<button class="btn">Upload</button>
</form>

</div>
</div>

<!-- MESSAGES -->
<div id="messages" class="section" style="display:none;">

<div class="card">
<h3>Messages</h3>

<div id="messagesBox">
<?php while ($msg = mysqli_fetch_assoc($messages)): ?>
<div class="message">
<strong><?php echo ucfirst($msg['sender']); ?>:</strong>
<?php echo $msg['message']; ?>
</div>
<?php endwhile; ?>
</div>

<hr>

<form method="POST" action="send_message.php">
<textarea name="message" style="width:100%; height:70px;"></textarea><br><br>
<button class="btn-primary">Send</button>
</form>

</div>

</div>

</div>

</div>