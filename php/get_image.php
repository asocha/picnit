<?php
	$con = mysql_connect('localhost', 'picnit', 'PhotoDolo247');
	if(!$con) {
		header("HTTP/1.1 503 Service Unavailable");
		exit;
	}

	$photoid = mysql_real_escape_string($_GET['id']);
	if($photoid == "") {
		header("HTTP/1.1 400 Bad Request");
		exit;
	}

	mysql_select_db('picnit', $con);

	$result = mysql_query("SELECT filepath,publicness FROM images WHERE image_id='$photoid' LIMIT 1", $con);
	if(!mysql_num_rows($result)) {
		header("HTTP/1.1 404 Not Found");
		exit;
	}

	$publicness = mysql_result($result, 0, publicness);
	$filepath = mysql_result($result, 0, filepath);

	if($publicness == 0) {
		$filetype = mime_content_type("/var/www/picnit/images/user".$filepath);
		header("Content-type: $filetype");
		echo file_get_contents("/var/www/picnit/images/user".$filepath);
		exit;
	}

	header("HTTP/1.1 403 Forbidden");
	exit;
?>
