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

			$res = mysql_query("SELECT *  FROM albums WHERE owner_id='$user_id'");
			if(!mysql_num_rows($res))
				$this->response('', 204);

			$i = 0;
			while($row = mysql_fetch_assoc($res)) {
				$tosend[$i]['album_id'] = intval($row['album_id']);
				$tosend[$i]['owner_id'] = intval($row['owner_id']);
				$tosend[$i]['date_created'] = $row['date_created'];
				$tosend[$i]['name'] = $row['name'];
				$tosend[$i]['description'] = $row['description'];
				$i++;
			}

			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);
		}

		public function createAlbum() {
			$album_name = $this->load('name');
			$album_description = $this->load('description');

			$this->forceauth();

			$res = mysql_query("INSERT INTO albums (owner_id,date_created,name,description) VALUES ('$this->memberid',NOW(),'$album_name','$album_description')");
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

		public function albumData() {
			$album_id = $this->load('album_id');

			$res = mysql_query("SELECT album_id,owner_id,date_created,name,description FROM albums WHERE album_id='$album_id'");

			if(!mysql_num_rows($res))
				$this->response(json_encode(array('msg' => 'Album does not exist')), 404);

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i++] = intval($row['album_id']);
				$i += 1;

			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);

		}

		public function getLastAlbumImages() {
			$num = $this->load('num');
			$album_id = $this->load('album_id');
			$id = $this->load('id', false);

			if($num > 10)
				$num = 10;

			if($id != "") {
				if($this->memberid == $id)
					$res = mysql_query("SELECT image_id FROM images WHERE owner_id='$id' && album_id='$album_id' ORDER BY image_id DESC LIMIT $num");

				if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$id'")))
					$res = mysql_query("SELECT image_id FROM images WHERE owner_id='$id' and publicness < 2 and album_id='$album_id' ORDER BY image_id DESC LIMIT $num");

				$res = mysql_query("SELECT image_id FROM images WHERE owner_id='$id' and publicness='0' and album_id='$album_id' ORDER BY image_id DESC LIMIT $num");
			} else {
				$res = mysql_query("SELECT image_id FROM images WHERE publicness='0' and album_id='$album_id' ORDER BY image_id DESC LIMIT $num");
			}

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
