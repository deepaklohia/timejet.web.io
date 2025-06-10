<?php
//$conn = mysqli_connect("localhost", "root", "", "testing");
require_once "config.php";

if(isset($_POST["id"]))
{
 $value = mysqli_real_escape_string($conn, $_POST["value"]);
 $query = "UPDATE tj_activity_list SET ".$_POST["column_name"]."='".$value."' WHERE id = '".$_POST["id"]."'";
 if(mysqli_query($conn, $query))
 {
  echo 'Activity Updated';
 }
}
?>




