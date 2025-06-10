<?php
header("Access-Control-Allow-Origin: *");
//header('Access-Control-Allow-Credentials: true');
require_once "config.php";

// Initialize the session
session_start();

$errorStatus = "" ;
$mgr_id_err = "" ;
$loginData = array();
$emp_id =  $mgr_id = "";
$empPwd = "" ;
$emp_id_err = $empPwd_err = $mgr_id_err = "" ; 

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $loginDate = trim($_POST["loginDate"]);
    $loginTime = trim($_POST["loginTime"]);
    $mgr_id = trim($_POST["mgrEmail"]);
    $emp_id = trim($_POST["empEmail"]);
    $emp_name = trim($_POST["empName"]);
    $recordRef = trim($_POST["recordRef"]);
    $timeStamp = trim($_POST["timeStamp"]);
    $activity = "LOGGED OUT" ;

    //UPDATE LIVE DATABASE 
    $sql = "UPDATE tj_login SET emp_loginlogout_dttm = ?, since_time = ?, activity = ? 
    WHERE mgr_id = ? AND emp_id = ? AND user_type= 'emp' ";
    $stmt = mysqli_prepare($conn, $sql) ;
    mysqli_stmt_bind_param($stmt, "sssss", $timeStamp, $loginTime, $activity, $mgr_id, $emp_id);   
    mysqli_stmt_execute($stmt) ;

    //CHECK IF LOGOUT ENTRY EXISTS
    $sql = "SELECT id FROM tj_empdata WHERE emp_id = ? AND mgr_id = ? AND record_ref = ? ";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "sss", $emp_id, $mgr_id, $recordRef);
        mysqli_stmt_execute($stmt) ;
        mysqli_stmt_store_result($stmt);
        
        //DELETE IF RECORD EXISTS
        if(mysqli_stmt_num_rows($stmt) > 0){
            $sql = "DELETE FROM tj_empdata WHERE mgr_id = ? AND emp_id = ? AND record_ref = ? ";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "sss", $mgr_id , $emp_id, $recordRef);
                mysqli_stmt_execute($stmt);
            }
        }

        $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, emp_name, activity, end_time, record_ref ) VALUES (?, ?, ?, ?,'LOGOUT',?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssssss", $mgr_id, $loginDate, $emp_id , $emp_name , $loginTime, $recordRef);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);

    }

    mysqli_close($conn);
    // Unset all of the session variables
    $_SESSION = array();
    // Destroy the session.
    session_destroy();
}

if (empty($errorStatus)){
    echo "success:Logout Successful";
}
else{
    echo "error:".$errorStatus ;
}

?>