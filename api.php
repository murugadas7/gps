<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once("utils.php");


  //sanitize post value
  $group_number = filter_var($_POST["page_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
  
  //throw HTTP error if group number is not valid
  if(!is_numeric($group_number)){
    header('HTTP/1.1 500 Invalid number!');
    exit();
  }
  
  //get current starting point of records
  $position = ($group_number * $items_per_group);
  
 
  
  $orderBy = "order by id desc";
 
  $sqlQuery = "SELECT device_id, device_label, DATE_FORMAT(last_reported, '%m/%d/%Y %h:%i %p') AS  last_reported_time FROM list_devices $orderBy LIMIT $position, $items_per_group ";
  //echo $sqlQuery;
  $sqlFetchAll = sqlFetchAll("$sqlQuery"); 
  

  if(!empty($sqlFetchAll)){
    echo json_encode($sqlFetchAll);
  }else{
    $empty = array();
    echo json_encode($empty);
  }

?>