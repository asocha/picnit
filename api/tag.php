<?php
	//Include API Class
	require_once("API.php");

	class Tag extends API {

		public function __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function addTag() {
			$image_id = $this->load('image_id');
			$cat_id = $this->load('cat_id', false); // If category
			$user_id = $this->load('user_id', false); // If member

			$this->forceauth();

			$res = mysql_query("SELECT owner_id FROM images WHERE image_id='$image_id'");
			if($this->memberid != mysql_result($res, 0, owner_id))
				$this->response(json_encode(array('msg' => 'You do not own this image')), 403);

			if($tag != "") { // Add category tag
				$res = mysql_query("INSERT INTO category_tags VALUES ($image_id, $cat_id)");
				if(!$res) {
					$err = mysql_errno();
					if($err == 1452)
						$this->response(json_encode(array('msg' => 'Image and/or tag do not exist')), 404);
					if($err == 1062)
						$this->response(json_encode(array('msg' => 'Image already has specified tag')), 409);

					$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
				}
			}
			else if($user_id != "") { // Add member tag
				$res = mysql_query("INSERT INTO mem_tags VALUES ($user_id, $image_id, NOW())");
				if(!$res) {
					$err = mysql_errno();
					if($err == 1452)
						$this->response(json_encode(array('msg' => 'Image and/or member do not exist')), 404);
					if($err == 1062)
						$this->response(json_encode(array('msg' => 'User already tagged in image')), 409);

					$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
				}
			}

			$this->response('', 200);
		}
		public function deleteTag() {
			$image_id = $this->load('image_id');
			$cat_id = $this->load('cat_id', false); // If category
			$user_id = $this->load('user_id', false); // If member

			$this->forceauth();

			$res = mysql_query("SELECT owner_id FROM images WHERE image_id='$image_id'");
			if($this->memberid != mysql_result($res, 0, owner_id) && $this->memberid != $user_id)
				$this->response(json_encode(array('msg' => 'You do not own this image')), 403);

			if($user_id != "") // Delete member tag
				$res = mysql_query("DELETE FROM mem_tags where image_id=$image_id and member_id=$user_id");
			else if($tag != "") // Delete category tag
				$res = mysql_query("DELETE FROM category_tags WHERE image_id='$image_id' and category_tag='$cat_id'");

			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);

			$this->response('', 200);
		}

		public function addFavorite() {
			$image_id = $this->load('image_id');

			$this->forceauth();

			$res = mysql_query("INSERT INTO favorites (image_id,member_id) VALUES ($image_id, $this->memberid)");
			if(!$res) {
				$err = mysql_errno();
				if($err == 1452)
					$this->response(json_encode(array('msg' => 'Image does not exist')), 404);
				if($err == 1062)
					$this->response(json_encode(array('msg' => 'You already favorited this image')), 409);

				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
			}

			$this->response('', 200);
		}

		public function deleteFavorite() {
			$image_id = $this->load('image_id');

			$this->forceauth();

			$res = mysql_query("DELETE FROM favorites where image_id=$image_id and member_id=$this->memberid");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);

			$this->response(json_encode('', 200));
		}

		public function getFavorites() {
			$this->forceauth();

			$res = mysql_query("SELECT * FROM favorites NATURAL JOIN images WHERE member_id='$this->memberid'");
			if(!mysql_num_rows($res))
				$this->response('', 204); // User has no favorites

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

			$this->response(json_encode($tosend), 200);
		}

		public function getCategories() {
			$res = mysql_query("SELECT category FROM categories");

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i++] = $row['category'];

			$this->response(json_encode($tosend), 200);
		}

		public function getCategoryTaggedImages() {
			$cat_id = $this->load('cat_id');

			$res = mysql_query("SELECT i.image_id,i.image_type,i.date_added,i.name,i.description FROM category_tags c,images i,follows f WHERE c.category_id='$cat_id' and c.image_id=i.image_id and ((publicness=0) or (i.owner_id=f.followee_id and f.follower_id='$this->memberid' and f.is_accepted=true and publicness < 2) or (i.owner_id='$this->memberid'))");

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

			$this->response(json_encode($tosend), 200);
		}

		public function getUserTaggedImages() {
			$cat_id = $this->load('user_id');

			$res = mysql_query("SELECT i.image_id,i.image_type,i.date_added,i.name,i.description FROM member_tags m,images i,follows f WHERE m.member_id='$user_id' and m.image_id=i.image_id and ((publicness=0) or (i.owner_id=f.followee_id and f.follower_id='$this->memberid' and f.is_accepted=true and publicness < 2) or (i.owner_id='$this->memberid'))");

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

			$this->response(json_encode($tosend), 200);
		}

		public function getLastCategoryTaggedImages() {
			$cat_id = $this->load('cat_id');
			$num = $this->load('num');

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT i.image_id,i.image_type,i.date_added,i.name,i.description FROM category_tags c,images i,follows f WHERE c.category_id='$cat_id' and c.image_id=i.image_id and ((publicness=0) or (i.owner_id=f.followee_id and f.follower_id='$this->memberid' and f.is_accepted=true and publicness < 2) or (i.owner_id='$this->memberid')) LIMIT $num");

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

			$this->response(json_encode($tosend), 200);
		}

		public function getLastUserTaggedImages() {
			$cat_id = $this->load('user_id');
			$num = $this->load('num');

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT i.image_id,i.image_type,i.date_added,i.name,i.description FROM member_tags m,images i,follows f WHERE m.member_id='$user_id' and m.image_id=i.image_id and ((publicness=0) or (i.owner_id=f.followee_id and f.follower_id='$this->memberid' and f.is_accepted=true and publicness < 2) or (i.owner_id='$this->memberid')) LIMIT $num");

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

			$this->response(json_encode($tosend), 200);
		}

		public function getTopCategories() {
			$num = $this->load('num');
			$user_id = $this->load('user_id',false);

			if($num > 10)
				$num = 10;

			$res = mysql_query("SELECT category_id,(SELECT COUNT(*)) as count FROM category_tags ORDER BY count DESC LIMIT $num");

			$i = 0;
			while($row = mysql_fetch_array($res))
					$tosend[$i++] = intval($row['category_id']);

			$this->response(json_encode($tosend), 200);

		}

		public function getTagsByImage() {
			$image_id = $this->load('image_id');

			$res = mysql_query("SELECT t.member_id FROM images i,tags t WHERE i.image_id=t.image_id");

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i++] = intval($row['member_id']);

			$this->response(json_encode($tosend), 200);
		}

		public function getUserTags() {
			$prefix = $this->load('prefix', false);

			$res = mysql_query("SELECT username, member_id from members where username LIKE '$prefix%'");

			$i = 0;
                        while($row = mysql_fetch_array($res)) {
                        	$tosend[$i]['username'] = $row['username'];
				$tosend[$i]['member_id'] = $row['member_id'];
				$i++;
			}

                        $this->response(json_encode($tosend), 200);
		}

		public function getCategoryTags() {
                        $prefix = $this->load('prefix', false);

                        $res = mysql_query("SELECT category, category_id from categories where category LIKE '$prefix%'");

                        $i = 0;
                        while($row = mysql_fetch_array($res)) {
                                $tosend[$i]['category'] = $row['category'];
				$tosend[$i]['category_id'] = $row['category_id'];
				$i++;
			}

                        $this->response(json_encode($tosend), 200);
                }
	}

	$api = new Tag;
	$api->process();
?>
