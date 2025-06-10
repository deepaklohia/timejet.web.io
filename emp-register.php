<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$emp_id = $password = $confirm_password = $mgr_id = $emp_name = "";
$emp_id_err = $password_err = $confirm_password_err = $mgr_id_err = $emp_name_err = "";
$emp_count = 0 ;
$sub_limit = 0 ;
$errorStatus = false;
$sub_day_left = 0 ;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mgr_id = trim($_POST["mgr_id"]);
    $emp_id = trim($_POST["emp_id"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $emp_name = trim($_POST["emp_name"]);
    
    // Validate manager name
    if(empty($mgr_id)){
        $mgr_id_err = "Please enter manager email.";
        $errorStatus = true;
    }
     else{
        // Prepare a select statement
        $sql = "SELECT id, subscription_limit, DATEDIFF(subscription_end_date , CURRENT_DATE) AS sub_stat FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr'";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $mgr_id);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 0){
                    $mgr_id_err = "manager email not found. use <a href='mgr-register.php'>Manager Signup </a> first." ;
                    $errorStatus = true ;
                }
                else{
                    //GET SUB LIMIT AND SUBSCRIPTION INFO
                    mysqli_stmt_bind_result($stmt, $id, $subscription_limit, $sub_stat);
                    mysqli_stmt_fetch($stmt);
                    $sub_limit = $subscription_limit ;
                    $sub_day_left = $sub_stat;
                    mysqli_stmt_free_result($stmt);

                    //GET EMP COUNT
                    $sql = "SELECT id FROM tj_login WHERE mgr_id = ? AND user_type = 'emp'";
         
                    if($stmt = mysqli_prepare($conn, $sql)){
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "s", $mgr_id);
                        
                        // Attempt to execute the prepared statement
                        if(mysqli_stmt_execute($stmt)){
                            /* store result */
                            mysqli_stmt_store_result($stmt);
                            $emp_count = mysqli_stmt_num_rows($stmt) ;
                            mysqli_stmt_free_result($stmt);
                        }
                    }
 
                    if ($sub_day_left <= 0){
                        $mgr_id_err = "Your Subscription has Expired . Upgrade to <a href='https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro' target='_blank'>TimeJet Pro</a> to renew. ";
                        $errorStatus = true;
                    }
                    else if ($emp_count >= $sub_limit && $sub_day_left <= 7){
                        //$mgr_id_err = "subscrption limit reached. maximum : ".$sub_limit." users can be created . Upgrade to TimeJet Pro. ";
                        $mgr_id_err = "Subscription limit reached. Upgrade to <a href='https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro' target='_blank'>TimeJet Pro</a>. ";
                        $errorStatus = true;
                    }
                }

            } else{
                $mgr_id_err = "error updating mgr records, try again" ;
                $errorStatus = true;
                //echo "Oops! Something went wrong while checking manager username. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    if ($errorStatus == false){
        // Validate username
        if(empty($emp_id)){
            $emp_id_err = "Please enter employee Email.";
            $errorStatus = true;
        } 
        else{
            // Prepare a select statement
            $sql = "SELECT id FROM tj_login WHERE emp_id = ? AND mgr_id = ? AND user_type = 'emp' ";
        
            if($stmt = mysqli_prepare($conn, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $emp_id, $mgr_id);
                                                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    /* store result */
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) >= 1){
                        $emp_id_err = "email already registered, use <a href='emp-login.php'>Employee Login </a> to login ";
                        $errorStatus = true;
                    }
                    mysqli_stmt_free_result($stmt);
                } 
                else{
                $emp_id_err = "error updating records, try again" ;
                $errorStatus = true;
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Validate manager name
    if ($errorStatus == false && empty($emp_name)){
        $emp_name_err = "Please enter your name.";
        $errorStatus = true;
    }
    
     //if user doest exists already
     if($errorStatus == false){
        // Validate password
        if(empty($password)){
            $password_err = "Please enter a password.";    
            $errorStatus = true ;
        }
        // Validate confirm password
        elseif(empty($confirm_password)){
            $confirm_password_err = "Please confirm password.";
            $errorStatus = true ;  
        }

        //if password is not empty
        if($errorStatus == false && strlen($password) < 6){
            $password_err = "Password must have atleast 6 characters.";
            $errorStatus = true ; 
        } 
        elseif($errorStatus == false && strlen($confirm_password) < 6){
            $confirm_password_err = "Password must have atleast 6 characters.";
            $errorStatus = true ;  
        }  
        
        if($errorStatus == false && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
            $errorStatus = true ; 
        }
    }

    // Check input errors before inserting in database
    if($errorStatus == false){
        
        // Prepare an insert statement
        $sql = "INSERT INTO tj_login (mgr_id, emp_id, emp_name, emp_pwd, user_type, subscription_limit) VALUES (?, ?, ?, ?, 'emp', '0')";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $mgr_id, $emp_id, $emp_name,  $param_password );
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                mysqli_stmt_free_result($stmt);                
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                
                /*
                #NEW EMP SIGNUP EMAIL
                require 'PHPMailer/PHPMailerAutoload.php';
                
                #************************************* 
                $id = 'notify-email@gmail.com' ;
                $pass = 'vE@bex-bindeq-m8mwo7';
                $to = 'youremail@gmail.com';
                #************************************* 

                $subject = "dlohia | timejet : New Employee Signup" ;
                $mailBody = 'Dear Deepak Lohia,
                <br><br>
                a new employee ('.$emp_id.') has signed up under manager ('.$mgr_id.').
                <br><br>Regards,
                <br>DLA | TimeJet ';

                
                $mail = new PHPMailer;

                $mail->isSMTP();                                   // Set mailer to use SMTP
                $mail->Host = 'smtp.office365.com';                    // Specify main and backup SMTP servers
                $mail->SMTPAuth = true ;                            // Enable SMTP authentication
                $mail->Username = $id ;   // SMTP username
                $mail->Password = $pass ;                     // SMTP password
                $mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587;                                 // TCP port to connect to

                $mail->setFrom($id, 'DLA | TimeJet');
                $mail->addReplyTo($emp_id, 'DLA | TimeJet');
                $mail->addAddress($to);   // To Email 
                $mail->addCC($id);
                //$mail->addBCC('admin@dlohia.com');

                $mail->isHTML(true);  // Set email format to HTML

                $mail->Subject = $subject ;
                $mail->Body = $mailBody;

                if(!$mail->send()) {
                    $errorStatus = $mail->ErrorInfo;; 
                } 
                */

                header("location: emp-login.php?status=200");
            } else{
                echo "Something went wrong while inserting data. Please try again later.";
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
	<title>TimeJet | Employee Signup</title>
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
                <a class="nav-item nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link active" href="emp-login.php">Employee Login</a>
                <a class="nav-item nav-link" href="mgr-login.php">Manager Login</a>
                <a class="nav-item nav-link" href="privacy-policy.php">Privacy Policy</a>
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
                <h5>Employee Sign Up</h5>
                <p>Please fill this form to create an account.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                    <div class="form-group <?php echo (!empty($mgr_id_err)) ? 'has-error' : ''; ?>">
                        <label>Your Manager Email</label>
                        <input id="mgr_id_" type="email" name="mgr_id" class="form-control" value="<?php echo $mgr_id; ?>">
                        <span id="mgr_id__" class="help-block"><?php echo $mgr_id_err; ?></span>
                    </div>  
                     
                    <div class="form-group <?php echo (!empty($emp_id_err)) ? 'has-error' : ''; ?>">
                        <label>Employee Email</label>
                        <input id="emp_id_" type="email" name="emp_id" class="form-control" value="<?php echo $emp_id; ?>">
                        <span id="emp_id__" class="help-block"><?php echo $emp_id_err; ?></span>
                    </div>  
                    
                    <div class="form-group <?php echo (!empty($emp_name_err)) ? 'has-error' : ''; ?>">
                        <label>Employee Name </label>
                        <input id="emp_name_" type="text" name="emp_name" class="form-control" value="<?php echo $emp_name; ?>">
                        <span id="emp_name__" class="help-block"><?php echo $emp_name_err; ?></span>
                    </div> 

                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Password</label>
                        <input id="pwd_" type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span id="pwd__" class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <label>Confirm Password</label>
                        <input id="pwd2_" type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                        <span id="pwd2__" class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-default" value="Reset">
                    </div>

                    <p>Already have an account? <a href="emp-login.php">Employee Login here</a>.</p>
                </form>
            </div>    
        </div>  
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"> </script>
    <script>
        
    var elements = document.getElementsByClassName("form-control");

    for (var i=0; i<elements.length; i++) {
        elements[i].addEventListener("input", function(e){
            if (e.target.value == ""){
                document.getElementById(e.target.id).className  = "form-control border border-danger";
                $('#' + e.target.id + '_').html("") ;
                $('#' + e.target.id + '_').show() ;
            }
            else{
                document.getElementById(e.target.id).className  = "form-control";
                $('#' + e.target.id + '_').hide() ;
            }                
        });
    }

  </script>
         
    <footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA. </a>
			All Rights Reserved
		</div>
	</footer> 
</body>
</html>



