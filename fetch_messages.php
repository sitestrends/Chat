<?php
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/admin/class/User3.php');

$user_id = $_SESSION['userid'];

$messages = mysqli_query($db, "
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