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
			$username = $this->load('username');
			$password = $this->getHashedPassword($username);

			$res = mysql_query("SELECT member_id, username, is_suspended, password FROM members where username='$username' and password='$password' LIMIT 1");

			if(mysql_num_rows($res) > 0) {
				$result = mysql_fetch_array($res, MYSQL_ASSOC);

				// Rename password to key
				$result['key'] = $result['password'];
				unset($result['password']);

				$this->response(json_encode($result), 200);
			}

			//Not found, return missing content
			$this->response(json_encode(array('msg' => 'Invalid username or password')), 403);
		}

		public function register() {
			$username = $this->load('username');
			$password = $this->load('password');
			$email = $this->load('email');
			$salt = mt_rand();

			// Hash the password
			$hashedpass = sha1($password.$salt);
			$result = mysql_query("INSERT INTO members (is_admin,is_suspended,username,password,salt,email) VALUES ('false','false','$username','$hashedpass','$salt','$email')");

			// Make sure query works
			if(!$result) {
				$err = mysql_errno();
				if($err == 1062) {
					// Username or E-Mail is already in use - figure out which
					$errstr = mysql_error();

					if(strstr($errstr, "username")) {
						$error = json_encode(array('msg' => 'Username already in use'));
					} else {
						$error = json_encode(array('msg' => 'E-Mail already in use'));
					}

					$this->response($error, 409);
				}

				// Something else went wrong
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
				$this->response($error, 500);
			}

			// Successful creation
			$msg = json_encode(array('status' => "Success"));
			$this->response($msg, 200);
		}

		public function deleteAccount() {
			$this->forceauth();

			mysql_query("DELETE FROM members where member_id=$this->memberid");
			$this->response('',200);
		}

		public function suspendUser() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			// Only admins can suspend people
			$res = mysql_query("SELECT is_admin FROM members where member_id='$this->memberid'");
			$array = mysql_fetch_array($res);
			$admin = $array['is_admin'];
			if(!$admin){
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You are not an admin'));
				$this->response($error, 403);
			}

			// Check if member to suspend exists and isn't suspended
			$res = mysql_query("SELECT is_suspended FROM members where member_id='$user_id'");
			$array = mysql_fetch_array($res);
			$suspended = $array['is_suspended'];

			if((mysql_num_rows($res) < 1) || ($suspended == 1)) {
				// Member doesn't exist or is already suspended
				$error = json_encode(array('status' => 'Failed', 'msg' => 'User does not exist or is already suspended'));
				$this->response($error, 409);
			} else {
				mysql_query("UPDATE members SET is_suspended = 1 where member_id='$user_id'");
				$this->response('',200);
			}
		}

		public function unsuspendUser() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			// Only admins can unsuspend people
			$res = mysql_query("SELECT is_admin FROM members where member_id='$this->memberid'");
			$array = mysql_fetch_array($res);
			$admin = $array['is_admin'];
			if(!$admin){
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You are not an admin'));
				$this->response($error, 403);
			}

			// Check if member to suspend exists and is suspended
			$res = mysql_query("SELECT is_suspended FROM members where member_id='$user_id'");
			$array = mysql_fetch_array($res);
			$suspended = $array['is_suspended'];

			if((mysql_num_rows($res) < 1) || (!$suspended)) {
				// Member doesn't exist or is already suspended
				$error = json_encode(array('status' => 'Failed', 'msg' => 'User does not exist or is not suspended'));
				$this->response($error, 409);
			} else {
				mysql_query("UPDATE members SET is_suspended = 0 where member_id='$user_id'");
				$this->response('',200);
			}
		}

		public function memberData() {
			$username = $this->load('tusername', false);
			$user_id = $this->load('user_id', false);

			if ($user_id == "" && $username == "")
				$this->response(json_encode(array('msg' => 'Missing data')), 400);
			else if ($user_id != "")
				$res = mysql_query("SELECT member_id,is_admin,is_suspended,username,(SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id=member_id) AS isfollowing,(SELECT member_id FROM messages WHERE from_id='$this->memberid' and to_id=member_id) AS requestsent FROM members WHERE member_id='$user_id'");
			else
				$res = mysql_query("SELECT member_id,is_admin,is_suspended,username,(SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id=member_id) AS isfollowing,(SELECT member_id FROM messages WHERE from_id='$this->memberid' and to_id=member_id) AS requestsent FROM members WHERE username='$username'");

			if(mysql_num_rows($res) < 1)
				$this->response(json_encode(array('msg' => 'User does not exist')), 404);

			$array = mysql_fetch_assoc($res);
			$array['isfollowing'] ? $array['isfollowing'] = true : $array['isfollowing'] = false;
			$array['requestsent'] ? $array['requestsent'] = true : $array['requestsent'] = false;
			$this->response(json_encode($array), 200);
		}

		public function requestFollow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			// Make sure user is not trying to follow himself/herself
			if ($user_id == $this->memberid){
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You cannot follow yourself'));
				$this->response($error, 417);
			}

			// Make sure user to follow exists
			$res = mysql_query("SELECT * FROM members where member_id='$user_id'");
			if(mysql_num_rows($res) == 0) {
				// User does not exist
				$error = json_encode(array('status' => 'Failed', 'msg' => 'The user you are trying to follow does not seem to exist'));
				$this->response($error, 417);
			}

			// Check if user is already following that person
			$res = mysql_query("SELECT * FROM follows where follower_id='$this->memberid' and followee_id='$user_id'");
			if(mysql_num_rows($res)) {
				// User is already following that person
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You are already following that user'));
				$this->response($error, 417);
			}

			// Check if already follow requested that person
			$res = mysql_query("SELECT * FROM messages where from_id='$this->memberid' and to_id='$user_id' and message_type=0");
			if(mysql_num_rows($res)) {
				// User already follow requested that person
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You have already requested to follow that user'));
				$this->response($error, 417);
			}

			// Add message of type 0 --> REQUEST TO FOLLOW
			$res = mysql_query("INSERT INTO messages (from_id, to_id, message_type, is_read, message) VALUES ('$this->memberid', '$user_id', '0', 'false', '')");

			if(!$res) {
				// Something else went wrong
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
				$this->response($error, 500);
			}

			// Success
			$this->response(json_encode('', 200));
		}

		public function follow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			$res = mysql_query("SELECT message_id FROM messages where from_id='$user_id' and to_id='$this->memberid' and message_type='0'");
			if(!mysql_num_rows($res)) {
				// Has not sent follow request
				$error = json_encode(array('status' => 'Failed', 'msg' => 'User did not request to follow you'));
				$this->response($error, 417);
			}

			// Delete follow request
			$message_id = mysql_result($res, 0, message_id);
			mysql_query("DELETE FROM messages WHERE message_id='$message_id'");

			// Implement Follow
			$res = mysql_query("INSERT INTO follows VALUES ('$user_id', '$this->memberid')");
			if(!$res) {
				//Get error
				$err = mysql_errno();

				if($err == 1062) {
					// Person is already following the user
					$error = json_encode(array('status' => 'Failed', 'msg' => 'That person is already following you'));
					$this->response($error, 409);
				}

				//Something else went wrong
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
				$this->response($error, 500);
			}

			// Success
			$this->response(json_encode('', 200));
		}

		public function unfollow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			mysql_query("DELETE FROM follows where follower_id='$this->memberid' and followee_id='$user_id'");
			$this->response('', 200);
		}

		public function refuseFollow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			$res = mysql_query("DELETE FROM messages where from_id='$user_id' and to_id='$this->memberid' and message_type='0'");

			// Success
			$this->response(json_encode('', 200));
		}

		public function removeFollower() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			mysql_query("DELETE FROM follows where followee_id='$this->memberid' and follower_id='$user_id'");
			$this->response('', 200);
		}

		public function getFollowers() {
			$this->forceauth();

			$res = mysql_query("SELECT f.follower_id,m.username FROM follows f,members m WHERE f.followee_id='$this->memberid' and m.member_id=f.follower_id");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
				$tosend[$i]['user_id'] = $row['follower_id'];
				$tosend[$i]['username'] = $row['username'];
				$i++;
			}

			$this->response(json_encode($tosend), 200);
		}

		public function getFollowees() {
			$this->forceauth();

			$res = mysql_query("SELECT f.followee_id,m.username FROM follows f,members m WHERE f.follower_id='$this->memberid' and f.followee_id=m.member_id");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
				$tosend[$i]['user_id'] = $row['followee_id'];
				$tosend[$i]['username'] = $row['username'];
				$i++;
			}

			$this->response(json_encode($tosend), 200);
		}

		public function getFollowRequests() {
			$this->forceauth();

			$res = mysql_query("SELECT mes.from_id, mem.username FROM messages mes, members mem WHERE mes.message_type=0 and mes.to_id='$this->memberid' and mes.from_id=mem.member_id");

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i]['user_id'] = $row['from_id'];
				$tosend[$i]['username'] = $row['username'];
				$i++;

			$this->response(json_encode($tosend), 200);
		}
	}

	$api = new Member;
	$api->process();
?>
