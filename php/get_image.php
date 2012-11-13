<?php
	// $memberid = ACTUALLY_GET_FROM_SESSION();
	$photoid = $_GET['id'];
	if(empty($photoid)) {
		header("HTTP/1.1 400 Bad Request");
		exit;
	}

	$con = mysql_connect('localhost', 'picnit', 'PhotoDolo247');
	if(!$con) {
		header("HTTP/1.1 503 Service Unavailable");
		exit;
	}

	mysql_select_db('picnit', $con);

	$result = mysql_query("SELECT album_id,publicness FROM images WHERE image_id='$photoid' LIMIT 1", $con);
	if(!$result) {
		header("HTTP/1.1 404 Not Found");
		exit;
	}

	$publicness = mysql_result($result, 0, publicness);
	$albumid = mysql_result($result, 0, album_id);

	if($publicness == 0)
		goto grant_access; // It's public - return it
	if(empty($memberid))
		goto deny_access; // Not public, not logged in - deny

	// Okay, the user is logged in with a valid session, and we have their ID

	if(mysql_result(mysql_query("SELECT is_admin FROM members WHERE member_id='$memberid' LIMIT 1", $con), 0, is_admin) == 1)
		goto grant_access; // The user logged in is an admin - allow it

	// We need the member_id of the owner of the image for the rest of the checks - go ahead and get it
	$ownerid = mysql_result(mysql_query("SELECT owner_id FROM albums WHERE album_id='$albumid' LIMIT 1", $con), 0, owner_id);

	if($publicness == 2) {
		if($memberid == $ownerid)
			goto grant_access; // User is the owner - allow
		goto deny_access;
	}

	// Now we have to see if the user is a follower of the owner
	if(mysql_query("SELECT follower_id FROM follows WHERE follower_id='$memberid' and followee_id='$ownerid' LIMIT 1")
		goto grant_access;
	goto deny_access;

grant_access:
	// Path is stored in the form "/xxxx/xxxx/xxxx/xxxxxxxxxxxx.ext"
	$imagepath = mysql_result(mysql_query("SELECT filepath FROM images WHERE image_id='$photoid' LIMIT 1"), 0, filepath);
	$filetype = mime_content_type("/var/www/images".$imagepath);
	header("Content-type: $filetype");
	echo file_get_contents("/var/www/images".$imagepath);
	exit;
deny_access:
	header("HTTP/1.1 403 Forbidden");
	exit;
?>
