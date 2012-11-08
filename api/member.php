<?php
	//Include API Class
	require_once("API.php");

	class Member extends API {

		public function __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function process() {
			//Get the action
			$action = $_POST['action'];

			//See if method exists in class
			if(method_exists($this, $action))
				$this->$action(); //Call if found, php magic
			else
				$this->response('',404); //Else send 404 (not found)
		}
		
		public function login() {
			//Get the vars
			$username = mysql_real_escape_string($_POST['username']);
			if(isset($_POST['password']))
				$password = md5($_POST['password']);

			//Ensure all variables needed are present
			if(!empty($username) && !empty($password)) {
				//Query the db
				$query = "SELECT member_id, username, is_suspended FROM members where username='$username' and password='$password' LIMIT 1";
				$sql = mysql_query($query, $this->link);

				//Check the results
				if(mysql_num_rows($sql) > 0) {
					//Get the row
					$result = mysql_fetch_array($sql, MYSQL_ASSOC);

					//Send the confirmation!
					$this->response(json_encode($result));
				}

				//Not found, return missing content
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Invalid email or password'));
				$this->response($error, 204);
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing email or password'));
			$this->response($error, 400);
		}
	}

	$api = new Member;
	$api->process();
?>
