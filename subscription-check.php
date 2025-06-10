<?php
require_once "config.php";

session_start() ;
$mgr_id =  $_POST["mgr_id"] ; 
$sub_day_left = 0 ;

if ($mgr_id != "") {
    $sql = "SELECT DATEDIFF(subscription_end_date , CURRENT_DATE) AS sub_stat FROM tj_login where user_type = 'mgr' and mgr_id = ? ";
    $stmt = mysqli_prepare($conn, $sql) ;
    mysqli_stmt_bind_param($stmt, "s", $mgr_id);
    mysqli_stmt_execute($stmt) ;
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $sub_stat);
    mysqli_stmt_fetch($stmt);
    $sub_day_left = $sub_stat ;
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    $_SESSION["sub_day_left"] = $sub_day_left;
    if ($sub_day_left > 7) {  $_SESSION["tjproUser"] = true; } else{ $_SESSION["tjproUser"] = false;   }

    echo $sub_day_left;

    /*
    if ($sub_day_left > 7) { 
      echo "validPro";
    }
    else if ($sub_day_left > 0) { 
        echo "valid";
    } 
    else{ 
      $_SESSION["sub_day_left"] = 0 ;
      echo "expired"; 
    }
    */
}
?>



