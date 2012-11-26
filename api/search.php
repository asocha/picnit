<?php
	//Include API Class
	require_once("API.php");

	class Search extends API {

		public function __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function getImagesByName() {
			$text = $this->load('name');
			$num = $this->load('num');

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT * FROM images WHERE name LIKE '%$name%'");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
					$tosend[$i]['image_id'] = intval($row['image_id']);
					$tosend[$i]['image_type'] = $row['imgtype'];
					$tosend[$i]['date_added'] = $row['date_added'];
					$tosend[$i]['name'] = $row['name'];
					$tosend[$i]['description'] = $row['description'];
					$tosend[$i]['image'] = base64_encode(file_get_contents("/var/www/picnit/images/user".$row['filepath']));
					$i++;
			}
		}

		public function getAlbumsByName() {
			$text = $this->load('name');
			$num = $this->load('num');

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT * FROM albums WHERE name LIKE '%$name%'");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
					$tosend[$i]['album_id'] = intval($row['image_id']);
					$tosend[$i]['owner_id'] = $row['imgtype'];
					$tosend[$i]['date_added'] = $row['date_added'];
					$tosend[$i]['name'] = $row['name'];
					$tosend[$i]['description'] = $row['description'];
					$i++;
			}
		}
	}

	$api = new Search;
	$api->process();
?>
