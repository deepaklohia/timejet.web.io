<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;
$mgr_id = "";
$activityData = array() ;

if($_SERVER["REQUEST_METHOD"] == "POST"){
   $mgr_id = trim($_POST["mgrEmail"]);
  
    $sql = "SELECT DISTINCT * FROM tj_activity_list WHERE mgr_id = ? ";

    if ($stmt = $conn->prepare($sql)) {

        $stmt->bind_param("s", $mgr_id);
        $stmt-> execute();
        $result = $stmt->get_result();

        if(mysqli_num_rows($result) > 0)
        {
            while($row = $result->fetch_assoc())
            {
                $activityData[] = [
                'activity_name' => $row['activity_name'] , 
                ];
            }
        }
        $stmt->close();
        $conn->close();
        //mysqli_close($conn);
    }
    else{
        $errorStatus = "unable to fetch activity list" ;
    }
}

if (empty($errorStatus)){
    echo json_encode(array("success:"=>$activityData));
}
else{
    echo "error:".$errorStatus ;
}
?>





