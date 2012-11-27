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
				$res = mysql_query("SELECT member_id,is_admin,is_suspended,username,(SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id=member_id and is_accepted=true) AS isfollowing,(SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id=member_id and is_accepted=false) AS requestsent FROM members WHERE member_id='$user_id'");
			else
				$res = mysql_query("SELECT member_id,is_admin,is_suspended,username,(SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id=member_id and is_accepted=true) AS isfollowing,(SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id=member_id and is_accepted=false) AS requestsent FROM members WHERE username='$username'");

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

			if ($user_id == $this->memberid)
				$this->response(json_encode(array('msg' => 'You cannot follow yourself')), 409);

			$res = mysql_query("INSERT INTO follows (follower_id,followee_id,is_accepted) VALUES ('$this->memberid','$user_id',false)");
			if(!$res) {
				$err = mysql_errno();
				if($err == 1452)
					$this->response(json_encode(array('msg' => 'User does not exist')), 404);
				if($err == 1062)
					$this->response(json_encode(array('msg' => 'You are already following/requesting to follow this user')), 409);

				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
			}

			$this->response(json_encode('', 200));
		}

		public function follow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			$res = mysql_query("UPDATE follows SET is_accepted=true WHERE followee_id='$this->memberid' and follower_id='$user_id'");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
			if(mysql_affected_rows($res))
				$this->response('', 200);

			$this->response(json_encode(array('msg' => 'User has not requested you or user does not exist or you aleady follow user')), 409);
		}

		public function unfollow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			$res = mysql_query("DELETE FROM follows where follower_id='$this->memberid' and followee_id='$user_id'");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
			if(mysql_affected_rows($res))
				$this->response('', 200);

			$this->response(json_encode(array('msg' => 'You do not follow user or user does not exist')), 409);
		}

		public function refuseFollow() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			$res = mysql_query("DELETE FROM messages where follower_id='$user_id' and followee_id='$this->memberid' and is_accepted=false");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
			if(mysql_affected_rows($res))
				$this->response('', 200);

			$this->response(json_encode(array('msg' => 'User already follows you or user has not requested you or user does not exist')), 409);

		}

		public function removeFollower() {
			$user_id = $this->load('user_id');

			$this->forceauth();

			mysql_query("DELETE FROM follows where followee_id='$this->memberid' and follower_id='$user_id' and is_accpeted=true");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
			if(mysql_affected_rows($res))
				$this->response('', 200);

			$this->response(json_encode(array('msg' => 'You are not followed by user or user does not exist')), 409);
		}

		public function getFollowers() {
			$this->forceauth();

			$res = mysql_query("SELECT f.follower_id,m.username FROM follows f,members m WHERE f.followee_id='$this->memberid' and f.is_accepted=true and m.member_id=f.follower_id");

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

			$res = mysql_query("SELECT f.followee_id,m.username FROM follows f,members m WHERE f.follower_id='$this->memberid' and f.is_accepted=true and f.followee_id=m.member_id");

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

			$res = mysql_query("SELECT follower_id,(SELECT username from members where member_id=followee_id) AS username FROM follows WHERE followee_id='$this->memberid' and is_accepted=false");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
				$tosend[$i]['user_id'] = $row['follower_id'];
				$tosend[$i]['username'] = $row['username'];
				$i++;
			}

			$this->response(json_encode($tosend), 200);
		}
	}

	$api = new Member;
	$api->process();
?>
