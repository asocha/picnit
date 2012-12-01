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
			$cat_name = $this->load('category', false);
			$tag_username = $this->load('tag_username', false);

			$this->forceauth();

			$res = mysql_query("SELECT owner_id FROM images WHERE image_id='$image_id'");
			if($this->memberid != mysql_result($res, 0, owner_id))
				$this->response(json_encode(array('msg' => 'You do not own this image')), 403);

			if($tag_username != "") {
				$res = mysql_query("INSERT INTO mem_tags (member_id,image_id,date_tagged) VALUES ((SELECT member_id FROM members WHERE username='$tag_username'),'$image_id',NOW())");
				if(!$res) {
					$err = mysql_errno();
					if($err == 1048)
						$this->response(json_encode(array('msg' => 'Username does not exist')), 404);
					if($err == 1452)
						$this->response(json_encode(array('msg' => 'Image and/or member do not exist')), 404);
					if($err == 1062)
						$this->response(json_encode(array('msg' => 'User already tagged in image')), 409);

					$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
				}
				$res = mysql_query("SELECT member_id FROM members WHERE username='$tag_username'");
				$msg = json_encode(mysql_fetch_assoc($res));
			}
			else if($cat_name != "") {
HIT_ME_BABY_ONE_MORE_TIME:
				$res = mysql_query("INSERT INTO category_tags (category_id,image_id) VALUES ((SELECT category_id FROM categories WHERE category='$cat_name'),'$image_id')");
				if(!$res) {
					$err = mysql_errno();
					if($err == 1048) {
						mysql_query("INSERT INTO categories (category) VALUES ('$cat_name')");
						goto HIT_ME_BABY_ONE_MORE_TIME;
					}
					if($err == 1452)
						$this->response(json_encode(array('msg' => 'Image and/or member do not exist')), 404);
					if($err == 1062)
						$this->response(json_encode(array('msg' => 'The image already has that category tag')), 409);

					$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
				}
				$res = mysql_query("SELECT category_id FROM categories WHERE category='$cat_name'");
				$msg = json_encode(mysql_fetch_assoc($res));
			}
			$this->response($msg, 200);
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

		public function getCategories() {
			$res = mysql_query("SELECT category FROM categories");

			$i = 0;
			while($row = mysql_fetch_array($res))
				$tosend[$i++] = $row['category'];

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

			$res = mysql_query("SELECT m.member_id, m.username FROM images i,mem_tags t, members m WHERE i.image_id=t.image_id and i.image_id='$image_id' and m.member_id=t.member_id");

			$i = 0;
			while($row = mysql_fetch_array($res)){
				$tosend['member_tags'][$i]['member_id'] = intval($row['member_id']);
				$tosend['member_tags'][$i]['username'] = $row['username'];
				$i++;
			}

			$res = mysql_query("SELECT c.category, c.category_id FROM images i,categories c,category_tags t WHERE i.image_id=t.image_id and i.image_id='$image_id' and c.category_id=t.category_id");

                        $i = 0;
                        while($row = mysql_fetch_array($res)){
                                $tosend['cat_tags'][$i]['category_id'] = intval($row['category_id']);
                                $tosend['cat_tags'][$i]['category'] = $row['category'];
                                $i++;
                        }

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
