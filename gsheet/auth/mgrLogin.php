<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "config.php";

$errorStatus = "" ;
$mgr_id = "" ;
$mgrPwd = "" ;
$loginData = array();
$sub_day_left = 0 ;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $mgr_id = trim($_POST["mgrEmail"]);
    $mgrPwd = trim($_POST["mgrPwd"]);
    
    if (empty($mgr_id) ||   empty($mgrPwd) ){
        // Check if Manager username is empty
        if(empty($mgr_id)){
            $errorStatus = "Please enter manager email id.";
        }
        // Check if password is empty
        if(empty($mgrPwd)){
            $errorStatus = "Please enter manager password.";
        }
    }

    //validate manager email
    if (empty($errorStatus)){
        // Prepare a select statement + subscription check
        $sql = "SELECT id, mgr_id, mgr_name, mgr_pwd, account_status, DATEDIFF(subscription_end_date , CURRENT_DATE) AS sub_stat FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr'";
        //$sql = "SELECT id, mgr_id, mgr_name, mgr_pwd, account_status FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr'";
        
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $mgr_id);
            
            // Set parameters
            
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                //check if managar email is found
                if (mysqli_stmt_num_rows($stmt) == 0){
                    $errorStatus = "manager id: ".$mgr_id." not found. signup for manager!" ;
                }
                else{
                    //GET ACCOUNT STATUS
                    //mysqli_stmt_bind_result($stmt, $id, $account_status);
                    mysqli_stmt_bind_result($stmt, $id, $mgr_id, $mgr_name, $hashed_password, $account_status, $sub_stat);

                    mysqli_stmt_fetch($stmt);
                    if ($account_status == 0){
                        $errorStatus = "account deactivated. contact admin";
                    }
                    //try to login
                    else{

                        mysqli_stmt_store_result($stmt);

                        if(password_verify($mgrPwd, $hashed_password)){
                            // Password is correct, so start a new session
                             //save memory
                            //mysqli_stmt_free_result($stmt);

                            $loginData[] = [
                                'mgrloggedin' => true , 
                                'id' => $id ,
                                'mgr_id' => $mgr_id,
                                'mgr_name' => $mgr_name  ,
                                'sub_day_left' => $sub_stat  ,
                            ];

                            
                            // Redirect user to welcome page
                        } else{
                            // Display an error message if password is not valid
                            $errorStatus =  "invalid password";
                        }
                    }

                    mysqli_stmt_free_result($stmt);
                }

            } else{
                $errorStatus = "error validating manager email. Please try again later.";;
            }
        mysqli_stmt_close($stmt);
        }
    }
}

if (empty($errorStatus)){
    echo json_encode(array("success:"=>$loginData));
}
else{
    echo "error:".$errorStatus ;
}
?>