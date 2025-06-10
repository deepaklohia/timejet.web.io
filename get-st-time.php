<?php
  date_default_timezone_set('Asia/Colombo');
  $curDTTM = strtotime(date('Y-m-d h:i:sa') );
  /*
  $loginDate =date("d-M-Y", $curDTTM) ;
  $loginTime = date("h:i:s", $curDTTM)  ; 
  $recordRef = date("dMY", $curDTTM) . "LOGIN" ;
  $timeStamp = date("d-M-Y h:i:sa", $curDTTM) ;
  $curDTTM = strtotime(date('Y-m-d h:i:sa') );
  */
  $startTime = date("h:i:s", $curDTTM)  ; 
  echo $startTime;
?>


