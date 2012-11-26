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
				$this->response(json_encode(array('msg' => 'Photo Type must be 3 characters')), 400);

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

			$res = mysql_query("INSERT INTO images (album_id,publicness,filepath,date_added,name,description,imgtype,owner_id) VALUES ('$album_id','$publicness', '$filepath', NOW(), '$name', '$description', '$type', '$this->memberid')");
			if(!$res) {
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);
				unlink($fullpath);
			}

			$this->response('',200);
		}

		public function deleteImage() {
			$image_id = $this->load('image_id');

			$this->forceauth();

			$res = mysql_query("DELETE FROM images WHERE image_id='$image_id' and owner_id='$this->memberid'");
			if(!$res)
				$this->response(json_encode(array('msg' => 'Unknown error - try again')), 503);

			if(mysql_affected_rows() == 1) {
				unlink("/var/www/picnit/images/user".$row['filepath']);
				$this->response(json_encode(array('msg' => 'Deletion was performed if you exist, you own the image, and the image exists')), 200);
			}

			$this->response(json_encode(array('msg' => 'Image was not deleted. Does it exist? Do you own it? Do you exist?')), 469);
		}

		public function setPrivacy() {
			$privacy = $this->load('privacy');
			$image_id = $this->load('image_id');

			$this->forceauth();

			$res = 	mysql_query("UPDATE images SET publicness='$privacy' where image_id='$image_id' and owner_id='$this->memberid'");
			if(!mysql_affected_rows($res))
				$this->response(json_encode(array('msg' => 'Failed. Does the image exist? Do you own it?')), 404);

			$this->response(json_encode('', 200));
		}

		public function getLastImages() {
			$num = $this->load('num');
			$user_id = $this->load('user_id', false);

			if($num > 10)
				$num = 10;

			if($user_id != "") {
				if($this->memberid == -1)
					$res = mysql_query("SELECT * FROM images WHERE owner_id='$user_id' AND publicness='0' ORDER BY image_id DESC LIMIT $num");
				else if($this->memberid == $user_id)
					$res = mysql_query("SELECT * FROM images WHERE owner_id='$user_id' ORDER BY image_id DESC LIMIT $num");
				else if(mysql_num_rows(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$this->memberid' and followee_id='$user_id'")))
					$res = mysql_query("SELECT * FROM images WHERE owner_id='$user_id' AND publicness < 2 ORDER BY image_id DESC LIMIT $num");
				else
					$res = mysql_query("SELECT * FROM images WHERE owner_id='$user_id' AND publicness='0' ORDER BY image_id DESC LIMIT $num");
			} else {
				$res = mysql_query("SELECT * FROM images WHERE publicness='0' ORDER BY image_id DESC LIMIT $num");
			}

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

			$this->response(json_encode(array('status' => 'Success', 'list' => $tosend)), 200);
		}
	}

	$api = new Image;
	$api->process();
?>
