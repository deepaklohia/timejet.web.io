
<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

if(isset($_POST["id"]))
{
 $query = "DELETE FROM tj_activity_list WHERE id = '".$_POST["id"]."'";
 if(mysqli_query($conn, $query))
 {
  echo 'Activity Deleted';
 }
}
?>




