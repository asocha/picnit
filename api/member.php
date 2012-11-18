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

		public function login() {
			//Get the vars
			$username = $this->getUsername();
			$password = $this->getPassword($username);

			//Ensure all variables needed are present
			if(isset($username) && isset($password)) {
				//Query the db
				$res = mysql_query("SELECT member_id, username, is_suspended, password FROM members where username='$username' and password='$password' LIMIT 1");

				//Check the results
				if(mysql_num_rows($res) > 0) {
					//Get the row
					$result = mysql_fetch_array($res, MYSQL_ASSOC);

					//Rename passowrd to key
					$result['key'] = $result['password'];
					unset($result['password']);

					//Send the confirmation!
					$this->response(json_encode($result), 200);
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
			$username = $this->getUsername();
			$password = mysql_real_escape_string($_POST['password']);
			$email = mysql_real_escape_string($_POST['email']);
			$salt = mt_rand();

			//Make sure data arrives
			if(isset($username) && isset($password) && isset($email)) {
				//Hash the password
				$hashedpass = sha1($password.$salt);
				$result = mysql_query("INSERT INTO members (is_admin,is_suspended,username,password,salt,email) VALUES ('false','false','$username','$hashedpass','$salt','$email')");

				//Make sure query works
				if(!$result) {
					//Get error
					$err = mysql_errno();

					if($err == 1062) {
						// Username or E-Mail is already in use - figure out which
						$errstr = mysql_error();

						if(strstr($errstr, "username")) {
							$error = json_encode(array('status' => 'Failed', 'msg' => 'Username already in use'));
						} else {
							$error = json_encode(array('status' => 'Failed', 'msg' => 'E-Mail already in use'));
						}
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

		/*
			I'm not sure if this method is needed, logging out will probably only
			delete the cookies saved in the browser
		*/
		public function logout() {
			//Get the vars
			$username = $this->getUsername();
			$password = $this->getPassword($username);

			//Ensure all variables needed are present
			if(isset($username) && isset($password)) {
				//Send the confirmation!
				$this->response('',200);
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or password'));
			$this->response($error, 400);
		}

		public function deleteAccount() {
			//Get the vars
			$username = $this->getUsername();
			if(isset($_POST['password'])) {
				$res = mysql_query("SELECT salt FROM members WHERE username='$username' LIMIT 1");
				if(!$res)
					goto no_such_user;
				$hashedpass = sha1($password.mysql_result($res, 0, salt));
			}

			//Ensure all variables needed are present
			if(isset($username) && isset($password)) {
				//Query the db
				mysql_query("REMOVE FROM members where username='$username' and password='$hashedpass'");

				//Send the confirmation
				$this->response('',200);
			}

			no_such_user:

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or password'));
			$this->response($error, 400);
		}

		public function suspendUser() {
			//Get the vars
			$username = $this->getUsername();
			$toSuspend = mysql_real_escape_string($_POST['toSuspend']);

			//Ensure all variables needed are present
			if(isset($toSuspend) && isset($username)) {
				//check if user is an admin
				$res = mysql_query("SELECT is_admin FROM members where username='$username'");
				$array = mysql_fetch_array($res);
                                $admin = $array['is_admin'];
				if($admin == 0){
					//user is not admin, cannot suspend
					$error = json_encode(array('status' => 'Failed', 'msg' => 'User is not admin'));
					$this->response($error, 401);
				}

				//check if member to suspend exists and isn't suspended
				$res = mysql_query("SELECT is_suspended FROM members where username='$toSuspend'");
				$array = mysql_fetch_array($res);
                                $suspended = $array['is_suspended'];

				if((mysql_num_rows($res) < 1) || ($suspended == 1)){
					//member doesn't exist or is already suspended
					$error = json_encode(array('status' => 'Failed', 'msg' => 'User does not exist or is already suspended'));
					$this->response($error, 204);
				}
				else {
					//Query the db
					mysql_query("UPDATE members SET is_suspended = 1 where username='$toSuspend'");
					//Send the confirmation!
					$this->response('',200);
				}
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username to suspend'));
			$this->response($error, 400);
		}

		public function unsuspendUser() {
			//Get the vars
			$toUnsuspend = mysql_real_escape_string($_POST['toUnsuspend']);
			$username = $this->getUsername();

			//Ensure all variables needed are present
			if(isset($toUnsuspend) && isset($username)) {
				//check if user is an admin
				$res = mysql_query("SELECT is_admin FROM members where username='$username'");
				$array = mysql_fetch_array($res);
                                $admin = $array['is_admin'];
				if($admin == 0){
					//user is not admin, cannot unsuspend
					$error = json_encode(array('status' => 'Failed', 'msg' => 'User is not admin'));
					$this->response($error, 401);
				}

				//check if member to suspend exists and is suspended
				$res = mysql_query("SELECT is_suspended FROM members where username='$toUnsuspend'");
				$array = mysql_fetch_array($res);
                                $suspended = $array['is_suspended'];

				if((mysql_num_rows($res) < 1) || ($suspended == 0)){
					//member doesn't exist or is already unsuspended
					$error = json_encode(array('status' => 'Failed', 'msg' => 'User does not exist or is already unsuspended'));
					$this->response($error, 204);
				}
				else {
					//Query the db
					mysql_query("UPDATE members SET is_suspended = 0 where username='$toUnsuspend'");
					//Send the confirmation!
					$this->response('',200);
				}
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username to unsuspend'));
			$this->response($error, 400);
		}

		public function memberData() {
			$username = $this->getUsername();
			if (isset($username)){
				$res = mysql_query("SELECT member_id, is_admin, is_suspended, username, password, email FROM members where username='$username'");
				$array = mysql_fetch_array($res);
				//success
				$this->response(json_encode($array), 200);
			}
			
			//missing data
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username'));
			$this->response($error, 400);
		}

		public function requestFollow() {
			$username = $this->getUsername();
			$toFollow = mysql_real_escape_string($_POST['toFollow']);
			if (isset($username) && isset($toFollow)){
				//check if user is already following that person
				$res = mysql_query("SELECT * FROM follows where follower_id='$username' and followee_id='$toFollow'");
				if ($res){
					//user is already following that person
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Already following that user'));
                        		$this->response($error, 417);
				}

				//create follow request
				$res = mysql_query("INSERT INTO messages VALUES ('$username', '$toFollow', 'follow_request', 0, '')");

                                //Make sure query works
                                if(!$res) {
                                        //Get error
                                        $err = mysql_errno();

                                        if($err == 1062) {
                                                //user has already follow requested that person
                                                $error = json_encode(array('status' => 'Failed', 'msg' => 'Already requested to follow that user'));
                                                $this->response($error, 409);
                                        }

                                        //Something else went wrong
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
                                        $this->response($error, 500);
				}

				//success
                                $this->response(json_encode('', 200));
			}
			
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or tofollow'));
                        $this->response($error, 400);
		}

		public function follow() {
			$username = $this->getUsername();
                        $toFollow = mysql_real_escape_string($_POST['toFollow']);

                        if (isset($username) && isset($toFollow)){
				//make sure user has requested to follow
                                $res = mysql_query("SELECT * FROM messages where sender_id='$username' and member_id='$toUnfollow'");
                                if(!$res){
                                        //has not sent follow request
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'User did not request to follow that person'));
                                        $this->response($error, 417);
                                }

                                //delete follow request
                                mysql_query("REMOVE FROM follows where follower_id='$username' and followee_id='$toUnfollow");
                                
				//implement Follow
				$res = mysql_query("INSERT INTO follows VALUES ('$username', '$toFollow')");
				
				//Make sure query works
                                if(!$res) {
                                        //Get error
                                        $err = mysql_errno();

                                        if($err == 1062) {
                                                //user is already following that person
                                                $error = json_encode(array('status' => 'Failed', 'msg' => 'User is already following that person'));
                                                $this->response($error, 409);
                                        }

                                        //Something else went wrong
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
                                        $this->response($error, 500);
                                }

				//success
                                $this->response(json_encode('', 200));
                        }
			
			//missing data
                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or toFollow'));
                        $this->response($error, 400);
		}

		public function unfollow() {
			$username = $this->getUsername();
                        $toUnfollow = mysql_real_escape_string($_POST['toUnfollow']);

                        if (isset($username) && isset($toUnfollow)){
				//make sure user is following that person
				$res = mysql_query("SELECT * FROM follows where follower_id='$username' and followee_id='$toUnfollow'");
				if(!$res){
					//not following, error
					$error = json_encode(array('status' => 'Failed', 'msg' => 'User is not following that person'));
                        		$this->response($error, 417);
				}

				//success
                                mysql_query("REMOVE FROM follows where follower_id='$username' and followee_id='$toUnfollow'");
                                $this->response('', 200);
                        }
			
			//missing data
                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing username or toUnfollow'));
                        $this->response($error, 400);
		}
	}

	$api = new Member;
	$api->process();
?>
