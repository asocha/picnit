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

			$res = mysql_query("SELECT * FROM albums WHERE owner_id='$user_id'");
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
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);

			$this->response('', 200);
		}

		public function deleteAlbum() {
			$error = json_encode(array('status' => 'Failed', 'msg' => 'This is not implemented yet!'));
			$this->response($error, 501);
		}

		public function getImages() {
			$album_id = $this->load('album_id');

			$res = mysql_query("SELECT * FROM images WHERE album_id='$album_id'");

			if(!mysql_num_rows($res))
				$this->response('', 204); // This is actually right - no images, no content

			$row = mysql_fetch_array($res);
			$alb_owner = $row['owner_id'];

			if($this->memberid == $alb_owner)
				$cutoff = 2;
			else if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$alb_owner'")))
				$cutoff = 1;
			else
				$cutoff = 0;

			$i = 0;
			do {
				if($row['publicness'] <= $cutoff) {
					$tosend[$i]['image_id'] = intval($row['image_id']);
					$tosend[$i]['image_type'] = $row['imgtype'];
					$tosend[$i]['date_added'] = $row['date_added'];
					$tosend[$i]['name'] = $row['name'];
					$tosend[$i]['description'] = $row['description'];
					$tosend[$i]['image'] = base64_encode(file_get_contents("/var/www/picnit/images/user".$row['filepath']));
					$i++;
				}
			} while($row = mysql_fetch_array($res));
			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);
		}

		public function albumData() {
			$album_id = $this->load('album_id');

			$res = mysql_query("SELECT album_id,owner_id,date_created,name,description FROM albums WHERE album_id='$album_id'");

			if(!mysql_num_rows($res))
				$this->response(json_encode(array('msg' => 'Album does not exist')), 404);

			$i = 0;
			while($row = mysql_fetch_array($res)) {
				$tosend[$i]['album_id'] = intval($row['album_id']);
				$tosend[$i]['owner_id'] = $row['owner_id'];
				$tosend[$i]['date_created'] = $row['date_created'];
				$tosend[$i]['name'] = $row['name'];
				$tosend[$i]['description'] = $row['description'];
				$i++;
			}
			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);

		}

		public function getLastAlbumImages() {
			$num = $this->load('num');
			$album_id = $this->load('album_id');	

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT * FROM images WHERE album_id='$album_id' ORDER BY image_id DESC LIMIT $num");
			if(!mysql_num_rows($res))
				$this->response('', 204);

			$row = mysql_fetch_array($res);

			$alb_owner = $row['owner_id'];
			if($this->memberid == $alb_owner)
				$cutoff = 2;
			else if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$alb_owner'")))
				$cutoff = 1;
			else
				$cutoff = 0;

			$i = 0;
			do {
				if($row['publicness'] <= $cutoff) {
					$tosend[$i]['image_id'] = intval($row['image_id']);
					$tosend[$i]['image_type'] = $row['imgtype'];
					$tosend[$i]['date_added'] = $row['date_added'];
					$tosend[$i]['name'] = $row['name'];
					$tosend[$i]['description'] = $row['description'];
					$tosend[$i]['image'] = base64_encode(file_get_contents("/var/www/picnit/images/user".$row['filepath']));
					$i++;
				}
			} while($row = mysql_fetch_array($res));
			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);
		}
	}

	$api = new Album;
	$api->process();
?>
