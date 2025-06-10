<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["mgrloggedin"]) || $_SESSION["mgrloggedin"] !== true){
    header("location: mgr-login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
$sub_day_left = 0 ;
$mgr_id =  $_SESSION["mgr_id"] ;
$sub_day_left =  $_SESSION["sub_day_left"] ;
$tjproUser = $_SESSION["tjproUser"] ;
?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>TimeJet | Activity List</title>
    <?php include('header.php'); ?>
    <?php if ($tjproUser != true){include('ad.php');  }?>
    <!--
    <link rel="stylesheet" href="css/style3.css" type="text/css">
    <link rel="stylesheet" href="css/styleBoot.css" type="text/css">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">  
    <link rel="stylesheet" type="text/css" href="css/DT_bootstrap.css">
-->

<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
 
<link href="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.css" rel="stylesheet"/>
<!--
<script src="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.js"></script>
-->

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
                <a class="nav-item nav-link" href="mgr-dash.php">Dashboard <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link active" href="mgr-activity-list.php">Activites</a>
                <a class="nav-item nav-link" href="mgr-report.php">Reports</a>
                <a class="nav-item nav-link" href="mgr-reset-pwd.php">Reset Password</a>
                <a class="nav-item nav-link" href="mgr-logout.php">Log out</a>
                </div>
            </div>
        </div>
    </nav>

    <br>
	<br>
    <br>
    
    <div class="container justify-content-left">
        <div class="card">
            <div class="card-body">

             <!--SUSCRIPTION WARNING-->
             <div id ="subWarning" class="alert alert-warning small" role="alert">
                    Your subscription is about to expire in  <strong id="lblSubDays"><?php echo $sub_day_left ; ?> </strong> Day(s).  <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" style="color:#721c24" target="_blank"><strong>  Renew now. </strong> </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>

                <!--SUSCRIPTION EXPIRED-->
                <div id = "subExpired" class="alert alert-danger small" role="alert">
                    Your subscription has <strong> expired.  <a href="https://www.fiverr.com/dlohia/create-subscription-for-time-jet-pro" style="color:#721c24" target="_blank"> Renew now. </a></strong>

                    <input type="image" onclick="refreshSubscription()" src="https://timejet.dlohia.com/images/refresh_2.png"  height="20px" />

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>


                <button type="button" name="add" id="add" class="btn btn-info">Add Activity</button>
                <br>
                <br>
                <div id="alert_message"></div>
                    <table id="user_data" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Activity Name</th>
                        <th></th>
                        </tr>
                        </thead>
                    </table>
                </div>
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
    
<script type="text/javascript" language="javascript" >
 //$(document).ready(function(){
  
    loadDefaults();
    fetch_data();

    function loadDefaults(){
        var sub_day_left =  "<?php echo $sub_day_left ?>" ;  
        subAdj(sub_day_left);
    };

    function subAdj(sub_day_left){
        $("#subExpired").hide();
        $("#subWarning").hide();
        $(':input[id="add"]').prop('disabled', false);

        if (sub_day_left <= 0) {
            $("#subExpired").show();
            $(':input[id="add"]').prop('disabled', true);
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


  function fetch_data()
  {
   var urlLink = "mgr-act-fetch.php" ;
   var dataTable = $('#user_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "order" : [],
    "ajax" : {
     url: urlLink,
     type:"POST" , 
    }
   });
  }
  
  function update_data(id, column_name, value)
  {
   $.ajax({
    url:"mgr-act-update.php",
    method:"POST",
    data:{id:id, column_name:column_name, value:value},
    success:function(data)
    {
     $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
     $('#user_data').DataTable().destroy();
     fetch_data();
    }
   });
   setInterval(function(){
    $('#alert_message').html('');
   }, 5000);
  }

  $(document).on('blur', '.update', function(){
   var id = $(this).data("id");
   var column_name = $(this).data("column");
   var value = $(this).text();
   update_data(id, column_name, value);
  });
  
  $('#add').click(function(){
   var html = '<tr>';
   html += '<td contenteditable id="data1"></td>';
   //html += '<td contenteditable id="data2"></td>';
   html += '<td><button type="button" name="insert" id="insert" class="btn btn-success btn-xs">Insert</button></td>';
   html += '</tr>';
   $('#user_data tbody').prepend(html);
  });
  
  $(document).on('click', '#insert', function(){
   var activity_name = $('#data1').text();
   var mgr_id =  "<?php echo $mgr_id ?>" ;
   if(activity_name != '')
   {
    $.ajax({
     url:"mgr-act-insert.php",
     method:"POST",
     data:{activity_name:activity_name, mgr_id:mgr_id},
     success:function(data)
     {
      $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
      $('#user_data').DataTable().destroy();
      fetch_data();
     }
    });
    setInterval(function(){
     $('#alert_message').html('');
    }, 5000);
   }
   else
   {
    alert("Both Fields is required");
   }
  });
  
  $(document).on('click', '.delete', function(){
   var id = $(this).attr("id");
   if(confirm("Are you sure you want to remove this?"))
   {
    $.ajax({
     url:"mgr_act_del.php",
     method:"POST",
     data:{id:id},
     success:function(data){
      $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
      $('#user_data').DataTable().destroy();
      fetch_data();
     }
    });
    setInterval(function(){
     $('#alert_message').html('');
    }, 5000);
   }
  });
 //});
</script>
    
</body>
</html>





