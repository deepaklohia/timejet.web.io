<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;
$result = "" ;
$mgrData = array();
$mgr_id = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mgr_id = trim($_POST["mgrEmail"]);

    if(empty($mgr_id) ){
        $errorStatus = "session lost. re-login.";
    }
    else
    {
        $sql = "SELECT id, login_date, emp_id, activity, case_ref, comments, start_time, end_time, total_time FROM tj_empdata where mgr_id = ? " ;  

        if ($stmt = $conn->prepare($sql)) {

            $stmt->bind_param("s", $mgr_id);
            $stmt-> execute();
            $result = $stmt->get_result();

            if(mysqli_num_rows($result) > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $mgrData[] = [
                    'id' => $row['id'] , 
                    'login_date' => $row["login_date"] ,
                    'emp_id' => $row["emp_id"] ,
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
    echo json_encode(array("success:"=>$mgrData));
}
else{
    echo "error:".$errorStatus ;
}
?>