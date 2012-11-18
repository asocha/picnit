<?php
	//Include API Class
	require_once("API.php");

	class Comment extends API {

		public function __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function addComment() {
			//Get the vars
			$member_id = $_POST['member_id'];
			$image_id = $_POST['image_id'];
			$comment = mysql_real_escape_string($_POST['comment']);

			//Ensure all variables needed are present
			if(isset($member_id) && isset($image_id) && isset($comment)) {
				//Query the db
				mysql_query("INSERT INTO comments values ($member_id, $image_id, '$comment')");
				//Send confirmation
				$this->response('',200);
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing member_id, image_id, or comment'));
			$this->response($error, 400);
		}

		public function deleteComment() {
			//Get the vars
			$comment_id = $_POST['comment_id'];

			//Ensure all variables needed are present
			if(isset($comment_id)) {
				//Query the db
				mysql_query("DELETE FROM comments where comment_id=$comment_id");
				//Send confirmation
				$this->response('',200);
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing comment_id'));
			$this->response($error, 400);
		}

		public function getComments(){
			//Get the vars
			$image_id = $_POST['image_id'];

			//Ensure all variables needed are present
			if(isset($image_id)) {
				//Query the db
				$res = mysql_query("SELECT comment_text FROM comments where image_id=$image_id");
				$result = mysql_fetch_array($res);

				//Send the confirmation!
				$this->response(json_encode($result));
			}

			//Missing input, send response
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing image_id'));
			$this->response($error, 400);
		}

	}
	$api = new Comment;
	$api->process();
?>

