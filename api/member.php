<?php

	class Member extends API {

		public funtion __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function login() {
			//Get the vars
			$username = mysql_real_escape_string($_POST['username']);
			$password = md5($_POST['password']);

			//Ensure all variables needed are present
			if(!empty($username) && !empty($password) && !empty($email)) {
				//Make sure email is valid

			}
		}
	}

?>
