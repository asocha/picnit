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
			$name = $this->load('name');
			$num = $this->load('num');

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT * FROM images WHERE name LIKE '$name%'");

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

			$this->response(json_encode(array('list' => $tosend)), 200);
		}

		public function getAlbumsByName() {
			$text = $this->load('name');
			$num = $this->load('num');

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT * FROM albums WHERE name LIKE '$name%'");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
					$tosend[$i]['album_id'] = intval($row['image_id']);
					$tosend[$i]['owner_id'] = $row['imgtype'];
					$tosend[$i]['date_added'] = $row['date_added'];
					$tosend[$i]['name'] = $row['name'];
					$tosend[$i]['description'] = $row['description'];
					$i++;
			}

			$this->response(json_encode(array('list' => $tosend)), 200);
		}

		public function filterMembers() {
			$name = $this->load('name');

			$res = mysql_query("SELECT member_id FROM members WHERE username='$name'");

			$i = 0;
			while($row = mysql_fetch_array($res)){
				$tosend[$i++] = intval($row['member_id']);
				$i += 1;
			}

			$this->response(json_encode(array('list' => $tosend)), 200);
		}

		public function filterImages() {
			$num = $this->load('num');
			$text = $this->load('text');

			$res = mysql_query("SELECT image_id, imgtype, date_added, name, description, filepath FROM images LEFT JOIN follows ON owner_id=followee_id NATURAL LEFT JOIN category_tags NATURAL LEFT JOIN categories WHERE ((publicness=0) OR (publicness=1 and follower_id='$this->memberid' and is_accepted=true) OR (owner_id='$this->memberid')) and (name='$text' or category='$text') GROUP BY image_id ORDER BY image_id DESC LIMIT $num");

			$i = 0;
			while($row = mysql_fetch_array($res)) {
				$tosend[$i]['image_id'] = intval($row['image_id']);
				$tosend[$i]['image_type'] = $row['imgtype'];
				$tosend[$i]['date_added'] = $row['date_added'];
				$tosend[$i]['name'] = $row['name'];
				$tosend[$i]['description'] = $row['description'];
				$tosend[$i]['image'] = base64_encode(file_get_contents("/var/www/picnit/images/user".$row['filepath']));
				$i+=1;
			}

			$this->response(json_encode(array('list' => $tosend)), 200);
		}
	}

	$api = new Search;
	$api->process();
?>
