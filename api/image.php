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
			$imageid = $this->load($_POST['imageid']);

			$error = json_encode(array('status' => 'Failed', 'msg' => 'This is not implemented yet!'));
			$this->response($error, 501);
		}

		public function saveImage() {
			$userid = $this->load($_POST['userid']);
			$albumid = $this->load($_POST['albumid']);
			$publicness = $this->load($_POST['publicness']);
			$phototype = $this->load($_POST['phototype']);
			$photo = base64_decode($_POST['photo']);

get_new_file_path:
			// Path is stored in the form "/xxxx/xxxx/xxxx/xxxx/xxxx/xx.ext"
			// Storing very large numbers of files in a single directory is extremely sub-optimal
			// on the ext3/ext4 filesystems. Adding a random directory tree like this dramatically
			// enhances performance with large numbers of pictures.
			// FIXME: We need some way to determine the file extention to add...
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

			$result = mysql_query("INSERT INTO images (album_id,publicness,filepath,date_added) VALUES ('$albumid','$publicness', '$filepath', NOW())");
			if(!mysql_num_rows($result))
				$this->response('', 404);

			$this->response('',200);
		}

		public function deleteImage() {
			$image_id = $this->load($_POST['image_id']);

			$res = mysql_query("SELECT filepath from images where image_id=$image_id");
			if(mysql_num_rows($res) == 0){
				// Image does not exist
				$error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not exist'));
				$this->response($error, 409);
			}

			mysql_query("REMOVE FROM images where image_id=$image_id");
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
					$array = mysql_fetch_array($res);
					$tag_id = $array['category_id'];
				} else {
					$array = mysql_fetch_array($res);
					$tag_id = $array['category_id'];
				}

				$res = mysql_query("INSERT INTO category_tags VALUES ($image_id, $tag_id)");
				if(!$res) {
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
			if($tmember_id != "") {
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

			mysql_query("REMOVE FROM favorites where image_id=$image_id and member_id=$tmember_id");
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
	}

	$api = new Image;
	$api->process();
?>
