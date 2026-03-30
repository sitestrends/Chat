<?php
include('User3.php');
//include('auth.php:');
//require 'protect.php';
include('header2.php');
$user = new User();
$conn = new mysqli($DB_SERVER ="localhost", $DB_USER = "root", 
$DB_PASS = "", $DB_NAME = "sites");

$user_id = $_SESSION['userid'];
$other_id = $_GET['user_id'];

$stmt = $conn->prepare("
  SELECT * FROM site_messages
  WHERE (sender_id = ? AND receiver_id = ?)
     OR (sender_id = ? AND receiver_id = ?)
  ORDER BY created ASC
");

$stmt->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
  $messages[] = $row;
}

echo json_encode($messages);