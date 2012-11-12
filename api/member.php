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
			if (isset($_POST['password'])) {
				$saltqresult = mysql_query("SELECT salt FROM members where username='$username' LIMIT 1;", $this->link);
				if(mysql_num_rows($saltqresult) != 0)
					$password = sha1($_POST['password'].mysql_result($saltqresult, 0, salt));
			}

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
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Invalid username or password'));
				$this->response($error, 204);
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or password'));
			$this->response($error, 400);
		}

		public function register() {
			//Get data
			$username = mysql_real_escape_string($_POST['username']);
			$password = mysql_real_escape_string($_POST['password']);
			$email = mysql_real_escape_string($_POST['email']);
			$salt = mt_rand();

			//Make sure data arrives
			if(!empty($username) && !empty($password) && !empty($email)) {
				//Hash the password
				$hashedpass = sha1($password.$salt);
				$result = mysql_query("INSERT INTO members (is_admin,is_suspended,username,password,salt,email) VALUES ('false','false','$username','$hashedpass','$salt','$email')");
				
				//Make sure query works
				if(!$result) {
					//Get error
					$err = mysql_errno();
					
					//Check if username already taken
					if($err == 1062) {
						// Username is already in use
						$error = json_encode(array('status' => 'Failed', 'msg' => 'Username already in use'));
						$this->response($error, 204);
						return;
					}

					//Something else went wrong
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
					$this->response($error, 500);
				}
				//Successful creation
				$msg = json_encode(array('status' => "Success"));
				$this->response($msg, 200);
			}

			//Missing data from request
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing data'));
                        $this->response($error, 400);
		}

		public function logout() {
			//Get the vars
                        $username = mysql_real_escape_string($_POST['username']);
                        if(isset($_POST['password']))
                                $password = md5($_POST['password']);

                        //Ensure all variables needed are present
                        if(!empty($username) && !empty($password)) {
                                //Send the confirmation!
                        	$this->response(200);
                        }

                        //Missing input, send response
                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or password'));
                        $this->response($error, 400);
		}

		public function deleteAccount() {
			//Get the vars
                        $username = mysql_real_escape_string($_POST['username']);
                        if(isset($_POST['password'])) {
				$res = mysql_query("SELECT salt FROM members WHERE username='$username' LIMIT 1", $this->link);
				if(!$res)
					goto no_such_user;
				$hashedpass = sha1($password.mysql_result($res, 0, salt);
			}

                        //Ensure all variables needed are present
                        if(!empty($username) && !empty($password)) {
				//Query the db
                                $query = "REMOVE FROM members where username='$username' and password='$hashedpass'";
                                mysql_query($query, $this->link);

				//Send the confirmation
                                $this->response(200);                                                                                                                              
                        }
no_such_user:
                        //Missing input, send response
                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or password'));
                        $this->response($error, 400);
            	}

                public function suspendUser() {
                        // FIXME: Check that 'is_admin' is set for the user making this call
			//Get the vars
                        $toSuspend = mysql_real_escape_string($_POST['toSuspend']);

                        //Ensure all variables needed are present
                        if(!empty($toSuspend)) {
                                //check if member to suspend exists and isn't suspended                                                                       
                                $query = "SELECT is_Suspended FROM members where username='$toSuspend'";          
                                $sql = mysql_query($query, $this->link);
                                                                                                                    
                                if((mysql_num_rows($sql) < 1) || ($sql == 1)){                                                       
                                        //member doesn't exist or is already suspended                                                                    
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'User does not exist or is already suspended'));
                                        $this->response($error, 204);
                                }
                                else {
                                        //Query the db
                                        $query = "UPDATE members SET is_suspended = 1 where username='$toSuspend'";
                                        mysql_query($query, $this->link);

                                        //Send the confirmation!                                                                                                                                
                                        $this->response(200);
                                }
                        }

                        //Missing input, send response
                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username to suspend'));                                                                              
                        $this->response($error, 400);
                }

                public function unsuspendUser() {
                        //Get the vars
                        $toUnsuspend = mysql_real_escape_string($_POST['toUnsuspend']);

                        //Ensure all variables needed are present
                        if(!empty($toUnsuspend)) {
                                //check if member to suspend exists and is suspended                                                                                        
                                $query = "SELECT is_suspended FROM members where username='$toUnsuspend'";
                                $sql = mysql_query($query, $this->link);

                                if(mysql_num_rows($sql) < 1) || ($sql == 0)){
                                        //member doesn't exist or is already unsuspended                                                                                 
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'User does not exist or is already unsuspended'));
                                        $this->response($error, 204);                                                                                                                           
                                }
                                else {
                                        //Query the db
                                        $query = "UPDATE members SET is_suspended = 0 where username='$toUnsuspend'";
                                        mysql_query($query, $this->link);

                                        //Send the confirmation!                                                                                                                                
                                        $this->response(200);
                                }
                        }

                        //Missing input, send response
                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username to unsuspend'));
                        $this->response($error, 400);
                }
	}

	$api = new Member;
	$api->process();
?>
