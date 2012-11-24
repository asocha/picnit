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

			$album_name = $this->load('name');

			$res = mysql_query("INSERT INTO albums (owner_id,date_created,name) VALUES ('$this->memberid',NOW(),'$album_name')");
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

			$album_id = $this->load('album_id');

			// Permission checking is done when the client fetches the images
			$res = mysql_query("SELECT image_id FROM images WHERE album_id='$album_id'");

			if(mysql_num_rows($res) == 0) {
				$this->response('', 204); // This is actually right - no images, no content
			}

			$row = mysql_fetch_array($res);
			$csv = $row['image_id'];
			while($row = mysql_fetch_array($res))
				$csv = $csv.','.$row['image_id'];

			$this->response(json_encode(array('status' => 'Success', 'list' => $csv)), 200);
		}

	}

	$api = new Album;
	$api->process();
?>
