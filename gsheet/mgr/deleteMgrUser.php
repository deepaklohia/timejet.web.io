<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$record_id =  $_POST["del_id"] ;
$sql = "DELETE FROM tj_login WHERE id = '$record_id' AND user_type ='emp' " ;
$result= mysqli_query($conn, $sql) ;
?>
 





