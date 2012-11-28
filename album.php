<!DOCTYPE html>
<?php
	require_once('php/general.php');

	//Get data from URI
	$id = substr($_SERVER['REQUEST_URI'], strripos($_SERVER['REQUEST_URI'], "/") + 1); 

	//Make sure id has been passed
	if($id === "")
		header('Location: /picnit/404.php');

	//Get data of this album
	$fields = array(
		'action' => 'albumData',
		'username' => urlencode($_COOKIE['username']),
		'key' => urlencode($_COOKIE['key']),
		'album_id' => $id
	);

	//Send request
	$res = picnitRequest('api/album.php', $fields);

	if($res['status'] === 200) {
		$albuminfo = json_decode($res['result'], true);
		$albuminfo = $albuminfo['list'][0];
	}
	else
		header("Location: /picnit/404.php?$id");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="/picnit/css/style.css"/>
	<link rel="stylesheet" type="text/css" href="/picnit/css/jquery-ui-1.9.2.custom.min.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title><?php echo $albuminfo['name']; ?></title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery.transit.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="/picnit/js/general.js"></script>
	<script type="text/javascript" src="/picnit/js/member.js"></script>
	<script type="text/javascript" src="/picnit/js/image.js"></script>
	<script type="text/javascript" src="/picnit/js/album.js"></script>
	<script type="text/javascript" src="/picnit/js/tag.js"></script>
	<script type="text/javascript" src="/picnit/js/profile.js"></script>
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
		<h1><?php echo $albuminfo['name']; ?></h1>
	</div>
	<?php searchbar(); ?>
	<div id="results" class="panels">
		<div id="albuminfo" class="panels">
			<p><div id="albumtitle">Album: </div></p>
			<p><div id="albumdesc">Description: </div></p>
			<input type="button" id="uploadbut" class="buttons" value="upload"/>
			<input type="button" id="albdelbut" class="buttons" value="delete"/>
			<script type="text/javascript">
				$('#albdelbut').click(function() {
					if(deleteAlbum(<?php echo $albuminfo['album_id']; ?>))
						window.location = '/picnit/profile/' + getCookie('username');
					else
						alert("Failed deletion");
				});
			</script>
		</div>
		<div id="image-holder">
		</div>
		<script type="text/javascript">
			createAlbumImagesElements(<?php echo $albuminfo['album_id']; ?>);
		</script>
	</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php tagbar(); ?>
	<?php imageview(); ?>
	<?php uploader($albuminfo['album_id']); ?>
	<?php albumcreator(); ?>
</body>
</html>
