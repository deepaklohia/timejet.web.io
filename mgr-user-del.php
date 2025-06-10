<?php
require_once "config.php";
$record_id =  $_POST["del_id"] ;
$sql = "DELETE FROM tj_login WHERE id = ? AND user_type ='emp' " ;

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $record_id);
    //$result = mysqli_stmt_execute($conn, $sql) ;
    $stmt-> execute();
}
$stmt -> close();
$conn -> close();
?>




