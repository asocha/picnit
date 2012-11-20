<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>welcome to picnit!</title>
	<?php require_once('php/general.php'); ?>
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script>
	window.onload = function() {
		document.getElementById('signupbut').addEventListener('click',showsignup,false);
		document.getElementById('cancel').addEventListener('click',hidesignup,false);
		document.getElementById('overlay').addEventListener('click',hidesignup,false);
		document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
		document.getElementById('testimg').addEventListener('click',showViewer,false);
	}

	function showsignup() {
		document.getElementById('overlay').style.visibility="visible";
		document.getElementById('signupbar').style.visibility="visible";
	}

	function hidesignup() {
		document.getElementById('overlay').style.visibility="hidden";
		document.getElementById('signupbar').style.visibility="hidden";
	}
	</script>
</head>
<body>
	<?php include 'php/html/topbar.php'; ?>

	<img src="images/gui/largelogo.png" alt="picnit.net" height="150" id="logo">

	<div id="indexsearchbar" class="panels">
		<form id="search" action="index.php" method="post">
			<span id="searchlabel"><label for="Searchterm">search:</label></span><span><input type="text" name="Searchterm" id="Searchterm" class="inputs"/></span>		
			<span><input type="submit" name="search" id="search" class="buttons" value="Submit"/></span>
		</form>
	</div>
	<div id="gallery" name="gallery" class="panels">
		<img id="testimg" src="images/gui/test.jpg" alt="Pulpit rock" height="50px" width="50px"/>
	</div>
	<?php include 'php/html/infobar.php'; ?>
	<?php include 'php/html/signupbar.php'; ?>
	<?php include 'php/html/imageview.php'; ?>
</body>
</html>
