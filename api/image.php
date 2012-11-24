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
			$image_id = $this->load($_POST['image_id']);

			// Verify that user has authenticated before proceeding
			if($this->memberid == -1) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'You must authenticate'));
				$this->response($error, 403);
			}

			$res = mysql_query("SELECT publicness,owner_id,filepath,name,description,imgtype FROM images WHERE image_id='$image_id'");
			if(!mysql_num_rows($res)) {
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not exist'));
				$this->response($error, 404);
			}

			$publicness = mysql_result($res, 0, publicness);
			$owner_id = mysql_result($res, 0, owner_id);

			if($publicness == 0)
				goto allow_user_access;

			if($this->memberid == $owner_id)
				goto allow_user_access;

			if($publicness == 1 && mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$owner_id'")))
				goto allow_user_access;

			$error = json_encode(array('status' => 'Failed', 'msg' => 'You are not permitted to access this image'));
			$this->response($error, 403);
allow_user_access:
			$filepath = mysql_result($res, 0, filepath);
			$name = mysql_result($res, 0, name);
			$description = mysql_result($res, 0, description);
			$type = mysql_result($res, 0, imgtype);

			$respnse = json_encode(array('status' => 'Success', 'img' => base64_encode(file_get_contents("/var/www/picnit/images/user".$filepath)),
			'name' => $name, 'description' => $description, 'type' => $type));
			$this->response($respnse, 200);
		}

		public function saveImage() {
			$album_id = $this->load($_POST['album_id']);
			$publicness = $this->load($_POST['publicness']);
			$phototype = $this->load($_POST['phototype']);
			$name = $this->load($_POST['name']);
			$description = $this->load($_POST['description']);
			$photo = base64_decode($_POST['photo']);

			if(strlen($phototype) != 3)
				$this->response('', 400);

			// Verify that the album exists, and the user owns it
			$result = mysql_query("SELECT owner_id FROM albums WHERE album_id='$album_id'");
			if(!mysql_num_rows($result))
				$this->response('', 404);

			if(mysql_result($result, 0, owner_id) != $this->memberid)
				$this->response('', 403);

get_new_file_path:
			// Path is stored in the form "/xxxx/xxxx/xxxx/xxxx/xxxx/xx.ext"
			// Storing very large numbers of files in a single directory is extremely sub-optimal
			// on the ext3/ext4 filesystems. Adding a random directory tree like this dramatically
			// enhances performance with large numbers of pictures.
			$dir1 = mt_rand(0,9999);
			$dir2 = mt_rand(0,9999);
			$dir3 = mt_rand(0,9999);
			$dir4 = mt_rand(0,9999);
			$dir5 = mt_rand(0,9999);
			$file = mt_rand(0,99);
			$filepath = "/".$dir1."/".$dir2."/".$dir3."/".$dir4."/".$dir5."/".$file.".$phototype";

			// This should pretty much never happen, but still...
			if(file_exists("/var/www/picnit/images/user".$filepath))
				goto get_new_file_path;

			// Actually write out the POSTed photo file data
			mkdir("/var/www/picnit/images/user/".$dir1."/".$dir2."/".$dir3."/".$dir4."/".$dir5, 0775, true);
			$fh = fopen("/var/www/picnit/images/user".$filepath, 'w+');
			fwrite($fh, $photo);
			fclose($fh);
			$type = mime_content_type("/var/www/picnit/images/user".$filepath);

			$result = mysql_query("INSERT INTO images (album_id,publicness,filepath,date_added,name,description,imgtype,owner_id) VALUES ('$album_id','$publicness', '$filepath', NOW(), '$name', '$description', '$type', '$this->memberid')");
			$this->response('',200);
		}

		public function deleteImage() {
			$image_id = $this->load($_POST['image_id']);

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
			$image_id = $this->load($_POST['image_id']);
			$tag = $this->load($_POST['tag'], false); // If category
			$tmember_id = $this->load($_POST['tmember_id'], false); // If member

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
			$image_id = $this->load($_POST['image_id']);
			$tag = $this->load($_POST['tag'], false); // If category
			$tmember_id = $this->load($_POST['tmember_id'], false); // If member

			if($tag != "") { // Delete category tag
				$res = mysql_query("SELECT category_id FROM categories where category='$tag'");
				$array = mysql_fetch_array($res);
				$tag_id = $array['category_id'];

				$res = mysql_query("SELECT * FROM category_tags where image_id=$image_id and category_tag=$tag_id");
				if(mysql_num_rows($res) == 0) {
					// Image does not have that tag
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not have that tag'));
					$this->response($error, 409);
				}

				mysql_query("REMOVE FROM category_tags where image_id=$image_id and category_tag=$tag_id");
			}
			if($tmember_id != "") { // Delete member tag
				//make sure already tagged
				$res = mysql_query("SELECT * FROM mem_tags where image_id=$image_id and member_id=$tmember_id");
				if(mysql_num_rows($res) == 0) {
					//Person is not tagged in that image
					$error = json_encode(array('status' => 'Failed', 'msg' => 'Person is not tagged in that image'));
					$this->response($error, 409);
				}

				mysql_query("REMOVE FROM mem_tags where image_id=$image_id and member_id=$tmember_id");
			}

			$this->response(json_encode('', 200));
		}

		public function addFavorite() {
			$image_id = $this->load($_POST['image_id']);
			$tmember_id = $this->load($_POST['tmember_id']);

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
			$image_id = $this->load($_POST['image_id']);
			$tmember_id = $this->load($_POST['tmember_id']);

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
			$privacy = $this->load($_POST['privacy']);
			$image_id = $this->load($_POST['image_id']);

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
			$num = $this->load($_POST['num']);
			$id = $this->load($_POST['id'], false);

			if($num > 10)
				$num = 10;

			if($id != "") {
				if($this->memberid == $id)
					$res = mysql_query("SELECT image_id FROM images WHERE owner_id='$id' ORDER BY image_id DESC LIMIT $num");

				if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$id'")))
					$res = mysql_query("SELECT image_id FROM images WHERE owner_id='$id' and publicness < 2 ORDER BY image_id DESC LIMIT $num");

				$res = mysql_query("SELECT image_id FROM images WHERE owner_id='$id' and pubicness='0' ORDER BY image_id DESC LIMIT $num");
			} else {
				$res = mysql_query("SELECT image_id FROM images WHERE publicness='0' ORDER BY image_id DESC LIMIT $num");
			}

			if(!mysql_num_rows($res))
				$this->response('', 404);

			$array = mysql_fetch_array($res);
			$this->response(json_encode($array), 200);
		}
	}

	$api = new Image;
	$api->process();
?>
