<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["mgrloggedin"]) || $_SESSION["mgrloggedin"] !== true){
    header("location: mgr-login.php");
    exit;
}
 
// Include config file
require_once "config.php";
$mgr_id =  $_SESSION["mgr_id"] ;

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
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE tj_login SET mgr_pwd = ? WHERE mgr_id = ? AND user_type = 'mgr' ";

        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_mgr_pwd, $param_mgr_id);

            // Set parameters
            $param_mgr_pwd = password_hash($new_password, PASSWORD_DEFAULT);
            $param_mgr_id = $mgr_id  ;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: mgr-login.php?status=201");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
       
    }
    
    // Close connection
    mysqli_close($conn);
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
                <a class="nav-item nav-link" href="mgr-dash.php">Dashboard <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="mgr-activity-list.php">Activites</a>
                <a class="nav-item nav-link" href="mgr-report.php">Reports</a>
                <a class="nav-item nav-link active" href="mgr-reset-pwd.php">Reset Password</a>
                <a class="nav-item nav-link" href="mgr-logout.php">Log out</a>
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

                <h3>Reset Password</h3>
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
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a class="btn btn-link" href="mgr-dash.php">Cancel</a>
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




