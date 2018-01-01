<?php
function sqlFetchAll($sql) {
	$db = DB::getInstance();

	$result = $db->query($sql);
	while($row=$result->fetch_assoc()) {
	   $return[] = $row;
	}
	return $return;
} 

function sqlFetch($sql) {
	$db = DB::getInstance();

	$result = $db->query($sql);
	$row = $result->fetch_assoc();
 
	return $row;
} 
 

function insert_row($sql, $getlastInsertId = NULL) {
	$db = DB::getInstance(); 
	$result = $db->query($sql); 
	if(!empty($getlastInsertId)){
		$lastInsertId = $db->insert_id;
		return $lastInsertId;
	}else{
		return $result;
	}
} 

function optionList($sql, $key, $value, $selectBoxName,  $title = NULL)
{ 
	$sqlArray = sqlFetchAll($sql);
	$result = "<select name=\"$selectBoxName\"  class=\"$selectBoxName\"  id=\"$selectBoxName\"  > 
	<option value=\"\">Select $selectBoxName</option>"; 
	foreach($sqlArray as $a1){
			$result .= "<option value=\"$a1[$key]\">$a1[$title] - $a1[$value]</option>\n\t"; 	 
	} 
	$result .= "</select>";
	echo $result;
}

function CheckBoxList($name, $value, $lastWord = NULL){
 
	 
 
    $valueArray = explode(', ' ,$value);
	foreach($valueArray as $key => $checkboxValue){	
		$qid =   str_replace(" ", "", $name) . "$key";
		
		$checkboxresult .= "<li>
								<input id='".$qid."' name='".$name."' type='checkbox' value='".$checkboxValue."'     >
								<label for='".$qid."'>".$checkboxValue."".$lastWord."</label>
							</li>"; 	
	}	
 
	
 
	return $checkboxresult;
}

function sql_prep($string) {
	global $database;
	if($database) {
		return mysql_real_escape_string($database, $string);
	} else {
		// addslashes is almost the same, but not quite as secure.
		// Fallback only when there is no database connection available.
	 	return addslashes($string);
	}
}

function find_all_in_db($table, $key, $value) {
	$db = DB::getInstance(); 
	$sql = "select * from $table where $key = '$value'";
	$result = $db->query($sql); 
	
 
	$results = [];
  foreach($result as $record) {
    if (isset($record[$key]) && $record[$key] == $value) {
			// This is a matching record, add it to results array
      $results[] = $record;
    }
  }
  return $results;
}

function find_in_db($table, $key, $value){
	$results = find_all_in_db($table, $key, $value);
	$result = count($results) > 0 ? $results[0] : null;
	return $result;
}

function add_record_to_failed_login_table($table, $email, $count, $time) { 
	 
	$sql = "insert into $table set email = '$email', failed_count = '$count', failed_on = '$time' ";
	insert_row($sql);
	return true;
}

function update_record_in_db($table, $key, $email, $count, $time) {
 	$sql = "update  $table set failed_count = '$count' , failed_on = '$time' where  $key = '$email'";
	insert_row($sql); 
	return true;
}
?>