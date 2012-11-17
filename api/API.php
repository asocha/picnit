<?php

	/*

	Title: API.php
	Author: PhotoDolo

	Base class for all API functions.  Contains functions that most API classes use.

	*/

	class API {

		const DB_USER = 'picnit';
		const DB_NAME = 'picnit';
		const DB_PASS = 'PhotoDolo247';
		const DB_HOST = 'localhost';

		public $memberid;

		//Response code
		private $_code = 200;

		//Type of content being sent (MIME)
		public $_content_type = "application/json";

		public function __construct() {

		}

		public function db_connect() {
			//Connect to the mysql database
			$link = mysql_connect(self::DB_HOST, self::DB_USER, self::DB_PASS);

			//Make sure link works
			if(!$link) {
				header("HTTP/1.1 503 Service Unavailable");
				exit;
			}

			//Connect to the picnit db
			mysql_select_db(self::DB_NAME, $link);

			//Return the link
			return $link;
		}

		public function db_close($link) {
			//Close the connection on the given link
			mysql_close($link);
		}

		//Sends a response back
		public function response($data, $status){
			//Ensure we have a status
			$this->_code = ($status)? $status : 200;

			//Set the headers for the response
			header("HTTP/1.1 " . $this->_code . " " . $this->get_status_message());
			header("Content-Type:" . $this->_content_type);

			//Print the data
			echo $data;

			//Exit the request
			exit;
		}

		private function get_status_message() {
			$status = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				306 => '(Unused)',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}

		public function process() {
			//Get the action
			$action = $_POST['action'];

			// Set our magic memberid variable
			$this->memberid = $this->authenticateUser();

			//See if method exists in class
			if(method_exists($this, $action))
				$this->$action(); //Call if found, php magic
			else
				$this->response('Invalid action',404); //Else send 404 (not found)
		}

		public function getUsername() {
			return mysql_real_escape_string($_POST['username']);
		}

		public function getPassword($username) {
			if (isset($_POST['password'])) {
				$saltqresult = mysql_query("SELECT salt FROM members where username='$username' LIMIT 1;", $this->link);
				if(mysql_num_rows($saltqresult) != 0)
					 return sha1($_POST['password'].mysql_result($saltqresult, 0, salt));
			}
			return null;
		}

		public function authenticateUser() {
			//Get the username
			$username = $this->getUsername();

			//Get the key value
			$key = mysql_real_escape_string($_POST['key']);

			//See if it exists in database
			$query = "SELECT member_id FROM members WHERE username='$username' AND password='$key' AND is_suspended='false' LIMIT 1";
			$result = mysql_query($query, $this->link);

			//Result? Send member id
			if(mysql_num_rows($result) != 0)
				return mysql_result($result);

			//Else return anon user
			return -1;
		}
	}
?>
