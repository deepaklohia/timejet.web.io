<?php
// Initialize the session
session_start();
 
//checking if the user is pro
if(isset($_SESSION["tjproUser"]) && $_SESSION["tjproUser"] === true){
    $tjproUser = $_SESSION["tjproUser"] ;
}

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["mgrloggedin"]) && $_SESSION["mgrloggedin"] === true){
  header("location: mgr-dash.php");
  exit;
}
if(isset($_GET["status"]) && $_GET["status"] == 200){
    $status = "<font style=color:green;>Signup Success! </font> Please Login." ;
}elseif(isset($_GET["status"]) && $_GET["status"] == 201){
    $status = "<font style=color:green;>Password Changed successfully! </font> Login." ;
}elseif(isset($_GET["status"]) && $_GET["status"] == 202){
    $status = "<font style=color:green;>Successfully Logged-out ! </font> Login again." ;
}else{
    $status = "" ;
    //$status = "Please fill in your credentials to login." ;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$mgr_id = $password = "";
$mgr_id_err = $password_err = "";
$sub_day_left = 0 ;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    $mgr_id = trim($_POST["mgr_id"]);
    $password = trim($_POST["password"]);

    // Check if username is empty
    if(empty($mgr_id)){
        $mgr_id_err = "Please enter username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id, account_status FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr'";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_mgr_id);
            
            // Set parameters
            $param_mgr_id = $mgr_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 0){
                    // Display an error message if username doesn't exist
                    $mgr_id_err = "email not found.";
                }
                else{
                    //GET ACCOUNT STATUS
                    mysqli_stmt_bind_result($stmt, $id, $account_status);
                    mysqli_stmt_fetch($stmt);
                    if ($account_status == 0){
                        $mgr_id_err = "account deactivated. contact admin";
                    }
                    mysqli_stmt_free_result($stmt);
                }

            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if(empty($mgr_id_err) && empty($password)){
        $password_err = "Please enter your password.";
    } 
    // Validate credentials
    if(empty($mgr_id_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, mgr_id, mgr_name, mgr_pwd FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr'";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_mgr_id);

            $param_mgr_id = $mgr_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) >= 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $mgr_id, $mgr_name, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){

                        $mgr_name_ =  $mgr_name ;

                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session

                             //save memory
                            mysqli_stmt_free_result($stmt);

                            //GETTING SUBSCRIPTION STATUS
                            $sql = "SELECT DATEDIFF(subscription_end_date , CURRENT_DATE) AS sub_stat FROM tj_login where user_type = 'mgr' and mgr_id = ? ";
                            $stmt = mysqli_prepare($conn, $sql) ;
                            mysqli_stmt_bind_param($stmt, "s", $mgr_id);
                            mysqli_stmt_execute($stmt) ;
                            mysqli_stmt_store_result($stmt);
                            mysqli_stmt_bind_result($stmt, $sub_stat);
                            mysqli_stmt_fetch($stmt);
                            $sub_day_left = $sub_stat ;
                            mysqli_stmt_free_result($stmt);
                            
                            session_start();
                            // Store data in session variables
                            $_SESSION["mgrloggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["mgr_id"] = $mgr_id;  
                            $_SESSION["mgr_name"] = $mgr_name_;
                            $_SESSION["sub_day_left"] = $sub_day_left;
                            if ($sub_day_left > 7) {  $_SESSION["tjproUser"] = true;  } else{ $_SESSION["tjproUser"] = false;  }
                            
                            // Redirect user to welcome page
                            header("location: mgr-dash.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "invalid password";
                        }
                    }
                }  
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
	<title>TimeJet | Manager Login</title>
    <?php include('header.php'); ?>
    <?php if ($tjproUser != true){include('ad.php');  }?>
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
                <a class="nav-item nav-link" href="emp-login.php">Employee Login</a>
                <a class="nav-item nav-link active" href="mgr-login.php">Manager Login</a>
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
                <h5>Manager Login</h5>
                <p>
                <?php echo $status; ?>
                </p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($mgr_id_err)) ? 'has-error' : ''; ?>">
                        <label>Manager Email</label>
                        <input type="email" name="mgr_id" id="mgr_id_"  class="form-control" value="<?php echo $mgr_id; ?>">
                        <span id="mgr_id__" class="help-block"><?php echo $mgr_id_err; ?></span>
                    </div>    
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Password</label>
                        <input type="password" name="password" id="password_" class="form-control">
                        <span id="password__" class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Login">
                    </div>
                    <p><a href="mgr-register.php">Manager Sign-up</a>. 
                    &nbsp;|&nbsp;
                    <a href="mgr-forgot-pwd.php">Forgot Password</a>.</p>
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







