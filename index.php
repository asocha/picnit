<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>welcome to picnit!</title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/libraries/jquery.tinycarousel.min.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#slider1').tinycarousel({display: 2});
			$('#slider2').tinycarousel({display: 2});
			$('#slider3').tinycarousel({display: 2});
			$('#slider4').tinycarousel({display: 2});
			$('#slider5').tinycarousel({display: 2});
		});
	</script>
	<script>
	window.onload = function() {
		if(isLoggedIn()) {
			document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
		}
		else {
			document.getElementById('signupbut').addEventListener('click',showsignup,false);
			document.getElementById('cancel').addEventListener('click',hidesignup,false);
			document.getElementById('overlay').addEventListener('click',hidesignup,false);
			document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
		}	
	}
	</script>
</head>
<body>
	<?php menubar(); ?>
	<div>
	<img src="images/gui/largelogo.png" alt="picnit.net" height="150" id="logo">
	</div>
	<?php searchbar(); ?>
	<div id="gallery" name="gallery" class="panels">
		<div id="slider1">
		<a class="buttonsalt prev" href="#">left</a>
		<div class="viewport">
			<ul class="overview">
				<li><img src="images/gui/picture6.jpg" width="100"/></li>
				<li><img src="images/gui/picture5.jpg" width="100"/></li>
				<li><img src="images/gui/picture4.jpg" width="100"/></li>		
				<li><img src="images/gui/picture3.jpg" width="100"/></li>
				<li><img src="images/gui/picture2.jpg" width="100"/></li>
				<li><img src="images/gui/picture1.jpg" width="100"/></li>
			</ul>
		</div>
		<a class="buttonsalt next" href="#">right</a>
	</div>
	<div id="slider2">
		<a class="buttonsalt prev" href="#">left</a>
		<div class="viewport">
			<ul class="overview">
				<li><img src="images/gui/picture6.jpg" width="100"/></li>
				<li><img src="images/gui/picture5.jpg" width="100"/></li>
				<li><img src="images/gui/picture4.jpg" width="100"/></li>			
				<li><img src="images/gui/picture3.jpg" width="100"/></li>
				<li><img src="images/gui/picture2.jpg" width="100"/></li>
				<li><img src="images/gui/picture1.jpg" width="100"/></li>
			</ul>
		</div>
		<a class="buttonsalt next" href="#">right</a>
	</div>
	<div id="slider3">
		<a class="buttonsalt prev" href="#">left</a>
		<div class="viewport">
			<ul class="overview">
				<li><img src="images/gui/picture6.jpg" width="100"/></li>
				<li><img src="images/gui/picture5.jpg" width="100"/></li>
				<li><img src="images/gui/picture4.jpg" width="100"/></li>			
				<li><img src="images/gui/picture3.jpg" width="100"/></li>
				<li><img src="images/gui/picture2.jpg" width="100"/></li>
				<li><img src="images/gui/picture1.jpg" width="100"/></li>
			</ul>
		</div>
		<a class="buttonsalt next" href="#">right</a>
	</div>
	<div id="slider4">
		<a class="buttonsalt prev" href="#">left</a>
		<div class="viewport">
			<ul class="overview">
				<li><img src="images/gui/picture6.jpg" width="100"/></li>
				<li><img src="images/gui/picture5.jpg" width="100"/></li>
				<li><img src="images/gui/picture4.jpg" width="100"/></li>			
				<li><img src="images/gui/picture3.jpg" width="100"/></li>
				<li><img src="images/gui/picture2.jpg" width="100"/></li>
				<li><img src="images/gui/picture1.jpg" width="100"/></li>
			</ul>
		</div>
		<a class="buttonsalt next" href="#">right</a>
	</div>
	<div id="slider5">
		<a class="buttonsalt prev" href="#">left</a>
		<div class="viewport">
			<ul class="overview">
				<li><img src="images/gui/picture6.jpg" width="100"/></li>
				<li><img src="images/gui/picture5.jpg" width="100"/></li>
				<li><img src="images/gui/picture4.jpg" width="100"/></li>			
				<li><img src="images/gui/picture3.jpg" width="100"/></li>
				<li><img src="images/gui/picture2.jpg" width="100"/></li>
				<li><img src="images/gui/picture1.jpg" width="100"/></li>
			</ul>
		</div>
		<a class="buttonsalt next" href="#">right</a>
	</div>
	</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php imageview(); ?>
	<?php uploader(); ?>
</body>
</html>
