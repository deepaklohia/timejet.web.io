<?php
// Initialize the session
session_start();
 
// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();

/*
if(!empty($_SESSION['uname'])){
    session_destroy();
}
*/
// Redirect to login page
header("location: mgr-login.php?status=202");
exit;
?>



