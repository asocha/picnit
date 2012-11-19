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
			$image_id = $this->load($_POST['image_id']);
			$comment = $this->load($_POST['comment']);

			// Verify that user has authenticated before proceeding
			if($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate'));
				$this->response($error, 403);
			}

			mysql_query("INSERT INTO comments values ('$this->memberid', '$image_id', '$comment')");
			$this->response('',200);
		}

		public function deleteComment() {
			$comment_id = $this->load($_POST['comment_id']);

			// Verify that user has authenticated before proceeding
			if($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate'));
				$this->response($error, 403);
			}

			mysql_query("DELETE FROM comments where comment_id='$comment_id' and commenter_id='$this->memberid'");
			$this->response('',200);
		}

		public function getComments(){
			$image_id = $this->load($_POST['image_id']);

			$res = mysql_query("SELECT comment_text FROM comments where image_id='$image_id'");
			$result = mysql_fetch_array($res);

			$this->response(json_encode($result));
		}
	}
	$api = new Comment;
	$api->process();
?>

