<?php
// Must call session_start() before this loads

// Generate a token for use with CSRF protection.
// Does not store the token.
function csrf_token() {
	return md5(uniqid(rand(), TRUE));
}

// Generate and store CSRF token in user session.
// Requires session to have been started already.
function create_csrf_token() {
	$token = csrf_token();
	$_SESSION['csrf_token'] = $token;
 	$_SESSION['csrf_token_time'] = time();
	return $token;
}

// Destroys a token by removing it from the session.
function destroy_csrf_token() {
  $_SESSION['csrf_token'] = null;
 	$_SESSION['csrf_token_time'] = null;
	return true;
}

// Return an HTML tag including the CSRF token 
// for use in a form.
// Usage: echo csrf_token_tag();
function csrf_token_tag() {
	$token = create_csrf_token();
	return "<input type=\"hidden\" name=\"csrf\" value=\"".$token."\">";
}

// Returns true if user-submitted POST token is
// identical to the previously stored SESSION token.
// Returns false otherwise.
function csrf_token_is_valid() {
	if(isset($_POST['csrf'])) {
		$user_token = $_POST['csrf'];
		$stored_token = $_SESSION['csrf_token'];
		return $user_token === $stored_token;
	} else {
		return false;
	}
}

// You can simply check the token validity and 
// handle the failure yourself, or you can use 
// this "stop-everything-on-failure" function. 
function die_on_csrf_token_failure() {
	if(!csrf_token_is_valid()) {
		die("CSRF token validation failed.");
	}
}

// Optional check to see if token is also recent
function csrf_token_is_recent() {
	$max_elapsed = 60 * 60 * 24; // 1 day
	if(isset($_SESSION['csrf_token_time'])) {
		$stored_time = $_SESSION['csrf_token_time'];
		return ($stored_time + $max_elapsed) >= time();
	} else {
		// Remove expired token	
		destroy_csrf_token();
		return false;
	}
}

// Blacklist functions

// Check if an IP has been blacklisted.
// Returns true or false.
function is_blacklisted_ip($ip) {
	$blacklisted_ip = find_one_in_fake_db('blacklisted_ips', 'ip', sql_prep($ip));
	return isset($blacklisted_ip);
}

// The function that actually performs the blocking.
function block_blacklisted_ips() {
	$request_ip = $_SERVER['REMOTE_ADDR'];
	if(isset($request_ip) && is_blacklisted_ip($request_ip)) {
		die("Request blocked");
	}
}

// Add an IP address to the blacklist
// Can be done automatically after a certain 
// amount of bad behavior is reached.
function add_ip_to_blacklist($ip) {
	$record = ['ip' => sql_prep($ip)];
	add_record_to_fake_db('blacklisted_ips', $record);
	return true;
}

function request_is_same_domain() {
	if(!isset($_SERVER['HTTP_REFERER'])) {
		// No refererer sent, so can't be same domain
		return false;
	} else {
		$referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		$server_host = $_SERVER['HTTP_HOST'];

		// Uncomment for debugging
		// echo 'Request from: ' . $referer_host . "<br />";
		// echo 'Request to: ' . $server_host . "<br />";

		return ($referer_host == $server_host) ? true : false;
	}
}
 
function end_session() {
	// Use both for compatibility with all browsers
	// and all versions of PHP.
	$count = $_SESSION[Count];
	$session_id = $_SESSION[cartCount];
	//$totalCost = $_SESSION[totalCost];
	//$shippingCost = $_SESSION[shippingCost];
	session_unset();
	session_destroy();
	session_start();
	$_SESSION[Count] = $count;
	$_SESSION[cartCount] = $session_id;
	//$_SESSION[totalCost] = $totalCost;
	//$_SESSION[shippingCost] = $shippingCost;
}

// Does the request IP match the stored value?
function request_ip_matches_session() {
	// return false if either value is not set
	if(!isset($_SESSION['ip']) || !isset($_SERVER['REMOTE_ADDR'])) {
		return false;
	}
	if($_SESSION['ip'] === $_SERVER['REMOTE_ADDR']) {
		return true;
	} else {
		return false;
	}
}

// Does the request user agent match the stored value?
function request_user_agent_matches_session() {
	// return false if either value is not set
	if(!isset($_SESSION['user_agent']) || !isset($_SERVER['HTTP_USER_AGENT'])) {
		return false;
	}
	if($_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {
		return true;
	} else {
		return false;
	}
}

// Has too much time passed since the last login?
function last_login_is_recent() {
	$max_elapsed = 60 * 60 * 24; // 1 day
	// return false if value is not set
	if(!isset($_SESSION['last_login'])) {
		return false;
	}
	if(($_SESSION['last_login'] + $max_elapsed) >= time()) {
		return true;
	} else {
		return false;
	}
}

// Should the session be considered valid?
function is_session_valid() {
	$check_ip = true;
	$check_user_agent = true;
	$check_last_login = true;

	if($check_ip && !request_ip_matches_session()) {
		return false;
	}
	if($check_user_agent && !request_user_agent_matches_session()) {
		return false;
	}
	if($check_last_login && !last_login_is_recent()) {
		return false;
	}
	return true;
}

// If session is not valid, end and redirect to login page.
function confirm_session_is_valid() {
	if(!is_session_valid()) {
		end_session();
		// Note that header redirection requires output buffering 
		// to be turned on or requires nothing has been output 
		// (not even whitespace).
		header("Location: login.php");
		exit;
	}
}


// Is user logged in already?
function is_logged_in() {
	return (isset($_SESSION['logged_in']) && $_SESSION['logged_in']);
}

// If user is not logged in, end and redirect to login page.
function confirm_user_logged_in() {
	
	if(!is_logged_in()) {
		end_session();
		// Note that header redirection requires output buffering 
		// to be turned on or requires nothing has been output 
		// (not even whitespace).
		$_SESSION['redirectTo'] = $_SERVER['REQUEST_URI'];
		header("Location: login.php");
		exit;
	}
}


// Actions to preform after every successful login
function after_successful_login($email) {
	// Regenerate session ID to invalidate the old one.
	// Super important to prevent session hijacking/fixation.
	session_regenerate_id();
	
	$_SESSION['logged_in'] = true;
	$_SESSION['email'] = $email;
	// Save these values in the session, even when checks aren't enabled 
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['last_login'] = time();
	
}

// Actions to preform after every successful logout
function after_successful_logout() {
	$_SESSION['logged_in'] = false;
	end_session();
}

// Actions to preform before giving access to any 
// access-restricted page.
function before_every_protected_page() {
	confirm_user_logged_in();
	confirm_session_is_valid();
}


// Brute force throttling

// IMPORTANT: The session is used for demonstration purposes only.
// A hacker attempting a brute force attack would not bother to send 
// cookies, which would mean that you could not use the session 
// (which is referenced by a cookie).
// In real life, use a real database.

function record_failed_login($username) {
	$time = time();
	$failed_login = find_in_db('failed_login', 'email', sql_prep($username));
	 
	if(!isset($failed_login)) { 		
		add_record_to_failed_login_table('failed_login', $username, 1  , $time);
	} else {		  
		$failed_count = $failed_login[failed_count] + 1;
		update_record_in_db('failed_login', 'email', $username, $failed_count, $time );
	}
	
	return true;
}

function clear_failed_logins($username) {
	$time = time();
	$failed_login = find_in_db('failed_login', 'email', sql_prep($username));
	if(isset($failed_login)) {		
		update_record_in_db('failed_login', 'email', $username, $failed_count, $time);
	}	
	return true;
}

// Returns the number of minutes to wait until logins 
// are allowed again.
function throttle_failed_logins($username) {
	$throttle_at = 3;
	$delay_in_minutes = 10;
	$delay = 60 * $delay_in_minutes;
	
	$failed_login = find_in_db('failed_login', 'email', sql_prep($username));

	// Once failure count is over $throttle_at value, 
	// user must wait for the $delay period to pass.
	if(isset($failed_login) && $failed_login['failed_count'] >= $throttle_at) {
		$remaining_delay = ($failed_login['failed_on'] + $delay) - time();
		$remaining_delay_in_minutes = ceil($remaining_delay / 60);
		return $remaining_delay_in_minutes;
	} else {
		return 0;
	}
}
