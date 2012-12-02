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
			$image_id = $this->load('image_id');
			$comment = $this->load('comment');

			$this->forceauth();

			mysql_query("INSERT INTO comments(commenter_id, image_id, comment_text) values ('$this->memberid', '$image_id', '$comment')");

			$res = mysql_query("SELECT username as commenter, comment_id, comment_text FROM members, comments WHERE members.member_id=comments.commenter_id ORDER BY comment_id desc LIMIT 1");

			$this->response(json_encode(mysql_fetch_array($res)), 200);
		}

		public function deleteComment() {
			$comment_id = $this->load('comment_id');

			$this->forceauth();

			mysql_query("DELETE FROM comments where comment_id='$comment_id' and commenter_id='$this->memberid'");
			$this->response('',200);
		}

		public function getComments(){
			$image_id = $this->load('image_id');
			$num = $this->load('num',false);
			$offset = $this->load('offset',false);

			if($num != "") {
				if($offset != "")
					$limclause = " LIMIT ".$offset.",".$num;
				else
					$limclause = " LIMIT $num";
			} else {
				$limclause = "";
			}

			$res = mysql_query("SELECT comment_id,comment_text,(SELECT username FROM members WHERE member_id=commenter_id) AS commenter FROM comments where image_id='$image_id' ORDER BY comment_id desc$limclause");
			$result = array();
			while($row = mysql_fetch_assoc($res))
				$result[] = $row;

			$this->response(json_encode($result));
		}
	}
	$api = new Comment;
	$api->process();
?>

