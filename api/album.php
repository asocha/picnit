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

			$this->response(json_encode(array('list' => $tosend)), 200);
		}

		public function createAlbum() {
			$album_name = $this->load('name');
			$album_description = $this->load('description', false);

			if ($album_description == null)
				$album_description = "";

			$this->forceauth();

			$res = mysql_query("INSERT INTO albums (owner_id,date_created,name,description) VALUES ('$this->memberid',NOW(),'$album_name','$album_description')");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);

			$this->response('', 200);
		}

		public function deleteAlbum() {
			$album_id = $this->load('album_id');

			$this->forceauth();

			$res = mysql_query("SELECT is_admin from members WHERE member_id='$this->memberid'");
			$is_admin = mysql_result($res, 0);

			if (!$is_admin)
				$res = mysql_query("SELECT filepath FROM images WHERE album_id='$album_id' and owner_id='$this->memberid'");
			else
				$res = mysql_query("SELECT filepath FROM images WHERE album_id='$album_id'");

			while($row = mysql_fetch_array($res))
				unlink("/var/www/picnit/images/user".$row['filepath']);

			if (!$is_admin)
				$res = mysql_query("DELETE FROM albums WHERE album_id='$album_id' and owner_id='$this->memberid'");
			else
				$res = mysql_query("DELETE FROM albums WHERE album_id='$album_id'");
			if(mysql_affected_rows())
				$this->response(json_encode(array('msg' => 'Deletion was succesfully performed')), 200);

			$this->response(json_encode(array('msg' => 'Album was not deleted. Does it exist? Do you own it? Do you exist?')), 469);
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
			$this->response(json_encode(array('list' => $tosend)), 200);

		}

		public function numImages() {
			$album_id = $this->load('album_id');

			$res = mysql_query("SELECT count(*) FROM images WHERE album_id='$album_id'");

			$array = mysql_fetch_array($res);
			$this->response(json_encode(array('num' => $array[0])), 200);
		}

		public function getAlbumPrefix() {
			$prefix = $this->load('prefix', false);

			$res = mysql_query("SELECT album_name,album_id from albums where album_name LIKE '$prefix%'");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
				$tosend[$i]['name'] = $row['album_name'];
				$tosend[$i]['id'] = $row['album_id'];
				$i++;
			}

			$this->response(json_encode($tosend), 200);
		}

	}

	$api = new Album;
	$api->process();
?>
