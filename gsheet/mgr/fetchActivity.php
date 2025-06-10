<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
require_once "../auth/config.php";

$columns = array('activity_list');

$mgr_id = trim($_POST["mgr_id"]);

$query = "SELECT * FROM tj_activity_list WHERE mgr_id = '$mgr_id' ";

if(isset($_POST["search"]["value"]))
{
    $query .= ' AND activity_name LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST["order"]))
{
    $query .= 'ORDER BY activity_name ' ;
 //$query .= 'ORDER BY '.$columns[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
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

$number_filter_row = mysqli_num_rows(mysqli_query($conn, $query));

$result = mysqli_query($conn, $query . $query1);

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





