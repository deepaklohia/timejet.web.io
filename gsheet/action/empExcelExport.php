<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;
$result = "" ;
$empData = array();
 
$emp_id = $mgr_id = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mgr_id = trim($_POST["mgrEmail"]);
    $emp_id = trim($_POST["empEmail"]);

    // Check if Manager username is empty
    if(empty($mgr_id) || empty($emp_id) ){
        $errorStatus = "session lost. re-login.";
    }
    else
    {
        $sql = "SELECT id, login_date, emp_id, emp_name, activity, case_ref, comments, start_time, end_time, total_time FROM tj_empdata where mgr_id = ? AND emp_id = ? " ;

        if ($stmt = $conn->prepare($sql)) {

            $stmt->bind_param("ss", $mgr_id, $emp_id);
            $stmt-> execute();
            $result = $stmt->get_result();

            //$stmt->bind_result($id, $login_date, $emp_id, $activity, $case_ref, $comments, $start_time, $end_time, $total_time);
            //$stmt->fetch();
            //$records->mysqli_query()
            //$records = mysqli_query($conn, $sql);
            //if(mysqli_num_rows($records) > 0)
            if(mysqli_num_rows($result) > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $empData[] = [
                    'id' => $row['id'] , 
                    'login_date' => $row["login_date"] ,
                    'emp_id' => $row["emp_id"] ,
					'emp_name' => $row["emp_name"] ,
                    'activity' => $row["activity"] ,
                    'case_ref' => $row["case_ref"] ,
                    'comments' => $row["comments"] ,
                    'start_time' => $row["start_time"] ,
                    'end_time' => $row["end_time"] ,
                    'total_time' => $row["total_time"] 
                    ];
                }
            }
            $stmt->close();
            $conn->close();
            //mysqli_close($conn);
        }
      
    }
}

//$errorStatus  = $result; 
//echo "error status:" +$errorStatus."\n";

if (empty($errorStatus)){
    echo json_encode(array("success:"=>$empData));
}
else{
    echo "error:".$errorStatus ;
}
?>