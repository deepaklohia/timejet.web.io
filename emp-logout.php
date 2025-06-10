<?php

include_once ('config.php') ;

// Initialize the session
session_start();

date_default_timezone_set('Asia/Colombo');

$emp_id = $_SESSION["emp_id"] ;
$mgr_id =  $_SESSION["mgr_id"] ;
$activity = "LOGGED OUT" ;
$sinceTime = date('h:i:s') ;

$curDTTM = strtotime(date('Y-m-d h:i:sa') );
$loginDate =date("d-M-Y", $curDTTM) ;
$loginTime = date("h:i:s", $curDTTM)  ; 
$recordRef = date("dMY", $curDTTM) . "LOGOUT" ;

//UPDATE LIVE DATABASE
/*
$sql = "UPDATE tj_login SET activity = '$activity', since_time = '$sinceTime' WHERE mgr_id = '$mgr_id' AND emp_id = '$emp_id' AND user_type= 'emp' ";
mysqli_query($conn, $sql) ;
*/
$sql = "UPDATE tj_login SET emp_loginlogout_dttm = ?, since_time = ?, activity = ? 
WHERE mgr_id = ? AND emp_id = ? AND user_type= 'emp' ";
$stmt = mysqli_prepare($conn, $sql) ;
mysqli_stmt_bind_param($stmt, "sssss", $sinceTime, $loginTime, $activity, $mgr_id, $emp_id);   
mysqli_stmt_execute($stmt) ;

//LOGGOUT OUT ENTRY IN DB
$sql = "SELECT id FROM tj_empdata WHERE emp_id = ? AND mgr_id = ? AND record_ref = ? ";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_mgrusername, $param_record_ref);
    $param_username = $emp_id;
    $param_mgrusername = $mgr_id ; 
    $param_record_ref = $recordRef ;            
    mysqli_stmt_execute($stmt) ;
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0){
        //DELETE LAST LOGOUT ENTRY AND UPDATE LATEST
        $sql = "DELETE FROM tj_empdata WHERE mgr_id = ? AND emp_id = ? AND record_ref = ? ";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_mgrusername , $param_username, $param_record_ref);

            $param_mgrusername = $mgr_id ; 
            $param_username = $emp_id ;
            $param_record_ref = $recordRef ;
            mysqli_stmt_execute($stmt);
        }
    }

    $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, emp_name, activity, end_time, record_ref ) VALUES (?, ?, ?, ?,'LOGOUT',?, ?)";

    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssss", $param_mgrusername, $param_loginDate, $param_username , $param_empname , $param_loginTime, $param_record_ref);
        $param_mgrusername = $mgr_id ; 
        $param_loginDate = $loginDate ;
        $param_username = $emp_id ;
        $param_empname  = $empname ;
        $param_loginTime = $loginTime ;
        $param_record_ref = $recordRef ;
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();
 
// Redirect to login page
header("location: emp-login.php?status=202");
exit;
?>



