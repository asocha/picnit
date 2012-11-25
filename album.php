<!DOCTYPE html>
<?php
	require_once('php/general.php');

	//Make sure id has been passed
	if(!isset($_GET['id']))
		header('Location: 404.php');

	//Get data of this album
	$fields = array(
		'action' => 'albumData',
		'username' => $_COOKIE['username'],
		'key' => $_COOKIE['key'],
		'album_id' => $_GET['id']
	);

	//Send request
	$res = picnitRequest('api/album.php', $fields);

	if($res['status'] === 200)
		
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>Album</title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script type="text/javascript" src="js/image.js"></script>
	<script type="text/javascript" src="js/album.js"></script>
	<script>
	window.onload = function() {
		if(isLoggedIn()) {
			document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
			document.getElementById('uploadbut').addEventListener('click',showUploader,false);
			document.getElementById('imgcancel').addEventListener('click',hideUploader,false);
			document.getElementById('uploadoverlay').addEventListener('click',hideUploader,false);
		}
		else {
			document.getElementById('signupbut').addEventListener('click',showsignup,false);
			document.getElementById('cancel').addEventListener('click',hidesignup,false);
			document.getElementById('overlay').addEventListener('click',hidesignup,false);
			document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
			document.getElementById('uploadbut').addEventListener('click',showUploader,false);
			document.getElementById('imgcancel').addEventListener('click',hideUploader,false);
			document.getElementById('uploadoverlay').addEventListener('click',hideUploader,false);
		}
	}
	</script>
</head>
<body>
	<?php menubar(); ?>
	<div>
		<h1>album</h1>
	</div>
	<?php searchbar(); ?>
	<div id="results" class="panels">
		<div id="albuminfo" class="panels">
			<p><div id="albumtitle">Album: </div></p>
			<p><div id="albumdesc">Description: </div></p>
			<input type="button" id="uploadbut" class="buttons" value="upload"/>
		</div>
	</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php imageview(); ?>
	<?php uploader(); ?>
	<?php albumcreator(); ?>
</body>
</html>
