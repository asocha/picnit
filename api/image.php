<?php
	//Include API Class
	require_once("API.php");

	class Image extends API {

		public function __construct() {
			//Parent Constructor
			parent::__construct();

			//Connect to the database
			$this->link = $this->db_connect();
		}

		public function getImage() {
			$image_id = $this->load('image_id');

			$res = mysql_query("SELECT * FROM images WHERE image_id='$image_id'");
			if(!mysql_num_rows($res))
				$this->response(json_encode(array('msg' => 'Image does not exist')), 404);

			$row = mysql_fetch_assoc($res);
			$owner_id = $row['owner_id'];

			if($row['publicness'] == 0)
				goto allow_user_access;

			if($this->memberid == $row['owner_id'])
				goto allow_user_access;

			if($row['publicness'] == 1)
				if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$owner_id")))
					goto allow_user_access;

			$this->response(json_encode(array('msg' => 'You are not permitted to access this image')), 403);
allow_user_access:
			$row['image'] = base64_encode(file_get_contents("/var/www/picnit/images/user".$row['filepath']));
			unset($row['filepath']);
			$this->response(json_encode($row), 200);
		}

		public function saveImage() {
			$album_id = $this->load('album_id');
			$publicness = $this->load('publicness');
			$phototype = $this->load('phototype');
			$name = $this->load('name');
			$description = $this->load('description');
			$photo = base64_decode($_POST['photo']);

			if(strlen($phototype) != 3)
				$this->response('', 400);

			$this->forceauth();

			// Verify that the album exists, and the user owns it
			$result = mysql_query("SELECT owner_id FROM albums WHERE album_id='$album_id'");
			if(!mysql_num_rows($result))
				$this->response('', 404);

			if(mysql_result($result, 0, owner_id) != $this->memberid)
				$this->response('', 403);

get_new_file_path:
			// Path is stored in the form "/xxxx/xxxx/xxxx/xxxx/xxxx/xx.ext"
			$dir1 = mt_rand(0,9999);
			$dir2 = mt_rand(0,9999);
			$dir3 = mt_rand(0,9999);
			$dir4 = mt_rand(0,9999);
			$dir5 = mt_rand(0,9999);
			$file = mt_rand(0,99);
			$filepath = "/".$dir1."/".$dir2."/".$dir3."/".$dir4."/".$dir5."/".$file.".$phototype";
			$fullpath = "/var/www/picnit/images/user".$filepath;

			if(file_exists($fullpath))
				goto get_new_file_path;

			$tmppath = "/tmp/".mt_rand();
			$fh = fopen($tmppath, 'w+');
			fwrite($fh, $photo);
			fclose($fh);
			$type = mime_content_type($tmppath);

			if(!strstr($type, "image"))
				$this->response(json_encode(array('msg' => 'Image file format not supported: '.$type)),415);

			mkdir("/var/www/picnit/images/user/".$dir1."/".$dir2."/".$dir3."/".$dir4."/".$dir5, 0775, true);
			link($tmppath,$fullpath);
			unlink($tmppath);
			chmod($fullpath, 0664);

			mysql_query("INSERT INTO images (album_id,publicness,filepath,date_added,name,description,imgtype,owner_id) VALUES ('$album_id','$publicness', '$filepath', NOW(), '$name', '$description', '$type', '$this->memberid')");
			$this->response('',200);
		}

		public function deleteImage() {
			$image_id = $this->load('image_id');

			$this->forceauth();

			$res = mysql_query("SELECT filepath,album_id,owner_id from images where image_id=$image_id");
			if(!mysql_num_rows($res)) {
				// Image does not exist
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not exist'));
				$this->response($error, 409);
			}

			$filepath = mysql_result($res, 0, filepath);
			$album_id = mysql_result($res, 0, album_id);
			$owner_id = mysql_result($res, 0, owner_id);

			if($this->memberid != $owner_id)
				$this->response('', 403);

			mysql_query("DELETE FROM images where image_id='$image_id'");
			$array = mysql_fetch_array($res);
			$filepath = $array['filepath'];
			unlink("/var/www/picnit/images/user".$filepath);
			$this->response('',200);
		}

		public function addTag() {
			$image_id = $this->load('image_id');
			$tag = $this->load('tag', false); // If category
			$tmember_id = $this->load('tmember_id', false); // If member

			$this->forceauth();

			if($tag != "") { // Add category tag
				$res = mysql_query("SELECT category_id from categories where category='$tag'");
				if(mysql_num_rows($res) == 0) {
					// ID doesn't exist, create new id
					mysql_query("INSERT INTO categories VALUES ('$tag')");
					$res = mysql_query("SELECT category_id FROM categories where category='$tag'");
				}
				$array = mysql_fetch_array($res);
				$tag_id = $array['category_id'];

				$res = mysql_query("INSERT INTO category_tags VALUES ($image_id, $tag_id)");
				if(!$res) { // Add member tag
					//Get error
					$err = mysql_errno();
					if($err == 1062) {
						//image already has that tag
						$error = json_encode(array('status' => 'Failed', 'msg' => 'Image already has that tag'));
						$this->response($error, 409);
					}

					//Something else went wrong
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
					$this->response($error, 500);
				}
			}
			if($tmember_id != "") { // Add member tag
				$res = mysql_query("INSERT INTO mem_tags VALUES ($tmember_id, $image_id, NOW())");
				if(!$res) {
					//Get error
					$err = mysql_errno();
					if($err == 1062) {
						//User already tagged in this image
						$error = json_encode(array('status' => 'Failed', 'msg' => 'User already tagged in this image'));
						$this->response($error, 409);
					}

					//Something else went wrong
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
					$this->response($error, 500);
				}
			}

			// Success
			$this->response(json_encode('', 200));
		}
		public function deleteTag() {
			$image_id = $this->load('image_id');
			$tag = $this->load('tag', false); // If category
			$tmember_id = $this->load('tmember_id', false); // If member

			$this->forceauth();

			if ($tag != "" && $tmember_id != ""){
				// attempted to delete both a category and member tag at the same time
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Can\'t delete a category tag and a member tag at the same time!'));
				$this->response($error, 400);
			}

			$res = mysql_query("SELECT owner_id FROM images where image_id = $image_id");
			$array = mysql_fetch_array($res);

			if($tag != "") { // Delete category tag
				//make sure user is allowed to delete this tag
				if ($array['owner_id'] != $this->memberid){
					// person does not own that image
					$error = json_encode(array('status' => 'Failed', 'msg' => 'You don\'t own that image'));
					$this->response($error, 403);
				}
				$res = mysql_query("SELECT category_id FROM categories where category='$tag'");
				$array = mysql_fetch_array($res);
				$tag_id = $array['category_id'];

				$res = mysql_query("SELECT * FROM category_tags where image_id=$image_id and category_tag=$tag_id");
				if(mysql_num_rows($res) == 0) {
					// Image does not have that tag
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not have that tag'));
					$this->response($error, 409);
				}

				mysql_query("DELETE FROM category_tags where image_id=$image_id and category_tag=$tag_id");
			}
			if($tmember_id != "") { // Delete member tag
				//make sure user is allowed to remove this tag
				if ($array['owner_id'] != $this->memberid && $tmember_id != $this->memberid){
					// person does not own that image and is not the tagged person
					$error = json_encode(array('status' => 'Failed', 'msg' => 'You can only remove a tag if you own the image or you are the person tagged'));
					$this->response($error, 403);
				}
				//make sure already tagged
				$res = mysql_query("SELECT * FROM mem_tags where image_id=$image_id and member_id=$tmember_id");
				if(mysql_num_rows($res) == 0) {
					//Person is not tagged in that image
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Person is not tagged in that image'));
					$this->response($error, 409);
				}

				mysql_query("DELETE FROM mem_tags where image_id=$image_id and member_id=$tmember_id");
			}

			$this->response(json_encode('', 200));
		}

		public function addFavorite() {
			$image_id = $this->load('image_id');
			$tmember_id = $this->load('tmember_id');

			$this->forceauth();

			$res = mysql_query("INSERT INTO favorites VALUES ($image_id, $tmember_id)");
			if(!$res) {
				$err = mysql_errno();
				if($err == 1062) {
					// User already favorited this image
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Already favorited this image'));
					$this->response($error, 409);
				}
				//Something else went wrong
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
				$this->response($error, 500);
			}

			$this->response(json_encode('', 200));
		}

		public function deleteFavorite() {
			$image_id = $this->load('image_id');
			$tmember_id = $this->load('tmember_id');

			$this->forceauth();

			// Make sure already favorited
			$res = mysql_query("SELECT * FROM favorites where image_id=$image_id and member_id=$tmember_id");
			if(mysql_num_rows($res) == 0){
				//User has not favorited that image
				$error = json_encode(array('status' => 'Failed', 'msg' => 'User has not favorited that image'));
				$this->response($error, 409);
			}

			mysql_query("DELETE FROM favorites where image_id=$image_id and member_id=$tmember_id");
			$this->response(json_encode('', 200));
		}

		public function setPrivacy() {
			$privacy = $this->load('privacy');
			$image_id = $this->load('image_id');

			$this->forceauth();

			// Make sure image exists
			$res = mysql_query("SELECT * FROM images where image_id=$image_id");
			if(mysql_num_rows($res) == 0){
				// Image does not exist
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not exist'));
				$this->response($error, 409);
			}

			// Update privacy
			mysql_query("UPDATE images SET publicness=$privacy where image_id=$image_id");
			$this->response(json_encode('', 200));
		}

		public function getLastImages() {
			$num = $this->load('num');
			$user_id = $this->load('user_id', false);

			if($num > 10)
				$num = 10;

			if($user_id != "") {
				$res = mysql_query("SELECT * FROM images WHERE owner_id='$user_id' ORDER BY image_id DESC LIMIT $num");

				$row = mysql_fetch_array($res);
				$alb_owner = $row['owner_id'];

				if($this->memberid == $alb_owner)
					$cutoff = 2;
				else if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$alb_owner'")))
					$cutoff = 1;
				else
					$cutoff = 0;
			} else {
				$res = mysql_query("SELECT * FROM images ORDER BY image_id DESC LIMIT $num");
				$row = mysql_fetch_array($res);	// For do..while() loop
				$cutoff = 0;
			}

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

		public function getFavorites() {
			$this->forceauth();

			$res = mysql_query("SELECT image_id FROM favorites WHERE member_id='$this->memberid'");
			if(!mysql_num_rows($res)) {
				$this->response('', 204); //user has no favorites
			}

			$array = mysql_fetch_array($res);
			$this->response(json_encode($array), 200);
		}

		public function filterImages() {
			$num = $this->load('num');
			$text = $this->load('text');

			$res = mysql_query("SELECT image_id FROM images LEFT JOIN follows ON owner_id=followee_id LEFT JOIN category_tags LEFT JOIN categories WHERE ((publicness=0) OR (publicness=1 and follower_id='$this->memberid') OR (owner_id='$this->memberid')) and (name='$text' or category='$text') ORDER BY image_id DESC LIMIT $num");

			$i = 0;
			while($row = mysql_fetch_array($res)){
				$tosend[$i++] = intval($row['image_id']);
				$i += 1;
			}

			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);
		}
	}

	$api = new Image;
	$api->process();
?>
