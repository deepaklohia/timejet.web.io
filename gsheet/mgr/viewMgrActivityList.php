<?php
// Initialize the session
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

if (isset($_POST['mgr_id'])){
    $mgr_id =  $_POST['mgr_id'];
}
else{
    echo "error loading data , try login-logout ";
    exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>TimeJet | Activity List</title>

    <link rel="stylesheet" href="../css/style3.css" type="text/css">
    <link rel="stylesheet" href="../css/styleBoot.css" type="text/css">

    <link href="../css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">  
    <link rel="stylesheet" type="text/css" href="../css/DT_bootstrap.css">

    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/bootstrap.js" type="text/javascript"></script>
    <script type="text/javascript" charset="utf-8" language="javascript" src="../js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" language="javascript" src="../js/DT_bootstrap.js"></script>

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />-->
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
  
  <style>

  .box
  {
   width:980px;
   padding:20px;
   background-color:#fff;
   border:1px solid #ccc;
   border-radius:5px;
   margin-top:25px;
   box-sizing:border-box;
  }
  
    #header {
        background-color: #eee;
        border-bottom: 1px solid #e6e6e6;
        padding: 40px 0;
    }
        
    </style>
    
</head>
<body>

    <div id="wrapper">
        <div class="row-fluid">
            <div class="span12">
                <div class="container">
                    <br />
                    <div align="right">
                        <button type="button" name="add" id="add" class="btn btn-info">Add</button>
                    </div>
                    <br />
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
    </div> 
</div>

    
<script type="text/javascript" language="javascript" >

let baseUrl = "https://timejet.dlohia.com/gsheet" ;
let fetchURL =  baseUrl + '/mgr/fetchActivity.php' ;
let insertURL  = baseUrl + '/mgr/insertActivity.php' ;
let updateURL = baseUrl + '/mgr/updateActivity.php' ;
let deleteURL  = baseUrl + '/mgr/deleteActivity.php' ;

 $(document).ready(function(){
  
  fetch_data();

  function fetch_data()
  {
    //mgr_id = "manager@dlohia.com" ;
    var mgr_id =  "<?php echo $mgr_id ?>" ;
    var dataTable = $('#user_data').DataTable({
      "processing" : true,
      "serverSide" : true,
      "order" : [],
      "ajax" : {
      url: fetchURL,
      type:"POST" , 
      data:{mgr_id:mgr_id}
      }
    });

  }

  function update_data(id, column_name, value)
  {
   $.ajax({
    url:updateURL,
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
     url:insertURL,
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
     url:deleteURL,
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
 });
</script>


    
</body>
</html>








