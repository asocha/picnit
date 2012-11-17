<!DOCTYPE html>
<?php
	//Get general.php for DB calls
	require_once('php/general.php');

	//Check log in
	if(!isLoggedIn())
		header('Location: index.php');
	
	//See if this user exists

?>
<html>
<title><?php echo  $_GET['username']; ?>'s profile!</title>
<head>
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/flexslider.css" type="text/css">
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<script src="js/general.js"></script>
	<script src="js/member.js"></script>
	<script src="js/libraries/jquery-1.8.2.min.js"></script>
	<script src="js/libraries/jquery.flexslider-min.js"></script>
	<script type="text/javascript" charset="utf-8">
	  $(window).load(function() {
		//Set sign-out button
		$('#signoutbut').click(logout);

		//Load flexslider
		$('.flexslider').flexslider();
	  });
	</script>
</head>
<body>
	<div id="menubar">
		<form id="signoutbut">
			<span><input type="button" id="sign" value="Sign Out"/></span>
		</form>
		<form id="homebut" action="index.php">
			<span><input type="submit" id="home" value="Home"/></span>
		</form>
	</div>
	<div>
		<h1><?php echo  $_GET['username']; ?>'s profile</h1>
	</div>

	<div id="searchbar">
		<form id="search" action="index.php" method="post">
			<span id="searchlabel"><label for="Searchterm">Search:</label></span><span><input type="text" name="Searchterm" id="Searchterm"/></span>		
			<span><input type="submit" name="search" id="search" value="Submit"/></span>
		</form>
	</div>
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
		<div id="usermenu">
			<form id="userform">
				<input id="albumsbut" type="button" value="Albums"/>
				<input id="categorybut" type="button" value="Categories"/>
				<input id="favoritebut"type="button" value="Favorites"/>
				<input id="uploadbut" type="button" value="Upload"/>
			</form>
		</div>
		<div id="collection">
			<div id="thumbnail-display">
				<a href="images/AlArBdr.gif"><img src="images/AlArBdr.gif" alt="Pulpit rock" width="50" height="50"></a>
				<a href="images/article-0-14C152E0000005DC-964_964x764.jpg"><img src="images/article-0-14C152E0000005DC-964_964x764.jpg" alt="Pulpit rock" width="50" height=50"></a>
				<a href="images/FD_image.jpg"><img src="images/FD_image.jpg" alt="Pulpit rock" width="50" height="50"></a>
				<a href="images/image16.gif"><img src="images/image16.gif" alt="Pulpit rock" width="50" height="50"></a>
			<a href="images/pia12832-browse.jpg"><img src="images/pia12832-browse.jpg" alt="Pulpit rock" width="50" height="50"></a>
			</div>
		</div>
	</div>
	

	<div id="info" name="info">
		<div id="infotext">
			<div>Picnit.net</div>
			<div>A PhotoDolo Project</div>
		</div>
	</div>
</body>
</html>
