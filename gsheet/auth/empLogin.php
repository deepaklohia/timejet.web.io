<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "config.php";

$errorStatus = "" ;
$loginData = array();
$emp_id =  $mgr_id = "";
$empPwd = "" ;
$sub_day_left = 0 ;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $loginDate = trim($_POST["loginDate"]);
    $loginTime = trim($_POST["loginTime"]);
    $mgr_id = trim($_POST["mgrEmail"]);
    $emp_id = trim($_POST["empEmail"]);
    $empPwd = trim($_POST["empPwd"]);
    $recordRef = trim($_POST["recordRef"]);
    $timeStamp = trim($_POST["timeStamp"]);
    $activity = "LOGGED IN" ;

    if (empty($emp_id) || empty($mgr_id) || empty($empPwd) ){
        // Check if username is empty
        if(empty($emp_id)){
            $errorStatus = "Please enter employee email id.";
        }
        // Check if Manager username is empty
        if(empty($mgr_id)){
            $errorStatus = "Please enter manager email id.";
        }
        // Check if password is empty
        if(empty($empPwd)){
            $errorStatus = "Please enter employee password.";
        }
    }

    //validate manager email
    if (empty($errorStatus)){
        // Prepare a select statement
        //$sql = "SELECT id, account_status FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr' " ;
		$sql = "SELECT id, account_status, DATEDIFF(subscription_end_date , CURRENT_DATE) AS sub_day_left FROM tj_login WHERE mgr_id = ? AND user_type = 'mgr' " ;
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $mgr_id);
            
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                //check if managar email is found
                if (mysqli_stmt_num_rows($stmt) == 0){
                    $errorStatus = "manager id: ".$mgr_id." not found. signup for manager!" ;
                }
                else{
                    //GET ACCOUNT STATUS
                    mysqli_stmt_bind_result($stmt, $id, $account_status, $sub_stat);
                    mysqli_stmt_fetch($stmt);
                    if ($account_status == 0){
                        $errorStatus = "account deactivated. contact admin";
                    }
					else{
						$sub_day_left = $sub_stat ;
					}
                    mysqli_stmt_free_result($stmt);
                }

            } else{
                $errorStatus = "error validating manager email. Please try again later.";;
            }
        mysqli_stmt_close($stmt);
        }
    }
    
    //validate emp id
    if (empty($errorStatus)){

        $sql = "SELECT id, account_status FROM tj_login WHERE mgr_id = ? AND emp_id = ? AND user_type = 'emp' ";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $mgr_id , $emp_id);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 0 ){                    
                    $errorStatus = "employee id:".$emp_id." not found. signup for employee";
                }
                else{
                    //GET ACCOUNT STATUS
                    mysqli_stmt_bind_result($stmt, $id, $account_status);
                    mysqli_stmt_fetch($stmt);
                    if ($account_status == 0){
                        $errorStatus = "account deactivated. contact admin";
                    }
                    mysqli_stmt_free_result($stmt);
                }
            }
            else{
                $errorStatus = "error validating emp email. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    //echo ("going to validate password");
    //VALIDATE PASSWORD
    if (empty($errorStatus)){
        $sql = "SELECT id, mgr_id, emp_id, emp_name, emp_pwd FROM tj_login WHERE user_type = 'emp' AND mgr_id = ? AND emp_id = ? ";

        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $mgr_id , $emp_id);
            mysqli_stmt_execute($stmt) ;
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $id, $mgr_id, $emp_id, $emp_name, $hashed_password);

            if(mysqli_stmt_fetch($stmt)){
                //$emp_name_ =  $emp_name ;
                if(password_verify($empPwd, $hashed_password)){
                    //save memory
                    mysqli_stmt_free_result($stmt);
                    
                    //LIVE DATABASE *** LOGIN TRIGGER ON TJ_LOGIN 
                    $sql = "UPDATE tj_login SET emp_loginlogout_dttm = ?, since_time = ?, activity = ? 
                    WHERE mgr_id = ? AND emp_id = ? AND user_type= 'emp' ";
                    $stmt = mysqli_prepare($conn, $sql) ;
                    mysqli_stmt_bind_param($stmt, "sssss", $timeStamp, $loginTime, $activity, $mgr_id, $emp_id);   
                    mysqli_stmt_execute($stmt) ;
					
                    //LOGGIN ENTRY IN TJ_EMPDATA
                    $sql = "SELECT id FROM tj_empdata WHERE emp_id = ? AND mgr_id = ? AND record_ref = ? ";
                    if($stmt = mysqli_prepare($conn, $sql)){
                        mysqli_stmt_bind_param($stmt, "sss", $emp_id, $mgr_id, $recordRef);     
                        mysqli_stmt_execute($stmt) ;
                        mysqli_stmt_store_result($stmt);

                        if(mysqli_stmt_num_rows($stmt) == 0){
                            $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, emp_name, activity, start_time, record_ref ) VALUES (?, ?, ?, ?,'LOGIN',?, ?)";
            
                            if($stmt = mysqli_prepare($conn, $sql)){
                                mysqli_stmt_bind_param($stmt, "ssssss", $mgr_id, $loginDate, $emp_id, $emp_name, $loginTime, $recordRef);
                                mysqli_stmt_execute($stmt);
                            }
                        }
                        mysqli_stmt_free_result($stmt);
                        mysqli_stmt_close($stmt);
                    }
					 
                    $loginData[] = [
                        'emploggedin' => true , 
                        'id' => $id ,
                        'emp_id' => $emp_id,
                        'emp_name' => $emp_name  ,
                        'mgr_id' => $mgr_id ,
                        'loginDate' => $loginDate  ,
                        'loginTime' =>  $loginTime ,
						'sub_day_left' => $sub_day_left  
                        ];
                   
                } else{
                    // Display an error message if password is not valid
                    $errorStatus = "invalid password entered.";
                }
            }
            mysqli_close($conn);
        } 
        else{
            $errorStatus = "error verifying password.";
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