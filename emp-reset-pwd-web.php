<?php
// Initialize the session
/*
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["emploggedin"]) || $_SESSION["emploggedin"] !== true){
    header("location: emp-login.php");
    exit;
}
 
$emp_id =  $_SESSION["emp_id"] ;
$mgr_id = $_SESSION["mgr_id"] ;
 */
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

     // Validate new password
     if(empty($new_password)){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen($new_password) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    }  
    
    // Validate confirm password
    if(empty($new_password_err) && empty($confirm_password)){
        $confirm_password_err = "Please confirm the password.";
    } else if(empty($new_password_err) && ($new_password != $confirm_password)){
        $confirm_password_err = "Password did not match.";
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err) && !empty($_POST['fp_code'])) { 

        $fp_code = '';
        $fp_code = $_POST['fp_code'];

        $sql = "SELECT * FROM tj_login where forgot_pass_identity = ? AND reset_password = '1' " ;

        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_fpi );
            $param_fpi =  $fp_code;

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                //if there is user found
                if(mysqli_stmt_num_rows($stmt) == 1 ){  
                    
                    $sql = "UPDATE tj_login SET emp_pwd = ?, reset_password = '0' WHERE forgot_pass_identity = ? AND reset_password = '1' " ;

                    if($stmt = mysqli_prepare($conn, $sql)){
                        mysqli_stmt_bind_param($stmt, "ss", $param_emp_pwd ,$param_fpi );
            
                        $param_emp_pwd = password_hash($new_password, PASSWORD_DEFAULT);
                        $param_fpi =  $fp_code;

                        if(mysqli_stmt_execute($stmt)){
                            //session_destroy();
                            header("location: emp-login.php?status=201");
                            //$mgr_id_succ = "Password changed successfully." ; 
                        } else{
                            $confirm_password_err=  "Some problem occurred, please try again.";
                        }

                    }
                }
                else if (mysqli_stmt_num_rows($stmt) > 1 ){
                    $confirm_password_err = "error, multiple accounts linked . contact admin" ; 
                }
                else{
                    $confirm_password_err = "link expired or invalid, try resetting password again or contact admin.";
                }
            }
            else{
                $confirm_password_err = "unable to reset . try again.";
            }
            
        }
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
    <title>TimeJet | Password Reset</title>
    <?php include('header.php'); ?>
     
</head>
<body>
    <nav class="navbar fixed-top navbar-expand-sm bg-light navbar-light">
        <div class="container justify-content-center">
            <a class="navbar-brand" href="index.php">
            <img src="images/logo2.png" width="50" height="40" class="d-inline-block align-top" alt="">
            </a>
        
            <a class="navbar-brand mr-auto" href="index.php">TimeJet</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav mr-auto mt-2 mt-lg-0">
                <a class="nav-item nav-link" href="emp-dash.php">Dashboard <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="emp-report.php">Reports</a>
                <a class="nav-item nav-link active" href="emp-reset-pwd.php">Reset Password</a>
                <a class="nav-item nav-link" href="emp-logout.php">Log out</a>
                <a class="nav-item nav-link" href="https://docs.google.com/forms/d/e/1FAIpQLScfZ500QXUwWtBzKN4hrl1E4V_1GqortGoFRQe8V0ucLPbOIw/viewform">Contact Us</a>
                </div>
            </div>
        </div>
    </nav>
 
    <br>
	<br>
	<br>
    
    <div class="container justify-content-center">
        <div class="card">
            <div class="card-body">
                <h5>Reset Password</h5>
                <p>Please fill out this form to reset your password.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
                    <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                        <span class="help-block"><?php echo $new_password_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                        <span class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="fp_code" value="<?php echo $_REQUEST['fp_code']; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a class="btn btn-link" href="emp-dash.php">Cancel</a>
                    </div>
                </form>
            </div>    
        </div>    
    </div>    
            
	<footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA. </a>
			All Rights Reserved
		</div>
	</footer> 
</body>
</html>




