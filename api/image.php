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
			$imageid = $_POST['imageid'];

			if(!isset($imageid))
				$this->response('', 400);

			// Redirect to our get_image handler
			header("Location: php/get_image.php?id=$imageid");
			exit;
		}

		public function saveImage() {
			$userid = mysql_real_escape_string($_POST['userid']);
			$albumid = mysql_real_escape_string($_POST['albumid']);
			$publicness = mysql_real_escape_string($_POST['publicness']);
			$phototype = mysql_real_escape_string($_POST['phototype']);
			$photo = base64_decode($_POST['photo']);

			// Check that we have everything we need
			if(!isset($userid) || !isset($albumid) || !isset($publicness) || !isset($photo) || (strlen($phototype) != 3))
				$this->response('', 400);

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

		public function addTag() {
			//Get the vars
                        $image_id = $_POST['image_id'];
                        $tag = mysql_real_escape_string($_POST['tag']); //if category tag
			$member_id = $_POST['member_id']; //if member tag

                        //Ensure all variables needed are present
                        if(isset($tag) && (isset($image_id) || isset($member_id))) {
				if(isset($tag)){ //add category tag
					//get id for this tag
					$tag_id = mysql_query("SELECT category_id from categories where category='$tag'", $this->link);
					if(mysql_num_rows($res) == 0){
						//id doesn't exist, create new id
						mysql_query("INSERT INTO categories VALUES ('$tag')", $this->link);
						$tag_id = mysql_query("SELECT category_id FROM categories where category='$tag'", $this->link);
					}

	                                //create tag
        	                        $res = mysql_query("INSERT INTO category_tags VALUES ($image_id, $tag_id)", $this->link);

                	                //Make sure query works
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
				else{ //add member tag
	                                $res = mysql_query("INSERT INTO mem_tags VALUES ($member_id, $image_id, NOW())", $this->link);

        	                        //Make sure query works
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

                                //success
                                $this->response(json_encode('', 200));
                        }

                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing data'));
                        $this->response($error, 400);
		}

		public function deleteTag() {
			//Get the vars
                        $image_id = $_POST['image_id'];
                        $tag = mysql_real_escape_string($_POST['tag']); //if category tag
			$member_id = $_POST['member_id']; //if member tag

                        //Ensure all variables needed are present
                        if(isset($tag) && (isset($image_id) || isset($member_id))) {
				if(isset($tag)){ //delete category tag
					//get id for this tag
        	                        $tag_id = mysql_query("SELECT category_id FROM categories where category='$tag'", $this->link);

					//make sure tag exists
					$res = mysql_query("SELECT * FROM category_tags where image_id=$image_id and category_tag=$tag_id", $this->link);
					if(mysql_num_rows($res) == 0){
						//image does not have that tag
        	                                $error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not have that tag'));
                	                        $this->response($error, 409);
					}

                                	//delete tag
                                	mysql_query("REMOVE FROM category_tags where image_id=$image_id and category_tag=$tag_id", $this->link);
				}
				else {
					//make sure already tagged
	                                $res = mysql_query("SELECT * FROM mem_tags where image_id=$image_id and member_id=$member_id", $this->link);
        	                        if(mysql_num_rows($res) == 0){
                	                        //Person is not tagged in that image
                        	                $error = json_encode(array('status' => 'Failed', 'msg' => 'Person is not tagged in that image'));
                                	        $this->response($error, 409);
                                	}

        	                        //delete tag
	                                mysql_query("REMOVE FROM mem_tags where image_id=$image_id and member_id=$member_id", $this->link);
				}
                                //success
                                $this->response(json_encode('', 200));
                        }

                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing data'));
                        $this->response($error, 400);
		}

		public function addFavorite() {
                        //Get the vars
                        $image_id = $_POST['image_id'];
                        $member_id = $_POST['member_id'];

                        //Ensure all variables needed are present
                        if(isset($member_id) && isset($image_id)) {
                                //add Favorite
                                $res = mysql_query("INSERT INTO favorites VALUES ($image_id, $member_id)", $this->link);

                                //Make sure query works
                                if(!$res) {
                                        //Get error
                                        $err = mysql_errno();

                                        if($err == 1062) {
                                                //User already favorited this image
                                                $error = json_encode(array('status' => 'Failed', 'msg' => 'Already favorited this image'));
                                                $this->response($error, 409);
                                        }

                                        //Something else went wrong
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Unknown error'));
                                        $this->response($error, 500);
                                }

                                //success
                                $this->response(json_encode('', 200));
                        }

			//missing data
			$error = json_encode(array('status' => 'Failed', 'msg' => 'Missing image_id or member_id'));
                        $this->response($error, 400);
		}

		public function deleteFavorite() {
                        //Get the vars
                        $image_id = $_POST['image_id'];
                        $member_id = $_POST['member_id'];

                        //Ensure all variables needed are present
                        if(isset($member_id) && isset($image_id)) {
                                //make sure already favorited
                                $res = mysql_query("SELECT * FROM favorites where image_id=$image_id and member_id=$member_id", $this->link);
                                if(mysql_num_rows($res) == 0){
                                        //User has not favorited that image
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'User has not favorited that image'));
                                        $this->response($error, 409);
                                }

                                //delete favorite
                                mysql_query("REMOVE FROM favorites where image_id=$image_id and member_id=$member_id", $this->link);
                                //success
                                $this->response(json_encode('', 200));
                        }

                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing image_id or member_id'));
                        $this->response($error, 400);
                }

		public function setPrivacy() {
			//Get the vars
			$privacy = $_POST['privacy'];
			$image_id = $_POST['image_id'];

			//Ensure all variables needed are present
                        if(isset($privacy) && isset($image_id)) {
				//make sure image exists
				$res = mysql_query("SELECT * FROM images where image_id=$image_id", $this->link);
                                if(mysql_num_rows($res) == 0){
                                        //image does not exist
                                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Image does not exist'));
                                        $this->response($error, 409);
                                }

                                //update privacy
                                mysql_query("UPDATE images SET publicness=$privacy where image_id=$image_id", $this->link);
                                //success
                                $this->response(json_encode('', 200));
                        }

                        $error = json_encode(array('status' => 'Failed', 'msg' => 'Missing image_id or privacy'));
                        $this->response($error, 400);
		}
	}

	$api = new Image;
	$api->process();
?>
