<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;
$newRecord = array();

if($_SERVER["REQUEST_METHOD"] == "POST"){
        
    $mgr_id =  trim($_POST["mgrEmail"]) ; 
    $login_date = trim($_POST["loginDate"]) ; 
    $emp_id = trim($_POST["empEmail"]) ; 
    $emp_name = trim($_POST["empName"]) ; 
    $activity = trim($_POST["activity"]) ; 
    $caseRef = trim($_POST["caseRef"]) ; 
    $comments = trim($_POST["userComments"]) ;                           
    $start_time = trim($_POST["startTime"]) ; 
    $end_time = "" ;  
    $total_time = ""; 

    /*
    $_SESSION["tempStartTime"] = $startTime  ;
    $_SESSION["tempStopTime"] = $stopTime  ;
    $_SESSION["tempTotalTime"] = $totalTime  ;
    */

    if ($mgr_id != "") {
        $sql = "INSERT INTO tj_empdata (mgr_id, login_date, emp_id, emp_name, activity, case_ref, comments, 
                                                start_time, end_time, total_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt -> bind_param("sss", $sinceTime, $mgr_id, $emp_id);
            $stmt-> execute();

            $stmt->bind_result($mgr_id, $login_date, $emp_id, $emp_name, $activity, $caseRef, $comments, $start_time, $end_time, $total_time);

            if ($conn->query($stmt) === TRUE) {
                $last_id = $conn->insert_id;
                echo "New record created successfully. Last inserted ID is: " . $last_id;
                } 
            else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
                                
            $stmt->close();
            $conn->close();


            $newRecord[] = [
                'id' => "1234" , 
                ];

        }
    }
        
      

/*
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            
            mysqli_stmt_bind_param($stmt, "ssssssssss", 
            $mgr_id, $login_date, $emp_id, $emp_name, $activity, $caseRef, $comments, $start_time, $end_time, $total_time );
            
            if(mysqli_stmt_execute($stmt)){
                //operation successful
                //session_destroy();
                //exit();
            } else{
                $errorStatus = "error inserting data, try logging in and loggin out" ; 
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
*/
}

if (empty($errorStatus)){
    echo json_encode(array("success:"=>$newRecord));
}
else{
    echo "error:".$errorStatus ;
}
?>




