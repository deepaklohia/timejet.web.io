<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;
$result = "" ;
$empLoginData = array();

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mgr_id = trim($_POST["mgr_id"]);

    // Check if Manager username is empty
    if(empty($mgr_id) ){
        $errorStatus = "session lost. re-login.";
    }
    else
    {
        $sql = "SELECT * FROM tj_login where mgr_id = ? AND user_type ='emp' ";

        if ($stmt = $conn->prepare($sql)) {

            $stmt->bind_param("s", $mgr_id);
            $stmt-> execute();
            $result = $stmt->get_result();
 
            if(mysqli_num_rows($result) > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $empLoginData[] = [
                    'id' => $row['id'] , 
                    'emp_id' => $row["emp_id"] ,
                    'emp_loginlogout_dttm' => $row["emp_loginlogout_dttm"] ,
                    'activity' => $row["activity"] ,
                    'since_time' => $row["since_time"] ,
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
    echo json_encode(array("success:"=>$empLoginData));
}
else{
    echo "error:".$errorStatus ;
}
?>