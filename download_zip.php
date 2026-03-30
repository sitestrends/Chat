<?php
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/admin/class/User3.php');
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/members/class/config3.php');
require_once('ZipBuilder.php');

$submission_id = $_GET['id'] ?? 0;

// Get all files for this submission
$result = mysqli_query($db, "SELECT file_path FROM uploads WHERE submission_id = $submission_id");

$files = [];

while ($row = mysqli_fetch_assoc($result)) {
    if (file_exists($row['file_path'])) {
        $files[] = $row['file_path'];
    }
}

// Build ZIP
$zipFile = "zips/submission_" . $submission_id . ".zip";

$builder = new ZipBuilder();
$builder->setDirectories($files); // or setFiles depending on your class
$builder->setDestination($zipFile);
$builder->create();

if (file_exists($zipFile)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
    header('Content-Length: ' . filesize($zipFile));

    readfile($zipFile);
    exit;
}
?>