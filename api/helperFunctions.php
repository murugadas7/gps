<?php
/* Route - Start */ 
function route($path = NULL){
 
	$websiteLink = G_LINK . "/";
	if(!empty($path) && $path != 'pathReturn'){
		$websiteLink = $websiteLink . "$path" . "/";
	}
	if($path == 'pathReturn'){
		return $websiteLink;
	}else{
		echo $websiteLink;	
	}	
}
function returnRoute($path = NULL){

    $websiteLink = G_LINK . "/";
    if(!empty($path)){
        $websiteLink = $websiteLink . "$path" . "/";
    }
    return $websiteLink;
}

function e($v){
	echo "$v";
}
function seoLink($string, $spaceRepl = "-") {
	// Replace "&" char with "and"
	$string = str_replace("&", "and", $string);
	// Delete any chars but letters, numbers, spaces and _, -
	$string = preg_replace("/[^a-zA-Z0-9 _-]/", "", $string);
	// Optional: Make the string lowercase
	$string = strtolower($string);
	// Optional: Delete double spaces
	$string = preg_replace("/[ ]+/", " ", $string);
	// Replace spaces with replacement
	$string = str_replace(" ", $spaceRepl, $string);
	return $string;
}

function redirect_to($new_location) {
  header("Location: " . $new_location);
  exit;
}

function request_is_get() {
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function request_is_post() {
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Sanitize for HTML output 
function h($string) {
	return htmlspecialchars($string);
}

// Sanitize for JavaScript output
function j($string) {
	return json_encode($string);
}


// * validate value has presence
// use trim() so empty spaces don't count
// use === to avoid false positives
// empty() would consider "0" to be empty
function has_presence($value) {
	$trimmed_value = trim($value);
  return isset($trimmed_value) && $trimmed_value !== "";
}

// * validate value has string length
// leading and trailing spaces will count
// options: exact, max, min
// has_length($first_name, ['exact' => 20])
// has_length($first_name, ['min' => 5, 'max' => 100])
function has_length($value, $options=[]) {
	if(isset($options['max']) && (strlen($value) > (int)$options['max'])) {
		return false;
	}
	if(isset($options['min']) && (strlen($value) < (int)$options['min'])) {
		return false;
	}
	if(isset($options['exact']) && (strlen($value) != (int)$options['exact'])) {
		return false;
	}
	return true;
}

// * validate value has a format matching a regular expression
// Be sure to use anchor expressions to match start and end of string.
// (Use \A and \Z, not ^ and $ which allow line returns.) 
// 
// Example:
// has_format_matching('1234', '/\d{4}/') is true
// has_format_matching('12345', '/\d{4}/') is also true
// has_format_matching('12345', '/\A\d{4}\Z/') is false
function has_format_matching($value, $regex='//') {
	return preg_match($regex, $value);
}

// * validate value is a number
// submitted values are strings, so use is_numeric instead of is_int
// options: max, min
// has_number($items_to_order, ['min' => 1, 'max' => 5])
function has_number($value, $options=[]) {
	if(!is_numeric($value)) {
		return false;
	}
	if(isset($options['max']) && ($value > (int)$options['max'])) {
		return false;
	}
	if(isset($options['min']) && ($value < (int)$options['min'])) {
		return false;
	}
	return true;
}

// * validate value is inclused in a set
function has_inclusion_in($value, $set=[]) {
  return in_array($value, $set);
}

// * validate value is excluded from a set
function has_exclusion_from($value, $set=[]) {
  return !in_array($value, $set);
}
