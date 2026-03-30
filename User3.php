<?php	
session_start();
//require('config.php');
// ADMIN  Original:  https://webdamn.com/user-management-system-with-php-mysql/
class User {//extends Dbconfig {	
//    protected $hostName;
//    protected $userName;
//    protected $password;
//	protected $dbName;
	private $userTable = 'site_intake_details';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 		
//			$database = new dbConfig();            
//            $this -> hostName = $database -> serverName;
//            $this -> userName = $database -> userName;
//            $this -> password = $database ->password;
//			$this -> dbName = $database -> dbName;			
//            $conn = new mysqli(hostName, userName, password, dbName);
$conn = new mysqli($DB_SERVER ="localhost", $DB_USER = "root", 
$DB_PASS = "", $DB_NAME = "sites");

            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
    }	
	 // 1. Modify constructor to accept a table name
   /* public function __construct($tableName = 'site_intake_details'){
        $this->userTable = $tableName;
        // Assume Dbconfig establishes $this->dbConnect
        if(!$this->dbConnect){ 		
			$database = new dbConfig();            
            $this -> hostName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;			
            $conn = new mysqli(hostName, userName, password, dbName);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
    }		*/

	
    // Public method to get all data from the specific table
    public function getAllRecords() {
        $sql = "SELECT * FROM " . $this->userTable;
        return $this->executeAndFetch($sql); // Call the renamed private method
    }

    // Renamed private method to avoid the "getData" conflict
    private function executeAndFetch($sqlQuery) {
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        if(!$result){
            die('Error in query: '. mysqli_error($this->dbConnect));
        }
        $data = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = $row;            
        }
        return $data;
    }


	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}		
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}	
    public function loginStatus (){
		if(empty($_SESSION["userid"])) {
			header("Location: login.php");
		}
	}	
	public function login(){		
		$errorMessage = '';
		if(!empty($_POST["login"]) && $_POST["loginId"]!=''&& $_POST["loginPass"]!='') {	
			$loginId = $_POST['loginId'];
			$password = $_POST['loginPass'];
			if(isset($_COOKIE["loginPass"]) && $_COOKIE["loginPass"] == $password) {
				$password = $_COOKIE["loginPass"];
			} else {
				$password = md5($password);
			}	
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$loginId."' AND status = 'active'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValidLogin = mysqli_num_rows($resultSet);	
			if($isValidLogin){
				if(!empty($_POST["remember"]) && $_POST["remember"] != '') {
					setcookie ("loginId", $loginId, time()+ (10 * 365 * 24 * 60 * 60));  
					setcookie ("loginPass",	$password,	time()+ (10 * 365 * 24 * 60 * 60));
				} else {
					$_COOKIE['loginId' ]='';
					$_COOKIE['loginPass'] = '';
				}
				$userDetails = mysqli_fetch_assoc($resultSet);
				$_SESSION["userid"] = $userDetails['id'];
				$_SESSION["name"] = $userDetails['first_name']." ".$userDetails['last_name'];
				header("location: index.php"); 		
			} else {		
				$errorMessage = "Invalid login!";		 
			}
		} else if(!empty($_POST["loginId"])){
			$errorMessage = "Enter Both user and password!";	
		}
		return $errorMessage; 		
	}
	public function adminLoginStatus(){
		if(empty($_SESSION["adminUserid"])) {
			header("Location: index.php");
		}
	}		
				

public function adminLogin(){		
    $errorMessage = '';
    if(!empty($_POST["login"]) && $_POST["loginId"]!=''&& $_POST["loginPass"]!='') {	
        $email = $_POST['loginId'];
        $lpassword = $_POST['loginPass'];
        
        $sqlQuery = "SELECT * FROM ".$this->userTable." 
            WHERE email='".$email."' AND status = 'active' AND type = 'administrator'";
        $resultSet = mysqli_query($this->dbConnect, $sqlQuery);
        $isValidLogin = mysqli_num_rows($resultSet);
		if (!empty($resultSet->num_rows) && $resultSet->num_rows > 0) {
				$userDetails = $resultSet->fetch_assoc();
				$_SESSION["adminUserid"] = $userDetails['id'];
				$_SESSION["name"] = $userDetails['name'];
                if(password_verify($lpassword, $userDetails["password"])){
                if ($isValidLogin) {
            header("location: dashboard.php"); 
        }
        } else {		
            $errorMessage = "Invalid login!";		 
        }
    }
    } else if(!empty($_POST["login"])){
        $errorMessage = "Enter Both user and password!";	
    }
    return $errorMessage; 		
}

	public function register(){		
		$message = '';
		if(!empty($_POST["register"]) && $_POST["email"] !='') {
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$_POST["email"]."'";
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$isUserExist = mysqli_num_rows($result);
			if($isUserExist) {
				$message = "User already exist with this email address.";
			} else {			
				$authtoken = $this->getAuthtoken($_POST["email"]);
				$insertQuery = "INSERT INTO ".$this->userTable."(first_name, last_name, email, password, authtoken) 
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".md5($_POST["passwd"])."', '".$authtoken."')";
				$userSaved = mysqli_query($this->dbConnect, $insertQuery);
				if($userSaved) {				
					$link = "<a href='http://webdamn.com/demo/user-management-system/verify.php?authtoken=".$authtoken."'>Verify Email</a>";			
					$toEmail = $_POST["email"];
					$subject = "Verify email to complete registration";
					$msg = "Hi there, click on this ".$link." to verify email to complete registration.";
					$msg = wordwrap($msg,70);
					$headers = "From: info@webdamn.com";
					if(mail($toEmail, $subject, $msg, $headers)) {
						$message = "Verification email send to your email address. Please check email and verify to complete registration.";
					}
				} else {
					$message = "User register request failed.";
				}
			}
		}
		return $message;
	}	
/*	public function getAuthtoken($email) {
		$code = md5(889966);
		$authtoken = $code."".md5($email);
		return $authtoken;
	}	*/
	public function getAuthtoken($email) {
		$session_id = 'session_id';
		$authtoken = 'session_id';
		$authtoken = $session_id;
		return $authtoken;
	}
	public function verifyRegister(){
		$verifyStatus = 0;
		if(!empty($_GET["authtoken"]) && $_GET["authtoken"] != '') {			
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE authtoken='".$_GET["authtoken"]."'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValid = mysqli_num_rows($resultSet);	
			if($isValid){
				$userDetails = mysqli_fetch_assoc($resultSet);
				$authtoken = $this->getAuthtoken($userDetails['email']);
				if($authtoken == $_GET["authtoken"]) {					
					$updateQuery = "UPDATE ".$this->userTable." SET status = 'active'
						WHERE id='".$userDetails['id']."'";
					$isUpdated = mysqli_query($this->dbConnect, $updateQuery);					
					if($isUpdated) {
						$verifyStatus = 1;
					}
				}
			}
		}
		return $verifyStatus;
	}	
	public function userDetails() {
		$sqlQuery = "SELECT * FROM ".$this->userTable." WHERE id ='".$_SESSION['client_id']."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
	}		

	public function editAccount () {
		$message = '';
		$updatePassword = '';
		$passhash = password_hash($password, PASSWORD_DEFAULT);
		if(!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] != $_POST["cpasswd"]) {
			$message = "Confirm passwords do not match.";
		} else if(!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] == $_POST["cpasswd"]) {
			$updatePassword = ", password='".$passhash."' ";
		}		
		$updateQuery = "UPDATE ".$this->userTable." 
			SET name = '".$_POST["name"]."', 
			email = '".$_POST["email"]."', 
			paid_amount = '".$_POST["paid_amount"]."' , 
			payment_status = '".$_POST["payment_status"]."', 
			created = '".$_POST["created"]."', 
			type = '".$_POST["type"]."',
			status = '".$_POST["status"]."'  
			$updatePassword
			WHERE id ='".$_SESSION["userid"]."'";
		$isUpdated = mysqli_query($this->dbConnect, $updateQuery);	
		if($isUpdated) {
			$verifyStatus = 1;
			$_SESSION["name"] = $_POST['name'];
			$message = "Account details saved.";
		}
		return $message;
	}
	public function resetPassword(){
		$message = '';
		if($_POST['email'] == '') {
			$message = "Please enter username or email to proceed with password reset";			
		} else {
			$sqlQuery = "
				SELECT email 
				FROM ".$this->userTable." 
				WHERE email='".$_POST['email']."'";			
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$numRows = mysqli_num_rows($result);
			if($numRows) {			
				$user = mysqli_fetch_assoc($result);
				$authtoken = $this->getAuthtoken($user['email']);
				$link="<a href='https://www.webdamn.com/demo/user-management-system/reset_password.php?authtoken=".$authtoken."'>Reset Password</a>";				
				$toEmail = $user['email'];
				$subject = "Reset your password on examplesite.com";
				$msg = "Hi there, click on this ".$link." to reset your password.";
				$msg = wordwrap($msg,70);
				$headers = "From: info@webdamn.com";
				if(mail($toEmail, $subject, $msg, $headers)) {
					$message =  "Password reset link send. Please check your mailbox to reset password.";
				}				
			} else {
				$message = "No account exist with entered email address.";
			}
		}
		return $message;
	}
	public function savePassword(){
		$message = '';
		if($_POST['password'] != $_POST['cpassword']) {
			$message = "Password does not match the confirm password.";
		} else if($_POST['authtoken']) {
			$sqlQuery = "
				SELECT email, authtoken 
				FROM ".$this->userTable." 
				WHERE authtoken='".$_POST['authtoken']."'";			
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$numRows = mysqli_num_rows($result);
			if($numRows) {				
				$userDetails = mysqli_fetch_assoc($result);
				$authtoken = $this->getAuthtoken($userDetails['email']);
				if($authtoken == $_POST['authtoken']) {
					$sqlUpdate = "
						UPDATE ".$this->userTable." 
						SET password='".md5($_POST['password'])."'
						WHERE email='".$userDetails['email']."' AND authtoken='".$authtoken."'";	
					$isUpdated = mysqli_query($this->dbConnect, $sqlUpdate);	
					if($isUpdated) {
						$message = "Password saved successfully. Please <a href='login.php'>Login</a> to access account.";
					}
				} else {
					$message = "Invalid password change request.";
				}
			} else {
				$message = "Invalid password change request.";
			}	
		}
		return $message;
	}
	public function getUserList(){
		//$sqlQuery = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['userid']."' AND";
		$sqlQuery = "SELECT * FROM ".$this->userTable." WHERE id !='id' AND";
		if(isset($_POST["search"]["value"])){
			$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR created LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR modified LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR full_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR business_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR email LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR phone LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR project_type LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR branding_assets LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR brand_files LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR main_goal LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR pages LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR notes LIKE "%'.$_POST["search"]["value"].'%" ';			
            $sqlQuery .= ' OR total_price LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR required_deposit LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR deposit_paid LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR total_paid LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR balance_remaining LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR payment_status LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR project_status LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR password LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%") ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column']+1 .' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'] . ' ';
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows1 = mysqli_num_rows($result);
		
		$sqlQuery1 = "SELECT * FROM ".$this->userTable."";// WHERE id !='".$_SESSION['userid']."' ";
		$result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result1);
		
		$userData = array();
		$total = 0;	
		while( $users = mysqli_fetch_assoc($result) ) {		
			$userRows = array();
			$status = '';
			if($users['status'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else if($users['status'] == 'pending') {
				$status = '<span class="label label-warning">Inactive</span>';
			} else if($users['status'] == 'deleted') {
				$status = '<span class="label label-danger">Deleted</span>';
			}		
			$userRows[] = $users['id'];
			$userRows[] = $users['created'];
			$userRows[] = $users['modified'];	
			$userRows[] = $users['full_name'];
			$userRows[] = $users['business_name'];
			$userRows[] = $users['email'];            
			$userRows[] = $users['phone'];
            $userRows[] = $users['project_type'];
            $userRows[] = $users['branding_assets'];
            $userRows[] = $users['brand_files'];
            $userRows[] = $users['main_goal'];
            $userRows[] = $users['pages'];
            $userRows[] = $users['notes'];		
			$userRows[] = "$ ".$users['total_price'];
			$userRows[] = "$ ".$users['required_deposit'];
			$userRows[] = "$ ".$users['deposit_paid'];
			$userRows[] = "$ ".$users['total_paid'];
			$userRows[] = "$ ".$users['balance_remaining'];
			$userRows[] = $users['payment_status'];	
			$userRows[] = $users['project_status'];
			$userRows[] = $users['password'];	
			$userRows[] = $status;						
			$userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			$userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';			
	//		$total = $total + floatval($userRows[] = $users['paid_amount']);
			$userData[] = $userRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows1,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$userData,
			"error"				=>	'',
			"total"				=>  "$".number_format($total, 2)
		);
		echo json_encode($output);
	}       
	public function deleteUser(){
		if($_POST["userid"]) {
			$sqlUpdate = "
				UPDATE ".$this->userTable." SET status = 'deleted'
				WHERE id = '".$_POST["userid"]."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);		
		}
	}
	public function getUser(){
		$sqlQuery = "
			SELECT * FROM ".$this->userTable." 
			WHERE id = '".$_POST["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function saveAdminPassword(){
		$message = '';
		if($_POST['password'] && $_POST['password'] != $_POST['cpassword']) {
			$message = "Password does not match the confirm password.";
		} else {		
			$passhash = password_hash($_POST['cpassword'], PASSWORD_DEFAULT);	
			$sqlUpdate = "
				UPDATE ".$this->userTable." 
				SET password='".$passhash."'
				WHERE id='".$_SESSION["adminUserid"]."' AND type='administrator'";	
			$isUpdated = mysqli_query($this->dbConnect, $sqlUpdate);	
			if($isUpdated) {
				$message = "Password saved successfully.";
			}				
		}
		return $message;
	}
/*	public function adminDetails() {
		
        $_COOKIE["adminUserid"] = $_SESSION["adminUserid"];
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
			WHERE id ='".$_COOKIE['adminUserid']."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
		if($userDetails){
			$_SESSION == $userDetails;
		}
	}	*/
	public function adminDetails() {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
			WHERE id ='".$_SESSION["adminUserid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
	}
	public function updateUser() {
		if($_POST['userid']) {	
			$date = date("Y-m-d H:i:s");
			$password = $_POST['password'];
			$passhash = password_hash($password, PASSWORD_DEFAULT);
			$updateQuery = "UPDATE ".$this->userTable." 
			SET name = '".$_POST["name"]."', 
			email = '".$_POST["email"]."', 
			paid_amount = '".$_POST["paid_amount"]."', 
			payment_status = '".$_POST["payment_status"]."', 
			password = '".$passhash."' , 
			modified = '".$date."', 
			status = '".$_POST["status"]."', 
			type = '".$_POST['user_type']."'
			WHERE id ='".$_POST["userid"]."'";
			$isUpdated = mysqli_query($this->dbConnect, $updateQuery);		
		}	
	}

	public function addUser() {
		if($_POST["email"]) {
			$date = date("Y-m-d H:i:s");
			$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
			$insertQuery = "INSERT INTO ".$this->userTable."(full_name, email, paid_amount, 
			payment_status, password, modified, type, status)
				VALUES ('".$_POST["full_name"]."', '".$_POST["email"]."', 
				'".$_POST["paid_amount"]."',  '".$_POST["payment_status"]."', 
				'".$password."', '".$date."', '".$_POST['user_type']."',
				'active')";
				$userSaved = mysqli_query($this->dbConnect, $insertQuery);
		}

	}
	public function totalUsers ($status) {
		$query = '';
		if($status) {
			$query = " AND status = '".$status."'";
		}
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
		WHERE id !='".$_SESSION['adminUserid']."' $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function totalSum ($status) { 
		$query = '';
		if($status) {
			$query = " AND status = '".$status."'";
		}
	$sql = "SELECT  SUM(paid_amount) FROM ".$this->userTable."";
	$result = mysqli_query($this->dbConnect, $sql);
	while($row = mysqli_fetch_array($result)){
		return $row['SUM(paid_amount)'];
	}
	}
	public function totalSales ($status) { 
		$query = '';
		if($status) {
			$query = " AND status = '".$status."'";
		}
		$sql = "SELECT  SUM(paid_amount) FROM ".$this->userTable."";
		$result = mysqli_query($this->dbConnect, $sql);
		while($row = mysqli_fetch_array($result)){
			$sales = $row['SUM(paid_amount)'] * 2.9 / 100.0 + .30;
		$fees = $row['SUM(paid_amount)'] - $sales;
		return $fees;
		}
	}
	public function totalFees ($status) { 
		$query = '';
		if($status) {
			$query = " AND status = '".$status."'";
		}
		$sql = "SELECT  SUM(paid_amount) FROM ".$this->userTable."";
		$result = mysqli_query($this->dbConnect, $sql);
		while($row = mysqli_fetch_array($result)){	
		$sales = $row['SUM(paid_amount)'] * 2.9 / 100.0 + .30;
		return $sales;
		}
	}
	public function users () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." ";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function active () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
		WHERE status = 'active' ";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows -1;
	}
	public function pending () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
		WHERE status = 'pending' ";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function deleted () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
		WHERE status = 'deleted' ";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
/*	public function template () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
		WHERE paid_amount = '25.00' ";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows -1;

	}*/
}
?>