<?php
// Initialize the session
session_start();
 
//checking if the user is pro
if(isset($_SESSION["tjproUser"]) && $_SESSION["tjproUser"] === true){
    $tjproUser = $_SESSION["tjproUser"] ;
}

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["emploggedin"]) && $_SESSION["emploggedin"] === true){
  header("location: emp-dash.php");
  exit;
}
if(isset($_GET["status"]) && $_GET["status"] == 200){
    $status = "<font style=color:green;>Signup Success! </font> Please Login." ;
}elseif(isset($_GET["status"]) && $_GET["status"] == 201){
    $status = "<font style=color:green;>Password Changed! </font> Please Login." ;
}elseif(isset($_GET["status"]) && $_GET["status"] == 202){
    $status = "<font style=color:green;>Successfully Logged-out ! </font> Login again." ;
}else{
    $status = "" ;
    //$status = "Please fill in your credentials to login." ;
}

require_once "config.php";
 
// Define variables and initialize with empty values
$emp_id = $password = $mgr_id = "";
$emp_id_err = $password_err = $mgr_id_err ="" ; 
$sub_day_left = 0 ;

date_default_timezone_set('Asia/Colombo');

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    $mgr_id = trim($_POST["mgr_id"]);
    $emp_id = trim($_POST["emp_id"]);
    $password = trim($_POST["password"]);
    $activity = "LOGGED IN" ;
    
    // Check if Manager username is empty
    if(empty($mgr_id)){
        $mgr_id_err = "Please enter manager email.";
    }
    
    //validate manager email
    if(empty($mgr_id_err)){
        // Prepare a select statement
        $sql = "SELECT id, account_status FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr' " ;
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_mgr_id);
            
            // Set parameters
            $param_mgr_id = $mgr_id;
            
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                    //check if managar email is found
                    if (mysqli_stmt_num_rows($stmt) == 0){
                        $mgr_id_err = "manager email not found. use <a href='mgr-register.php'>Manager Signup </a> first.";
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
    
    // Check if username is empty
    if(empty($mgr_id_err) && empty($emp_id)){
        $emp_id_err = "Please enter employee email.";
    } 

    if(empty($mgr_id_err) && empty($emp_id_err)){
 
        $sql = "SELECT id, account_status FROM tj_login WHERE mgr_id = ? AND emp_id = ? AND user_type = 'emp' ";
                            
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $param_mgr_id , $param_emp_id);
            $param_mgr_id = $mgr_id ; 
            $param_emp_id = $emp_id;

                if(mysqli_stmt_execute($stmt)){
                    
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 0 ){                    
                        $emp_id_err = "employee email not found .use <a href='emp-register.php'>Employee Signup </a> first";
                        }

                    else{
                        //GET ACCOUNT STATUS
                        mysqli_stmt_bind_result($stmt, $id, $account_status);
                        mysqli_stmt_fetch($stmt);
                        if ($account_status == 0){
                            $emp_id_err = "account deactivated. contact admin";
                        }
                        mysqli_stmt_free_result($stmt);
                    }

                }
                else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
        mysqli_stmt_close($stmt);
        }
    }
    
    // Check if password is empty
    if(empty($mgr_id_err) && empty($emp_id_err) && empty($password)){
        $password_err = "Please enter employee password.";
    }

    //VALIDATE PASSWORD
    if(empty($mgr_id_err) && empty($emp_id_err) && empty($password_err)){
        $sql = "SELECT id, mgr_id, emp_id, emp_name, emp_pwd FROM tj_login WHERE user_type = 'emp' AND mgr_id = ? AND emp_id = ? ";

        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $param_mgr_id , $param_emp_id);
            $param_mgr_id = $mgr_id ; 
            $param_emp_id = $emp_id;
            mysqli_stmt_execute($stmt) ;
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $id, $mgr_id, $emp_id, $emp_name, $hashed_password);

            if(mysqli_stmt_fetch($stmt)){
                
                $emp_name_ =  $emp_name ;

                if(password_verify($password, $hashed_password)){

                    //save memory
                    mysqli_stmt_free_result($stmt);

                    $curDTTM = strtotime(date('Y-m-d h:i:sa') );
                    $loginDate =date("d-M-Y", $curDTTM) ;
                    $loginTime = date("h:i:s", $curDTTM)  ; 
                    $recordRef = date("dMY", $curDTTM) . "LOGIN" ;
                    $timeStamp = date("d-M-Y h:i:sa", $curDTTM) ;

                    //LOGIN TRIGGER ON TJ_LOGIN
                    /*
                    $sql = "UPDATE tj_login SET emp_loginlogout_dttm = '$timeStamp', since_time = '$loginTime', activity='LOGGED IN' 
                    WHERE mgr_id = '$mgr_id' AND emp_id = '$emp_id' AND user_type= 'emp' ";
                    mysqli_query($conn, $sql)  ;
                    */
                    $sql = "UPDATE tj_login SET emp_loginlogout_dttm = ?, since_time = ?, activity = ? 
                    WHERE mgr_id = ? AND emp_id = ? AND user_type= 'emp' ";
                    $stmt = mysqli_prepare($conn, $sql) ;
                    mysqli_stmt_bind_param($stmt, "sssss", $timeStamp, $loginTime, $activity, $mgr_id, $emp_id);
                    mysqli_stmt_execute($stmt) ;

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


                    //LOGGIN ENTRY IN TJ_EMPDATA
                    $sql = "SELECT id FROM tj_empdata WHERE emp_id = ? AND mgr_id = ? AND record_ref = ? ";
                    if($stmt = mysqli_prepare($conn, $sql)){
                        mysqli_stmt_bind_param($stmt, "sss", $emp_id, $mgr_id, $recordRef);
                        /*
                        $param_emp_id = $emp_id;
                        $param_mgr_id = $mgr_id ; 
                        $param_record_ref = $recordRef ;            
                        */
                        mysqli_stmt_execute($stmt) ;
                        mysqli_stmt_store_result($stmt);

                        if(mysqli_stmt_num_rows($stmt) == 0){
                            $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, activity, start_time, record_ref ) VALUES (?, ?, ?,'LOGIN',?, ?)";
            
                            if($stmt = mysqli_prepare($conn, $sql)){
                                mysqli_stmt_bind_param($stmt, "sssss", $mgr_id, $loginDate, $emp_id , $loginTime, $recordRef);
                                /*
                                $param_mgr_id = $mgr_id ; 
                                $param_loginDate = $loginDate ;
                                $param_emp_id = $emp_id ;
                                $param_loginTime = $loginTime ;
                                $param_record_ref = $recordRef ;
                                */
                                mysqli_stmt_execute($stmt);
                            }
                        }
                        mysqli_stmt_free_result($stmt);
                        mysqli_stmt_close($stmt);
                    }

                    // Password is correct, so start a new session
                    session_start();

                    $_SESSION["emploggedin"] = true;
                    //$_SESSION["subscription_status"] = false;
                    $_SESSION["id"] = $id;
                    $_SESSION["emp_id"] = $emp_id;  
                    $_SESSION["emp_name"] = $emp_name_;  
                    $_SESSION["mgr_id"] = $mgr_id;
                    $_SESSION["loginDate"] = $loginDate ; 
                    $_SESSION["loginTime"] =  $loginTime  ; 
                    $_SESSION["sub_day_left"] = $sub_day_left;  
                    if ($sub_day_left > 7) {  $_SESSION["tjproUser"] = true;  } else{ $_SESSION["tjproUser"] = false;  }
                
                    // Redirect user to welcome page
                    header("location: emp-dash.php");
                } else{
                    // Display an error message if password is not valid
                    $password_err = "The password you entered was not valid.";
                }
            }  
        }
    }
        
mysqli_close($conn);
}
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>TimeJet | Employee Login</title>
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
                <h5>Employee Login</h5>
                <p>
                <?php echo $status; ?>
                </p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
                    <div class="form-group <?php echo (!empty($mgr_id_err)) ? 'has-error' : ''; ?>">
                        <label>Manager Email</label>
                        <input type="email" name="mgr_id" id="mgr_id_"  class="form-control" value="<?php echo $mgr_id; ?>">
                        <span id ="mgr_id__" class="help-block"><?php echo $mgr_id_err; ?></span>
                    </div>  
                    
                    <div class="form-group <?php echo (!empty($emp_id_err)) ? 'has-error' : ''; ?>">
                        <label>Employee Email</label>
                        <input type="email" name="emp_id" id="emp_id_"  class="form-control" value="<?php echo $emp_id; ?>">
                        <span id = "emp_id__" class="help-block"><?php echo $emp_id_err; ?></span>
                    </div>    
                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <label>Employee Password</label>
                        <input type="password" name="password" id="password_" class="form-control">
                        <span id = "password__" class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Login">
                    </div>
                    <p>
                    <a href="emp-register.php">Employee Sign-up</a>. 
                        &nbsp;|&nbsp;
                    <a href="emp-forgot-pwd.php">Forgot Password</a>.
                    </p>
                </form>
            </div>    
        </div>
    </div>         

    <script>
    var elements = document.getElementsByClassName("form-control");

    for (var i=0; i<elements.length; i++) {
        elements[i].addEventListener("input", function(e){
            //document.getElementsByClassName("help-block").;
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

</body>
</html>




