<?php
require('User3.php');
require('config.php');
//require('../include/navbar.php');
//include('auth.php:');
$user = new User();
$error = '';
date_default_timezone_set('US/Eastern');
$conn = new mysqli($DB_SERVER ="localhost", $DB_USER = "root", 
$DB_PASS = "", $DB_NAME = "sites");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, full_name FROM site_intake_details WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
                    $user_id = $conn->select_id; 

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();
        $now = date('Y-m-d H:i:s');

/*        if ($user['locked_until'] && $user['locked_until'] > $now) {
            $error = "Account temporarily locked. Try again later.";
            exit;
        }   */
if (password_verify($password, $user['password'])) {

//    session_regenerate_id(true);

//    $_SESSION['client_id'] = $user['id'];
//    $_SESSION['client_name'] = $user['name'];
                    $_SESSION['userid'] = $user['id'];
                    $_SESSION['full_name'] = $user['full_name'];

    // Reset attempts
/*    $reset = $db->prepare("UPDATE site_clients 
                           SET login_attempts=0, locked_until=NULL 
                           WHERE id=?");
    $reset->bind_param("i", $user['id']);
    $reset->execute();
*/
    header("Location: dash.php");
    exit;

} else {

    // 🔐 RATE LIMIT FAILURE BLOCK GOES HERE
/*
    $attempts = $user['login_attempts'] + 1;

    if ($attempts >= 5) {
        $lockUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $update = $db->prepare("UPDATE site_clients 
                                SET login_attempts=?, locked_until=?, last_attempt=NOW()
                                WHERE id=?");
        $update->bind_param("isi", $attempts, $lockUntil, $user['id']);
    } else {
        $update = $db->prepare("UPDATE site_clients 
                                SET login_attempts=?, last_attempt=NOW()
                                WHERE id=?");
        $update->bind_param("ii", $attempts, $user['id']);
    }

    $update->execute();

*/    /*$error = "Invalid email or password.";*/
/*    $reset = $db->prepare("UPDATE site_clients 
                       SET login_attempts=0, locked_until=NULL 
                       WHERE id=?");
    $reset->bind_param("i", $user['id']);
    $reset->execute();
*/    /* remember me */
/*    if (!empty($_POST['remember'])) {

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

    $stmt = $db->prepare("INSERT INTO site_remember_tokens 
                          (client_id, token_hash, expires_at) 
                          VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $tokenHash, $expiry);
    $stmt->execute();
*/
    setcookie(
        "remember_token",
        $user['id'] . ':' . $token,
        time() + (86400 * 30),
        "/",
        "",
        true,
        true
    );
}
/*if (!$user['is_active']) {
    $error = "Please activate your account via email.";
    exit;
}   */
}
} else {
    $error = "Invalid email or password.";
}
//    $stmt->close();
//}
require('header2.php');
?>

<title>Sites For Trends | Website Intake Login</title>
<section class="aboutText login">
<div  class="main-heading text-center" id="homer">
    <img src="../images/img14.jpg" class="main-l img-responsive" alt="pc">
<div class="aboutText">
                <div class=" fadeInDown hlines-h" id="form">
                    <h1>Website Intake Login</h1>
                    <br />
                    <h3></h3>
                        <br />
            <form method="POST" style="max-width:400px;margin:50px auto;">
                    <div class="siteform">
					<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input name="email" type="email" placeholder="Email" required>
                    </div>
                    <div class="siteform">
                        <input name="password" type="password" id="password" placeholder="Password" required><br>
                        <span class="input-group-addon" id="togglePassword"><i class="fa fa-eye"></i></span>
                    </div>
					<div style="margin-top:10px" class="form-group">                               
						<div class="col-sm-12 controls">
                    <button type="submit"
                            class="btn btn-primary">
                        Login 
                    </button>
								  
						</div>						
					</div>
                    <div class="form-group">Remember Me
                        <input type="checkbox"
                            name="remember"
                            id="remember"
                            placeholder="Remember Me" />
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 control">
                            <div class="f-bottom">
                                Don't have a Site Build account! 
                            <a href="register.php">
                                Register 
                            </a>Here. 
                            </div>
                        </div>
                    </div>      
                    <br>      
                    <a href="forgot_password.php" tabindex="7"><i class="fa fa-question">Forgot Password </i></a><br>
                    <!-- <a href="login.php" tabindex="8"><i class="fa fa-sign-in"></i>Login</a><br>-->
                    <a href="index.php" tabindex="9"><i class="fa fa-home"> Home </i></a>
            </form>
    	</div>
    </div>	
</div>
</section>

<Script>
  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#password'); 
  togglePassword.addEventListener('click', function (e) {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
});
</script>

<?php //include('../include/footer.php');?>
<?php //include('../include/scripts1.php');?>