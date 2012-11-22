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
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/libraries/jquery.flexslider-min.js"></script>
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
	<div id="menubar" class="panels">
		<form id="signoutbut">
			<span><input type="button" id="sign" class="buttons" value="Sign Out"/></span>
		</form>
		<form id="homebut" action="index.php">
			<span><input type="submit" id="home" class="buttons" value="Home"/></span>
		</form>
	</div>
	<div>
		<h1><?php echo  $_GET['username']; ?>'s profile</h1>
	</div>

	<div id="searchbar" class="panels">
		<form id="search" action="index.php" method="post">
			<span id="searchlabel"><label for="Searchterm">search:</label></span><span><input type="text" name="Searchterm" id="Searchterm" class="inputs"/></span>		
			<span><input type="submit" name="search" id="search" class="buttons" value="Submit"/></span>
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
		<div id="usermenu" class="panels">
			<form id="userform">
				<input id="albumsbut" class="buttons" type="button" value="Albums"/>
				<input id="categorybut" class="buttons" type="button" value="Categories"/>
				<input id="favoritebut" class="buttons" type="button" value="Favorites"/>
				<input id="uploadbut" class="buttons" type="button" value="Upload"/>
			</form>
		</div>
		<div id="collection" class="panels">
			<div id="thumbnail-display">
				<a href="images/AlArBdr.gif"><img src="images/AlArBdr.gif" alt="Pulpit rock" width="50" height="50"></a>
				<a href="images/article-0-14C152E0000005DC-964_964x764.jpg"><img src="images/article-0-14C152E0000005DC-964_964x764.jpg" alt="Pulpit rock" width="50" height=50"></a>
				<a href="images/FD_image.jpg"><img src="images/FD_image.jpg" alt="Pulpit rock" width="50" height="50"></a>
				<a href="images/image16.gif"><img src="images/image16.gif" alt="Pulpit rock" width="50" height="50"></a>
			<a href="images/pia12832-browse.jpg"><img src="images/pia12832-browse.jpg" alt="Pulpit rock" width="50" height="50"></a>
			</div>
		</div>
	</div>
	<?php include 'php/html/infobar.php'; ?>
</body>
</html>
