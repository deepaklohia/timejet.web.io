<?php  

include_once 'config.php' ;

if(isset($_POST["export"])){

     session_start() ;
     $mgr_id =  $_SESSION["mgr_id"] ;       

     $fileName = "mgr_export_data-" . date('Ymd') . ".xls"; 
     $result = '';

     $sql = "SELECT ID, login_date as 'Login Date', emp_id AS 'Emp Id', activity AS 'Activity', case_ref AS 'Case Ref#', comments AS 'Comments', 
     start_time AS 'Start Time', end_time AS 'Stop Time', total_time AS 'Total Time' FROM tj_empdata where mgr_id = ? " ;
     if ($stmt = $conn->prepare($sql)) {
          $stmt -> bind_param("s", $mgr_id);
          $stmt-> execute();
          $records = $stmt->get_result();
     }

     header("Content-Type: application/vnd.ms-excel");
     header("Content-Disposition: attachment; filename=\"$fileName\"");

     //if(mysqli_num_rows($records) > 0){
     if(!empty($records)){
          $heading = false;
          //if(!empty($records))
          foreach($records as $row) {
               if (!empty($row)){
                    if(!$heading) {
                    // display field/column names as a first row
                         echo implode("\t", array_keys($row)) . "\n";
                         $heading = true;
                    }
                    echo implode("\t", array_values($row)) . "\n";
               }
          }
     }
     else{
          echo ('NO RECORDS FOUND');
     }

     $stmt->close();    
     $conn->close();    
}
?>

