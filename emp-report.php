<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["emploggedin"]) || $_SESSION["emploggedin"] !== true){
    header("location: emp-login.php");
    exit;
}

require_once "config.php";

$emp_id = $_SESSION["emp_id"] ;
$mgr_id =  $_SESSION["mgr_id"] ;
$loginDate = $_SESSION["loginDate"] ;
$loginTime =  $_SESSION["loginTime"] ;
$sub_day_left =  $_SESSION["sub_day_left"] ;
$tjproUser = $_SESSION["tjproUser"] ;

//$exportURL  = 'emp-excel-export.php' . '?empid='. $emp_id  . '&mgrid='. $mgr_id ;
$exportURL  = 'emp-excel-export.php' ;

?>

<!DOCTYPE HTML>
<html>
<head>
   <meta charset="UTF-8">
   <title>TimeJet | Employee Reports</title>
   <!--
   <link rel="stylesheet" href="css/style3.css" type="text/css">
   <link rel="stylesheet" href="css/styleBoot.css" type="text/css">

   <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">  
   <link rel="stylesheet" type="text/css" href="css/DT_bootstrap.css">

   <script src="js/jquery.js" type="text/javascript"></script>
   <script src="js/bootstrap.js" type="text/javascript"></script>
   <script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.dataTables.js"></script>
   <script type="text/javascript" charset="utf-8" language="javascript" src="js/DT_bootstrap.js"></script>
-->
   <?php include('header.php'); ?>
   <?php if ($tjproUser != true){include('ad.php');  }?>
</head>
<body>
	
<nav class="navbar fixed-top navbar-expand-sm bg-light navbar-light">
        <div class="container justify-content-center">
            <a class="navbar-brand" href="index.php">
            <img src="images/logo2.png" width="50" height="40" class="d-inline-block align-top" alt="">
            </a>
        
            <a class="navbar-brand mr-auto" href="index.php">TimeJet</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav mr-auto mt-2 mt-lg-0">
                <a class="nav-item nav-link" href="emp-dash.php">Dashboard <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link active" href="emp-report.php">Reports</a>
                <a class="nav-item nav-link" href="emp-reset-pwd.php">Reset Password</a>
                <a class="nav-item nav-link" href="emp-logout.php">Log out</a>
                </div>
            </div>
        </div>
    </nav>
 
    <br>
    <br>

    <br>
   <div class="container justify-content-center">
      <div class="card">
         <div class="card-body">

            <!--SUSCRIPTION WARNING-->
            <div id ="subWarning" class="alert alert-warning small" role="alert">
               Your subscription is about to expire in  <strong id="lblSubDays"><?php echo $sub_day_left ; ?> </strong> Day(s).  <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" style="color:#721c24" target="_blank"><strong>  Renew now. </strong> <a>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            </div>

            <!--SUSCRIPTION EXPIRED-->
            <div id = "subExpired" class="alert alert-danger small" role="alert">
               Your subscription has <strong> expired.  <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" style="color:#721c24" target="_blank"> Renew now. <a></strong>

               <input type="image" onclick="refreshSubscription()" src="https://timejet.dlohia.com/images/refresh_2.png"  height="20px" />

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            </div>


            <?php
               $sql = "SELECT * FROM tj_empdata WHERE emp_id = ? AND mgr_id = ? 
                              AND activity <> 'lOGIN' AND activity <> 'LOGOUT' ORDER BY id DESC ";

               if ($stmt = $conn->prepare($sql)) {

                  $stmt->bind_param("ss", $emp_id, $mgr_id);
                  $stmt-> execute();
                  $result = $stmt->get_result();

                  //$stmt->bind_result($id, $login_date, $emp_id, $activity, $case_ref, $comments, $start_time, $end_time, $total_time);
                  //$stmt->fetch();
                  //$records->mysqli_query()
                  //$records = mysqli_query($conn, $sql);
                  //if(mysqli_num_rows($records) > 0)
                  if(mysqli_num_rows($result) > 0){
                     ?>
                     
                     <h5 style="text-align:left;">
                        your reports
                        <p>
                           <form method="post" action="emp-excel-export.php">
                           <input type="submit" name="export" class="btn btn-success" id="exportXL" value="Export to excel" />
                           </form>
                        </p>
                     </h5>

                     <table class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Login Date</th>
                           <th>Activity</th>
                           <th>Case Ref#</th>
                           <th>Start Time</th>
                           <th>End Time</th>
                           <th>Total Time</th>
                        </tr>
                     </thead>
                     <tbody>

                     <?php
                        //$records = mysqli_query($conn, $sql);  // Use select query here 

                        //while($row = mysqli_fetch_array($records)){
                        while($row = $result->fetch_assoc()){
                           $id = $row['id'];
                           ?>
                        <tr class="delete_mem<?php echo $id ?>">
                           <td><?php echo $row['id']; ?></td>
                           <td><?php echo $row['login_date']; ?></td>
                           <td><?php echo $row['activity']; ?></td>
                           <td><?php echo $row['case_ref']; ?></td>
                           <td><?php echo $row['start_time']; ?></td>
                           <td><?php echo $row['end_time']; ?></td>
                           <td><?php echo $row['total_time']; ?></td>
                        </tr>
                     <?php }
                  }
                  else{
                     echo "No Records Found";
                  }

                  $stmt->close();
                  $conn->close();
                  
                  ?>
                  </tbody>
                  </table>
                  <?php
               }
               ?>
                  
         </div>
      </div>
   </div>
                 
	<footer class="bg-light text-center text-lg-start">
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0);text-align: right;">
			© 2022
			<a class="text-dark" href="https://dlohia.com/">DLA. </a>
			All Rights Reserved
		</div>
	</footer> 
    
   <script type="text/javascript">

        loadDefaults();

        function loadDefaults(){
            var sub_day_left =  "<?php echo $sub_day_left ?>" ;  
            subAdj(sub_day_left);
        };

        function subAdj(sub_day_left){
            $(':input[id="exportXL"]').prop('disabled', false);

            $("#subExpired").hide();
            $("#subWarning").hide();

            if (sub_day_left <= 0) {
                $("#subExpired").show();
                $(':input[id="exportXL"]').prop('disabled', true);
            }
            else if (sub_day_left <=7) {
                $("#subWarning").show();
            }
            else{
                $("#ad").hide();
            }
        }

        function refreshSubscription(){
            if (validSubscription() == true){
               alert("Subscription activated.");
            }
            else{
               alert("no subscription found.");
            }
        }

        function validSubscription() {
            var mgr_id  = "<?php echo $mgr_id ?>" ;   ;
            var isValid = false ;
            
            $.ajax({
                type: "POST",
                url: "subscription-check.php",
                async:false,
                data: ({
                    'mgr_id': mgr_id
                }),
                cache: false,
                success: function(response) {
                    //alert(response);
                    //if(response.search("valid") > 0){
                    response = Number(response);
                    //alert (response);
                    subAdj(response);
                    if (response > 0){
                        isValid = true;
                    }
                }
            });
            return isValid;
        };

</script>

</body>
</html>





