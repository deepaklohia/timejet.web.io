<?php
require_once "config.php";
$emp_id =  $_POST["emp_id"] ;
$mgr_id =  $_POST["mgr_id"] ;
$activity =  $_POST["activity"] ;
$sinceTime =  $_POST["sinceTime"] ;
$sql = "UPDATE tj_login SET activity = ?, since_time = ? WHERE mgr_id = ? AND emp_id = ? AND user_type= 'emp' ";
if ($stmt = $conn->prepare($sql)) {
    $stmt -> bind_param("ssss", $activity, $sinceTime, $mgr_id, $emp_id);
    //$result = mysqli_stmt_execute($conn, $sql) ;
    $stmt-> execute();
}
$stmt -> close();
$conn -> close();
?>




