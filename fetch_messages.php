<?php
include('User3.php');
$conn = new mysqli($DB_SERVER ="localhost", $DB_USER = "root", 
$DB_PASS = "", $DB_NAME = "sites");
$user_id = $_SESSION['userid'];

$messages = mysqli_query($conn, "
    SELECT * FROM site_messages 
    WHERE user_id = $user_id 
    ORDER BY created DESC
");

while ($msg = mysqli_fetch_assoc($messages)) {
    echo "<div class='message'>";
    echo "<strong>" . ucfirst($msg['sender']) . ":</strong> ";
    echo $msg['message'];
    echo "</div>";
}