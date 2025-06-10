<?php

require_once "config.php";

session_start();
 
//getting manager id
if  (isset($_SESSION["mgrloggedin"]) && $_SESSION["mgrloggedin"] === true){
    if(isset($_SESSION["mgr_id"])  ){
        $mgr_id = $_SESSION["mgr_id"] ;
    }
    else{
        header("location: mgr-login.php");
        exit ;
    }
}

else{
   header("location: mgr-login.php");
   exit ;
}

$columns = array('activity_list');

$query = "SELECT * FROM tj_activity_list WHERE mgr_id = ? ";

if(isset($_POST["search"]["value"]))
{
    $query .= ' AND activity_name LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST["order"]))
{
    $query .= 'ORDER BY activity_name ' ;
}
else
{
 $query .= 'ORDER BY id DESC ';
}

$query1 = '';

if($_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$stmt = $conn->prepare($query . $query1);
$stmt->bind_param("s", $mgr_id);
$stmt-> execute();
$result = $stmt->get_result();
$number_filter_row = mysqli_num_rows($result);

//$result = mysqli_query($conn, $query . $query1);

$data = array();

while($row = mysqli_fetch_array($result))
{
 $sub_array = array();
 $sub_array[] = '<div contenteditable class="update" data-id="'.$row["id"].'" data-column="activity_name">' . $row["activity_name"] . '</div>';
 //$sub_array[] = '<div contenteditable class="update" data-id="'.$row["id"].'" data-column="last_name">' . $row["last_name"] . '</div>';
 $sub_array[] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="'.$row["id"].'">Delete</button>';
 $data[] = $sub_array;
}

function get_all_data($conn)
{
 $query = "SELECT * FROM tj_activity_list";
 $result = mysqli_query($conn, $query);
 return mysqli_num_rows($result);
}

$output = array(
 "draw"    => intval($_POST["draw"]),
 "recordsTotal"  =>  get_all_data($conn),
 "recordsFiltered" => $number_filter_row,
 "data"    => $data
);

echo json_encode($output);

?>




