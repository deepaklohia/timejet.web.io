<?php
require_once "config.php";

session_start() ;
$_SESSION["recordStatus"] = true  ;

$mgr_id =  $_POST["mgr_id"] ; 
$login_date = $_POST["login_date"] ; 
$emp_id = $_POST["emp_id"] ; 
$emp_name =$_POST["emp_name"] ; 
$activity =$_POST["activity"] ; 
$case_ref = $_POST["case_ref"] ; 
$comments = $_POST["userComments"] ;                           
$start_time =$_POST["startTime"] ; 
$end_time = $_POST["stopTime"] ;  
$total_time = $_POST["totalTime"] ; 

/*
$_SESSION["tempStartTime"] = $startTime  ;
$_SESSION["tempStopTime"] = $stopTime  ;
$_SESSION["tempTotalTime"] = $totalTime  ;
*/

if ($mgr_id != "") {

    $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, emp_name, activity, case_ref, comments, 
                                            start_time, end_time, total_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        
        mysqli_stmt_bind_param($stmt, "ssssssssss", 
        $mgr_id, $login_date, $emp_id, $emp_name, $activity, $case_ref, $comments, $start_time, $end_time, $total_time );
        
        if(mysqli_stmt_execute($stmt)){
            //operation successful
            //session_destroy();
            //exit();
        } else{
            echo("Error description: " . mysqli_error($conn));
            //echo "<script>alert ('error inserting data, try logging in and loggin out'); </script>" ;
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>



