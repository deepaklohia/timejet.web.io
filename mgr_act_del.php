
<?php
require_once "config.php";

if(isset($_POST["id"]))
{
 $query = "DELETE FROM tj_activity_list WHERE id = '".$_POST["id"]."'";
 if(mysqli_query($conn, $query))
 {
  echo 'Activity Deleted';
 }
}
//mysqli_stmt_close($stmt);
mysqli_close($conn);

?>

