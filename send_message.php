<?php
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/admin/class/User4.php');
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/members/class/config3.php');

$user_id = $_SESSION['user_id'];
$message = $_POST['message'];

$stmt = $db->prepare("
    INSERT INTO site_messages (user_id, sender, message) 
    VALUES (?, 'user', ?)
");
$stmt->bind_param("is", $user_id, $message);
$stmt->execute();

header("Location: dashboard.php");