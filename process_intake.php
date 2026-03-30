<?php
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/admin/class/User4.php');
include('/home/1379323.cloudwaysapps.com/cvaateanrh/private_html/members/class/config3.php');
//include('auth.php:');
mysqli_query($db, "INSERT INTO site_submissions () VALUES ()");
$submission_id = mysqli_insert_id($db);
//echo "Submission ID: " . $submission_id;
//exit;
/*if (!mysqli_query($db, "INSERT INTO site_submissions () VALUES ()")) {
    die("DB Error: " . mysqli_error($db));
}

$submission_id = mysqli_insert_id($db);

echo "Submission ID 2: " . $submission_id;
exit;   */

$_SESSION['last_submission_id'] = $submission_id;
// Convert arrays to comma-separated strings
$branding_assets = isset($_POST['branding_assets']) ? implode(', ', $_POST['branding_assets']) : '';
$main_goal       = isset($_POST['main_goal'])       ? implode(', ', $_POST['main_goal'])       : '';
$files           = isset($_POST['brand_files'])           ? implode(', ', $_POST['brand_files'])           : '';
$pages           = isset($_POST['pages'])           ? implode(', ', $_POST['pages'])           : '';
//$brand_files     = isset($_POST['brand_files'])     ? implode(', ', $_POST['brand_files'])     : '';


$baseUploadDir = "uploads/";

if (!empty($_FILES['brand_files']['name'][0])) {

    // Create submission
    mysqli_query($db, "INSERT INTO site_submissions () VALUES ()");
    $user_id = mysqli_insert_id($db);

    foreach ($_FILES['brand_files']['tmp_name'] as $key => $tmp_name) {

        $originalName = $_FILES['brand_files']['name'][$key];
        $fileError    = $_FILES['brand_files']['error'][$key];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowed = ['pdf','doc','docx','jpg','jpeg','png','gif','mp3','wav','mp4','mov'];

        if (!in_array($ext, $allowed)) continue;
        if ($fileError !== 0) continue;

        // 📂 Determine folder
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            $type = 'image';
            $uploadDir = $baseUploadDir . "images/";
        } elseif (in_array($ext, ['mp3','wav'])) {
            $type = 'audio';
            $uploadDir = $baseUploadDir . "audio/";
        } elseif (in_array($ext, ['mp4','mov'])) {
            $type = 'video';
            $uploadDir = $baseUploadDir . "video/";
        } else {
            $type = 'document';
            $uploadDir = $baseUploadDir . "documents/";
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Unique file name
        $newName = uniqid('brand_', true) . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($tmp_name, $destination)) {

            // 💾 SAVE TO DB
            $stmt = $db->prepare("INSERT INTO site_uploads (submission_id, file_path, file_type, original_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $submission_id, $destination, $type, $originalName);
            $stmt->execute();
        }
    }

   // echo "Submission ID: " . $submission_id;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Default payment values
                $deposit_paid = 0.00;
                $total_paid = 0.00;
                $payment_status = "Pending";
                $project_status = "Pending";
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $files = $originalName;
                $query = $db->prepare("INSERT INTO site_intake_details (client_id, full_name, business_name, email, phone, project_type, 
                branding_assets, brand_files, main_goal, pages, notes, payment_status, project_status, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $query->bind_param("isssssssssssss", 
                    $_SESSION['client_id'], 
                    $_POST['full_name'], 
                    $_POST['business_name'], 
                    $_POST['email'], 
                    $_POST['phone'], 
                    $_POST['project_type'], 
                    $branding_assets,                    
                    $files,
                    $main_goal,  
                    $pages, 
                    $_POST['notes'],
                    $payment_status,
                    $project_status,
                    $password);

                    $query->execute();
                    
                    $user_id = mysqli_insert_id($db);
                    $_SESSION['userid'] = $user_id;
                    $_SESSION['full_name'] = $_POST['full_name'];
//                  $query->close();
                    
                    //$data = mysqli_fetch_assoc($conn, $r);
                    //return $data['value']; }

                    header("Location: dashboard.php");
}        
//    $result = $query->get_result();

//    if ($result->num_rows === 1) {
//        $_SESSION['client_id'] = $client_id;
//    }
/*
                    $client_id = $db->insert_id;  
                    //$result = mysqli_fetch_array($query, MYSQLI_ASSOC);	
                    //$result = mysqli_query($db, $query);
                    //$result = $db->fetch_array($query);			
                    $row = $result -> fetch_array(MYSQLI_ASSOC);
		            $rows=mysqli_num_rows($result);					
                    $_SESSION['client_id'] = $client_id;
                    $userDetails['id'] = $client_id;
                    */
/*                   
if ($project_type === "business") {
    $total_price = 300.00;
} elseif ($project_type === "personal") {
    $total_price = 75.00;
} else {
    $total_price = 0;
}*/

/////////////
/*if ($_POST['submit']) {
    // Clean the phone number by removing non-digit characters
    $phone_clean = preg_replace('/\D/', '', $_POST['phone']);
    
    // Now you can use $phone_clean for your database insertion
$stmt = $db->prepare("INSERT INTO site_intake_details 
(intake_id, full_name, business_name, email, phone, project_type, branding_assets, main_goals, main_goal_other, pages, pages_other, notes)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("isssssssssss", $intake_id, $_POST['full_name'], $_POST['business_name'], $_POST['email'], $phone_clean, $_POST['project_type'], $_POST['branding_assets'], $_POST['main_goal'], $_POST['main_goal_other'], $_POST['pages'], $_POST['pages_other'], $_POST['notes']);
$stmt->execute();
$stmt->close();

// Check if any checkboxes were actually selected
if (isset($_POST['branding_assets']) && is_array($_POST['branding_assets'])) {
    
    // 1. Prepare the statement once outside the loop
    $stmt1 = $db->prepare("INSERT INTO site_intake_details (branding_assets) 
    VALUES (?, ?, ?, ?, ?)");
    
    // 2. Bind the variable by reference
    // The variable $asset does not need to exist yet
    $stmt1->bind_param("ssss", $logos, $colors, $photos, $content, $social);
    
    // 3. Loop through the submitted array
    foreach ($_POST['branding_assets'] as $asset) {
        // On each loop, $asset gets the next value (e.g., "Logo")
        // and the statement uses it because it was bound by reference
        $stmt1->execute();
    }
    
    $stmt1->close();
}
*/
/*
                // Default payment values
                $deposit_paid = 0.00;
                $total_paid = 0.00;
                $payment_status = "Pending";

                $stmt2 = $db->prepare("INSERT INTO site_intakes 
                (full_name, email, project_type, 
                total_price, required_deposit,deposit_paid, total_paid, balance_remaining, payment_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");


                $stmt2->bind_param("sssddds", 
                    $_POST['full_name'], 
                    $_POST['email'], 
                    $_POST['project_type'], 
                    $total_price,
                    $deposit_paid,
                    $total_paid,
                    $payment_status
                );
                // Auto assign total price
                $stmt2->execute();
                $stmt2->close();

*/
//}

//header("Location: register.php");

//echo "Thank you! Your project details have been submitted.";
?>

