<?php
class DB {
	private $_connection;
	private static $_instance;  
	public static function getInstance() {
		if(!self::$_instance) {  
			self::$_instance = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
			if(mysqli_connect_error()) {
					trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(),  E_USER_ERROR);
			}
			 
		}
		return self::$_instance;
	}
	// Magic method clone is empty to prevent duplication of connection
	private function __clone() { } 
}