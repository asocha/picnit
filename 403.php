<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/Error.css"/>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="/picnit/css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>welcome to picnit!</title>
	<?php require_once(dirname(__FILE__) . '/php/general.php'); ?>
	<script type="text/javascript" src="/picnit/js/general.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/member.js"></script>
	<script>
	window.onload = function() {
		document.getElementById('overlay').style.visibility="hidden";
		document.getElementById('signupbar').style.visibility="hidden";	
		document.getElementById('signupbut').addEventListener('click',showsignup,false);
		document.getElementById('cancel').addEventListener('click',hidesignup,false);
		document.getElementById('overlay').addEventListener('click',hidesignup,false);
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
	<?php include dirname(__FILE__) . '/php/html/topbar.php'; menubar(); ?>

	<img src="images/gui/largelogo.png" alt="picnit.net" height="150" id="logo">

	<div id="searchbar">
		<form id="search" action="index.php" method="post">
			<span id="searchlabel"><label for="Searchterm">Search:</label></span><span><input type="text" name="Searchterm" id="Searchterm"/></span>		
			<span><input type="submit" name="search" id="search" value="Submit"/></span>
		</form>
	</div>
	<div id="gallery" name="gallery" class="gallery">
		<h1>ERROR 403: Forbidden</h1>
	</div>
	<div id="info" name="info">
		<div id="infotext">
			<div>picnit.net</div>
			<div>A PhotoDolo Project</div>
		</div>
	</div>
	<div id="overlay">
	</div>

	<?php signup(); ?>
</body>
</html>
