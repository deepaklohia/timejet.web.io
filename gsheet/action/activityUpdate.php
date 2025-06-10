<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$errorStatus = "" ;

if($_SERVER["REQUEST_METHOD"] == "POST"){
        
    $emp_id =  $_POST["empEmail"] ;
    $mgr_id =  $_POST["mgrEmail"] ;
    $activity =  $_POST["activity"] ;
    $sinceTime =  $_POST["sinceTime"] ;


    $sql = "UPDATE tj_login SET activity = ?, since_time = ? WHERE mgr_id = ? AND emp_id = ? AND user_type= 'emp' ";
    if ($stmt = $conn->prepare($sql)) {
        $stmt -> bind_param("ssss", $activity, $sinceTime, $mgr_id, $emp_id);
        $stmt-> execute();
    }
    else{
        $errorStatus = "some error with query".$sql ;
    }
    $stmt -> close();
    $conn -> close();
}
if (empty($errorStatus)){
    echo "success:";
    //$dta = array("Peter"=>35, "Ben"=>37, "Joe"=>43);
}
else{
    echo "error:".$errorStatus ;
}
?>
 





