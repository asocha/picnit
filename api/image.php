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

		public function process() {
			//Get the action
			$action = $_POST['action'];

			//See if method exists in class
			if(method_exists($this, $action))
				$this->$action(); //Call if found, php magic
			else
				$this->response('',404); //Else send 404 (not found)
		}

		public function getImage() {
			$imageid = $_POST['imageid'];

			if(empty($imageid))
				$this->response('', 400);

			// Redirect to our get_image handler
			header("Location: php/get_image.php?id=$imageid");
			exit;
		}

		public function saveImage() {
			// FIXME: Figure out if you can POST binary data
			$userid = mysql_real_escape_string($_POST['userid']);
			$albumid = mysql_real_escape_string($_POST['albumid']);
			$publicness = mysql_real_escape_string($_POST['publicness']);
			$photo = $_POST['image'];

			// Check that we have everything we need
			if(empty($userid) || empty($albumid) || empty($publicness) || empty ($photo))
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
			$filepath = "/".$dir1."/".$dir2."/".$dir3."/"."/".$dir4."/".$dir5."/".$file.".png";

			// This should pretty much never happen, but still...
			if(file_exists("/var/www".$filepath))
				goto get_new_file_path;

			// Actually write out the POSTed photo file data
			$fh = fopen("/var/www".$filepath);
			fwrite($fh, $photo);
			fclose($fh);

			$result = mysql_query("INSERT INTO images (album_id,publicness,filepath,date_added) VALUES ('$albumid','$publicness', '$filepath', NOW())");
			if(!$result)
				$this->response('', 404);

			$this->response('',200);
		}
	}

	$api = new Image;
	$api->process();
?>
