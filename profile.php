<!DOCTYPE html>
<?php
	//Get general.php for DB calls
	require_once('php/general.php');

	//Make sure they send in a username
	if(!isset($_GET['username']))
		if(isLoggedIn())
			$_GET['username'] = $_COOKIE['username'];
		else
			header("Location: index.php");
	
	//See if this user exists
	$fields = array(
		'action' => 'memberData',
		'username' => urlencode($_COOKIE['username']),
		'key' => urlencode($_COOKIE['key']),
		'tusername' => urlencode($_GET['username'])
	);

	$res = picnitRequest('api/member.php', $fields);

	//If does, get user info
	if($res['status'] == 200) 
		$profile = json_decode($res['result'], true);
	else
		header('Location: index.php');
?>
<html>
<title><?php echo  $profile['username']; ?>'s profile!</title>
<head>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/flexslider.css" type="text/css">
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/libraries/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script>
		window.onload = function() {
			if(isLoggedIn()) {
				document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
				document.getElementById('albcancel').addEventListener('click',hideAlbumCreator,false);
				document.getElementById('albumoverlay').addEventListener('click',hideAlbumCreator,false);
				document.getElementById('albumbut').addEventListener('click',showAlbumCreator,false);
			}
			else {
				document.getElementById('signupbut').addEventListener('click',showsignup,false);
				document.getElementById('cancel').addEventListener('click',hidesignup,false);
				document.getElementById('overlay').addEventListener('click',hidesignup,false);
				document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
				document.getElementById('albcancel').addEventListener('click',hideAlbumCreator,false);
				document.getElementById('albumoverlay').addEventListener('click',hideAlbumCreator,false);
				document.getElementById('albumbut').addEventListener('click',showAlbumCreator,false);
			}
		}
	</script>
</head>
<body>
	<?php menubar(); ?>
	<div>
		<h1><?php echo  $profile['username']; ?>'s profile</h1>
	</div>
	<?php searchbar(); ?>
	<div id="slideshow" class="flexslider">
		<ul class="slides">
			<li>
	      			<img src="images/300px-Leuk01.jpg" />
	    		</li>
	    		<li>
	      			<img src="images/dark_energy_camera_images.jpg" />
	    		</li>
	    		<li>
	      			<img src="images/help_clip_image020.jpg" />
	    		</li>
	  	</ul>
	</div>
	<div id="user-image-collection">
		<div id="usermenu" class="panels">
			<form id="userform">
				<input id="albumsbut" class="buttons" type="button" value="Albums"/>
				<input id="categorybut" class="buttons" type="button" value="Categories"/>
				<input id="favoritebut" class="buttons" type="button" value="Favorites"/>
				<input id="uploadbut" class="buttons" type="button" value="Upload"/>
				<input id="albumbut" class="buttons" type="button" value="Add Album"/>
			</form>
		</div>
		<div id="collection" class="panels">
			<div id="thumbnail-display">
			</div>
		</div>
	</div>
	<?php imageview(); ?>
	<?php albumcreator(); ?>
	<?php info(); ?>
</body>
</html>
