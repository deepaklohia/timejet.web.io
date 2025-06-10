
<?php
require_once "config.php";
//$connect = mysqli_connect("localhost", "root", "", "testing");

if(isset($_POST["activity_name"] ))
{
 $mgr_id = $_POST["mgr_id"]  ;
 $activity_name = mysqli_real_escape_string($conn, $_POST["activity_name"]);
 $query = "INSERT INTO tj_activity_list (activity_name , mgr_id) VALUES('$activity_name' , '$mgr_id')";
 if(mysqli_query($conn, $query))
 {
  echo 'Activity Inserted';
 }
}
?>




