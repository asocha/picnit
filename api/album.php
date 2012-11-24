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

		public function getAlbums() {
			$user_id = $this->load('user_id');

			$res = mysql_query("SELECT album_id FROM albums WHERE owner_id='$user_id'");
			if(!mysql_num_rows($res))
				$this->response('', 204);

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i++] = intval($row['album_id']);
				$i += 1;

			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);
		}

		public function createAlbum() {
			$album_name = $this->load('name');
			$album_description = $this->load('description');

			$this->forceauth();

			$res = mysql_query("INSERT INTO albums (owner_id,date_created,name,description) VALUES ('$this->memberid',NOW(),'$album_name','$description')");
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
			$album_id = $this->load('album_id');

			// Permission checking is done when the client fetches the images
			$res = mysql_query("SELECT image_id FROM images WHERE album_id='$album_id'");

			if(mysql_num_rows($res) == 0)
				$this->response('', 204); // This is actually right - no images, no content

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i++] = intval($row['image_id']);
				$i += 1;

			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);	
		}

	}

	$api = new Album;
	$api->process();
?>
