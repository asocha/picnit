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

		public function getImages() {
			$image_id = $this->load('image_id',false);
			$album_id = $this->load('album_id',false);
			$user_id = $this->load('user_id',false);
			$tagged_user_id = $this->load('tagged_user_id',false);
			$cat_id = $this->load('cat_id',false);
			$fuser_id = $this->load('fuser_id',false);
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

			if($image_id != "")
				$res = mysql_query("SELECT *,(SELECT member_id FROM follows f,members m WHERE (m.member_id=f.follower_id and f.followee_id=i.owner_id and m.member_id='$this->memberid' and f.is_accepted=true and i.publicness < 2) or (m.member_id=i.owner_id and m.member_id='$this->memberid') or (i.publicness=0) LIMIT 1) AS cansee FROM images i WHERE i.image_id='$image_id' HAVING cansee!='NULL' ORDER BY i.image_id desc$limclause");
			else if($album_id != "")
				$res = mysql_query("SELECT *,(SELECT member_id FROM follows f,members m WHERE (m.member_id=f.follower_id and f.followee_id=i.owner_id and m.member_id='$this->memberid' and f.is_accepted=true and i.publicness < 2) or (m.member_id=i.owner_id and m.member_id='$this->memberid') or (i.publicness=0) LIMIT 1) AS cansee FROM images i WHERE i.album_id='$album_id' HAVING cansee!='NULL' ORDER BY i.image_id desc$limclause");
			else if($user_id != "")
				$res = mysql_query("SELECT *,(SELECT member_id FROM follows f,members m WHERE (m.member_id=f.follower_id and f.followee_id=i.owner_id and m.member_id='$this->memberid' and f.is_accepted=true and i.publicness < 2) or (m.member_id=i.owner_id and m.member_id='$this->memberid') or (i.publicness=0) LIMIT 1) AS cansee FROM images i WHERE i.owner_id='$user_id' HAVING cansee!='NULL' ORDER BY i.image_id desc$limclause");
			else if($tagged_user_id != "")
				$res = mysql_query("SELECT i.* FROM member_tags m,images i,follows f WHERE m.member_id='$tagged_user_id' and m.image_id=i.image_id and ((i.publicness=0) or (i.owner_id=f.followee_id and f.follower_id='$this->memberid' and f.is_accepted=true and publicness < 2) or (i.owner_id='$this->memberid')) ORDER BY i.image_id desc$limclause");
			else if($cat_id != "")
				$res = mysql_query("SELECT i.* FROM category_tags c,images i,follows f WHERE c.category_id='$cat_id' and c.image_id=i.image_id and ((publicness=0) or (i.owner_id=f.followee_id and f.follower_id='$this->memberid' and f.is_accepted=true and publicness < 2) or (i.owner_id='$this->memberid')) ORDER BY i.image_id desc$limclause");
			else if($fuser_id != "")
				$res = mysql_query("SELECT i.*,(SELECT member_id FROM follows f,members m WHERE (m.member_id=f.follower_id and f.followee_id=i.owner_id and m.member_id='$this->memberid' and f.is_accepted=true and i.publicness < 2) or (m.member_id=i.owner_id and m.member_id='$this->memberid') or (i.publicness=0) LIMIT 1) AS cansee,(SELECT m.member_id FROM favorites v,members m WHERE m.member_id='$fuser_id' and v.member_id=m.member_id and v.image_id=i.image_id) AS isfavorite FROM images i HAVING cansee!='NULL' and isfavorite!='NULL' ORDER BY i.image_id desc$limclause");
			else
				$this->response(json_encode(array('msg' => "You must provide image_id,album_id,user_id,tagged_user_id,fuser_id, or cat_id")), 400);

			if(!$res)
				$this->response(json_encode(array('msg' => "Error: ".mysql_error())), 503);

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

		public function saveImage() {
			$album_id = $this->load('album_id');
			$publicness = $this->load('publicness');
			$phototype = $this->load('phototype');
			$name = $this->load('name');
			$description = $this->load('description', false);
			$photo = base64_decode($_POST['photo']);

			if ($description == null) $description = "";

			if(strlen($phototype) != 3)
				$this->response(json_encode(array('msg' => 'Photo Type must be 3 characters')), 400);

			$this->forceauth();

			// Verify that the album exists, and the user owns it
			$result = mysql_query("SELECT owner_id FROM albums WHERE album_id='$album_id'");
			if(!mysql_num_rows($result))
				$this->response(json_encode(array('msg' => 'Album does not exist')), 404);
			if(mysql_result($result, 0, owner_id) != $this->memberid)
				$this->response(json_encode(array('msg' => 'You don\'t own that album')), 403);

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

			$res =	mysql_query("UPDATE images SET publicness='$privacy' where image_id='$image_id' and owner_id='$this->memberid'");
			if(!mysql_affected_rows())
				$this->response(json_encode(array('msg' => 'Failed. Does the image exist? Do you own it?')), 404);

			$this->response(json_encode('', 200));
		}
	}

	$api = new Image;
	$api->process();
?>
