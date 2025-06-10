<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$mgr_id = $password = $confirm_password = $mgr_name = "";
$mgr_id_err = $password_err = $confirm_password_err = $mgr_name_err = "";
$errorStatus = false;
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    $mgr_id = trim($_POST["mgr_id"]);
    $password = trim($_POST["password"]);
    $confirm_password =trim($_POST["confirm_password"]);
    $mgr_name =trim($_POST["mgr_name"]);

    // Validate username
    if(empty($mgr_name)){
        $mgr_name_err = "Please enter a Manager name.";
        $errorStatus = true ;  }  
    // Validate username
    elseif(empty($mgr_id)){
        $mgr_id_err = "Please enter Manager email.";
        $errorStatus = true ; 
    } 

    //now we check manager info
    if( $errorStatus == false ){
        // Prepare a select statement
        $sql = "SELECT id FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr' ";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $mgr_id);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) >= 1){
                    $mgr_id_err = "email already registered.use <a href='mgr-login.php'> manager login </a>.";
                    $errorStatus = true ;
                }
            } else{
                //echo "Oops! Something went wrong. Please try again later.";
                $confirm_password_err = "Oops! Something went wrong. Please try again later.";;
                $errorStatus = true ;
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
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
    if( $errorStatus == false ){
        
        // Prepare an insert statement
        $sql = "INSERT INTO tj_login (mgr_id, mgr_name, mgr_pwd, user_type) VALUES (?,?, ?, 'mgr')";
         
        if($stmt = mysqli_prepare($conn, $sql)){

            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $mgr_id ,$mgr_name, $param_password);
             
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                
                $sql = "INSERT INTO tj_activity_list ( mgr_id, activity_name) VALUES ('$mgr_id','Break')" ;
                mysqli_query($conn , $sql) ;
                $sql = "INSERT INTO tj_activity_list ( mgr_id, activity_name) VALUES ('$mgr_id','Production')" ;
                mysqli_query($conn , $sql) ;
                $sql = "INSERT INTO tj_activity_list ( mgr_id, activity_name) VALUES ('$mgr_id','Non-Production')" ;
                mysqli_query($conn , $sql) ;
                
                //updating default subscription
                $sql = "update tj_login set subscription_end_date = (CURRENT_DATE + 7) where user_type = 'mgr' and mgr_id = '$mgr_id' " ;
                mysqli_query($conn , $sql) ;

                //mysqli_stmt_execute($stmt) ;
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
               

                /*
                #NEW MGR SIGNUP EMAIL
                require 'PHPMailer/PHPMailerAutoload.php';
                
                #************************************* 
                $id = 'notify-email@gmail.com' ;
                $pass = 'vE@bex-bindeq-m8mwo7';
                $to = 'youremail@gmail.com';
                #************************************* 

                $subject = "dlohia | timejet : New Manager Signup" ;
                $mailBody = 'Dear Deepak Lohia,
                <br><br>
                a new manager has signed up using id <a href="mailto:'.$mgr_id.'">'.$mgr_id.'</a>.
                <br><br>
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
                $mail->addReplyTo($mgr_id, 'DLA | TimeJet');
                $mail->addAddress($to);   // To Email 
                $mail->addCC($id);
                //$mail->addBCC('admin@dlohia.com');

                $mail->isHTML(true);  // Set email format to HTML

                $mail->Subject = $subject ;
                $mail->Body = $mailBody;
                
                if(!$mail->send()) {
                    $errorStatus = $mail->ErrorInfo;; 
                    //echo 'Mailer Error: ' . $mail->ErrorInfo;
                }  

                */

                // Redirect to login page
                header("location: mgr-login.php?status=200");
            } else{
                echo "Something went wrong. Please try again later.";
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
	<title>Timejet ~ Welcome </title>
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
                <h5>TimeJet | Manager Sign Up</h5>
                <p>Please fill this form to create an account.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($mgr_name_err)) ? 'has-error' : ''; ?>">
                        <label>Manager Name </label>
                        <input id="mgr_name_" type="text" name="mgr_name" class="form-control" value="<?php echo $mgr_name; ?>">
                        <span id="mgr_name__" class="help-block"><?php echo $mgr_name_err; ?></span>
                    </div>    

                    <div class="form-group <?php echo (!empty($mgr_id_err)) ? 'has-error' : ''; ?>">
                        <label>Manager Email</label>
                        <input id="mgr_id_" type="email" name="mgr_id" class="form-control" value="<?php echo $mgr_id; ?>">
                        <span id="mgr_id__" class="help-block"><?php echo $mgr_id_err; ?></span>
                    </div>    
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Password</label>
                        <input id="password_" type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span id="password__" class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <label>Confirm Password</label>
                        <input id="password2_" type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                        <span id="password2__" class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input id="btnSubmit" type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-default" value="Reset">
                    </div>
                    <p>Already have an account? <a href="mgr-login.php">Manager Login here</a>.</p>
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



