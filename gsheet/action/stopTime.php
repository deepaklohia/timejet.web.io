<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;
$sub_day_left = 0 ;

if($_SERVER["REQUEST_METHOD"] == "POST"){
        
    $mgr_id =  trim($_POST["mgrEmail"]) ; 
    $login_date = trim($_POST["loginDate"]) ; 
    $emp_id = trim($_POST["empEmail"]) ; 
    $emp_name = trim($_POST["empName"]) ; 
    $activity = trim($_POST["activity"]) ; 
    $caseRef = trim($_POST["caseRef"]) ; 
    $comments = trim($_POST["userComments"]) ;                           
    $start_time = trim($_POST["startTime"]) ; 
    $end_time = trim($_POST["stopTime"]) ;  
    $total_time = trim($_POST["totalTime"]) ; 
 
    if ($mgr_id != "") {
        //subscription check
        $sql = "SELECT DATEDIFF(subscription_end_date , CURRENT_DATE) AS sub_stat FROM tj_login where user_type = 'mgr' and mgr_id = ? ";
        $stmt = mysqli_prepare($conn, $sql) ;
        mysqli_stmt_bind_param($stmt, "s", $mgr_id);
        mysqli_stmt_execute($stmt) ;
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $sub_stat);
        mysqli_stmt_fetch($stmt);
        $sub_day_left = $sub_stat ;
        mysqli_stmt_free_result($stmt);
        
        #mysqli_stmt_close($stmt);
        #mysqli_close($conn);
        
        if ($sub_day_left <= 0) { 
            $errorStatus = "SubscriptionExpired" ;
        }
        else{
            $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, emp_name, activity, case_ref, comments, 
            start_time, end_time, total_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssssssss", 
                $mgr_id, $login_date, $emp_id, $emp_name, $activity, $caseRef, $comments, $start_time, $end_time, $total_time );

                if(mysqli_stmt_execute($stmt)){
                //operation successful
                } else{
                    $errorStatus = "error inserting data, try logging in and loggin out" ; 
                }

                if ($sub_day_left <= 7) { 
                    $errorStatus = "success:SubscriptionWarning:Expiring in ##".$sub_day_left."## Days" ;
                }
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}

if (empty($errorStatus)){
    echo "success:Data inserted";
}
else{
    echo "error:".$errorStatus ;
}
?>