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

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT album_id, image_id, username, name, description, date_added FROM images, members WHERE name LIKE '$name%' and members.member_id = images.owner_id ORDER BY image_id DESC$limclause");

			while($row = mysql_fetch_array($res)) 
				$tosend[] = $row;

			$this->response(json_encode($tosend), 200);
		}

		public function getAlbumsByName() {
			$text = $this->load('name');
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

			$res = mysql_query("SELECT album_id, username, date_created, name, description FROM albums, members WHERE name LIKE '$text%' and albums.owner_id = members.member_id ORDER BY album_id DESC$limclause");

			while($row = mysql_fetch_array($res)) 
				$tosend[] = $row;

			$this->response(json_encode($tosend), 200);
		}

		public function filterMembers() {
			$name = $this->load('name');

			$res = mysql_query("SELECT member_id, username FROM members WHERE username LIKE '$name%'");

			while($row = mysql_fetch_array($res))
				$tosend[] = $row;

			$this->response(json_encode($tosend), 200);
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

		public function getImagesByCategory() {
			$name = $this->load('name');
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

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT i.* FROM images i, categories c, category_tags t WHERE i.image_id=t.image_id and t.category_id=c.category_id and c.category LIKE '$name%' ORDER BY image_id DESC$limclause");

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
	}

	$api = new Search;
	$api->process();
?>
