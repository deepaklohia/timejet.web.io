<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

if (isset($_POST['mgr_id2'])){
    $mgr_id =  $_POST['mgr_id2'];
    $mgr_name =  $_POST['mgr_name'];
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
    <title>TimeJet | Active Users</title>

    <link rel="stylesheet" href="../css/style3.css" type="text/css">
    <link rel="stylesheet" href="../css/styleBoot.css" type="text/css">
    <link href="../css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">  
    <link rel="stylesheet" type="text/css" href="../css/DT_bootstrap.css">
    
    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/bootstrap.js" type="text/javascript"></script>
    <script type="text/javascript" charset="utf-8" language="javascript" src="../js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8" language="javascript" src="../js/DT_bootstrap.js"></script>

    <style>
    
    #header {
        background-color: #eee;
        border-bottom: 1px solid #e6e6e6;
        padding: 40px 0;
    }
    </style>
</head>
<body>
    
    <div id="contents">
                    <div class="row-fluid">
                    <div class="span12">
                    <div class="container">
                    <br><br>
                    <h4 style="text-align:left;">
                        <?php echo htmlspecialchars($mgr_name); ?>
                        (<?php echo htmlspecialchars($mgr_id); ?>)
                        : active logins 
                    </h4>

                <table class="table table-striped table-bordered" id="example">
                    <thead>
                        <tr>
                        <th>ID</th>
                            <th>Emp ID</th>
                            <th>Emp Login/Logout Time</th>
                            <th>Last Activity</th>
                            <th>Since Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM tj_login WHERE mgr_id = '$mgr_id' 
                                        AND user_type ='emp' ";

                        $records = mysqli_query($conn, $sql);  // Use select query here 

                            while($row = mysqli_fetch_array($records)){
                                $id = $row['id'];
                                $name =$row['emp_id'];
                                ?>
                            <tr class="delete_mem<?php echo $id ?>">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['emp_id']; ?></td>
                                <td><?php echo $row['emp_loginlogout_dttm']; ?></td>
                                <td><?php echo $row['activity']; ?></td>
                                <td><?php echo $row['since_time']; ?></td>

                                <td width="80">
                                <a class="btn btn-danger" name="<?php echo $name; ?>" id="<?php echo $id; ?>">Delete user</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
            </div>
            </div>
    </div>
    
<script type="text/javascript">
    $(document).ready(function() {
        $('.btn-danger').click(function() {
            var del_id = $(this).attr("id");
            var emp_id = $(this).attr("name");
            
            if (confirm("delete login info of user : " + emp_id + " ( id:"  +  del_id + ")" )) {
                $.ajax({
                    type: "POST",
                    url: "deleteMgrUser.php",
                    data: ({
                        del_id: del_id
                    }),
                    cache: false,
                    success: function(html) {
                        $(".delete_mem" + del_id).fadeOut('slow');
                    }
                });
            } else {
                return false;
            }
        });
    });
</script>
    
</body>
</html>
 








