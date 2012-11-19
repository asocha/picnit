<?php
	//Include API Class
	require_once("API.php");

	class Album extends API {

		public function __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function createAlbum()
		{
			if ($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate to create albums'));
				$this->response($error, 401);
			}

			$album_name = mysql_real_escape_string($_POST['name']);

			if(isset($album_name)) {
				$res = mysql_query("INSERT INTO albums (owner_id,date_created,name) VALUES ('$this->memberid',NOW(),'$album_name')", $this->link);
				if(!$res) {
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error - try again'));
					$this->response($error, 503);
				}
				$error = json_encode(array('status' => 'Failed', 'msg' => 'This is not implemented yet!'));
				$this->response($error, 501);
			}

			$error = json_encode(array('status' => 'Failed', 'msg' => 'Your request is bad, and you should feel bad'));
			$this->response($error, 400);
		}
		
		public function deleteAlbum()
		{
			$error = json_encode(array('status' => 'Failed', 'msg' => 'This is not implemented yet!'));
			$this->response($error, 501);
		}
		
		public function getImages()
		{
			if ($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate to get images from albums, for now'));
				$this->response($error, 401);
			}

			$albumid = mysql_real_escape_string($_POST['id']);

			if(isset($albumid)) {
				$res = mysql_query("SELECT image_id FROM images WHERE album_id='$id'");
				if(!$res) {
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error - try again'));
					$this->response($error, 503);
				}
				if(mysql_num_rows($res) == 0) {
					$error = json_encode(array('status' => 'Success', 'msg' => 'Album is empty'));
					$this->response($error, 204);
				}
				
				while($row = mysql_fetch_array($res)) {
					echo $row['image_id'];
				}

				$this->response('', 200);
			}

			$error = json_encode(array('status' => 'Failed', 'msg' => 'Your request is bad, and you should feel bad'));
			$this->response($error, 400);
		
		}
	}
	$api = new Album;
	$api->process();
	}
?>
