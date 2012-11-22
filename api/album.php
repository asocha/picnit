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

		public function createAlbum() {
			if ($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate to create albums'));
				$this->response($error, 401);
			}

			$album_name = $this->load($_POST['name']);

			$res = mysql_query("INSERT INTO albums (owner_id,date_created,name) VALUES ('$this->memberid',NOW(),'$album_name')", $this->link);
			if(!$res) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error - try again'));
				$this->response($error, 503);
			}

			$this->response('', 200);
		}

		public function deleteAlbum() {
			$error = json_encode(array('status' => 'Failed', 'msg' => 'This is not implemented yet!'));
			$this->response($error, 501);
		}

		public function getImages() {
			if ($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate to get images from albums, for now'));
				$this->response($error, 401);
			}

			$albumid = $this->load($_POST['id']);

			// Permission checking is done when the client fetches the images
			$res = mysql_query("SELECT image_id, link FROM images WHERE album_id='$id'");
			if(!$res) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error - try again'));
				$this->response($error, 503);
			}

			if(mysql_num_rows($res) == 0) {
				$this->response('', 204); // This is actually right - no images, no content
			}

			$rows = mysql_fetch_array($res);
			$this->response(json_encode($rows), 200);
		}

	}

	$api = new Album;
	$api->process();
?>
