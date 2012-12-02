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
	<script type="text/javascript" src="/picnit/js/libraries/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script type="text/javascript" src="js/image.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			
		});
	</script>
</head>
<body>
	<?php menubar(); ?>
	<div>
	<img src="images/gui/smalllogo.png" alt="picnit.net" height="150" id="logo">
	</div>
	<?php searchbar(); ?>
	<div id="gallery" name="gallery" class="panels">
		<div id="cat1" class="indexcat">
			<div id="cat1label" class="catlabel"></div>
			<div class="flexslider">
				<div id="slideshow1" class='slides'>
				<div>
			<div>
		</div>
		<div id="cat2" class="indexcat">
			<div id="cat2label" class="catlabel"></div>
			<div class="flexslider">
				<div id="slideshow2" class='slides'>
				<div>
			<div>
		</div>
		<div id="cat3" class="indexcat">
			<div id="cat3label" class="catlabel"></div>
			<div class="flexslider">
				<div id="slideshow3" class='slides'>
				<div>
			<div>
		</div>
		<div id="cat4" class="indexcat">
			<div id="cat4label" class="catlabel"></div>
			<div class="flexslider">
				<div id="slideshow4" class='slides'>
				<div>
			<div>
		</div>
		<div id="cat5" class="indexcat">
			<div id="cat5label" class="catlabel"></div>
			<div class="flexslider">
				<div id="slideshow5" class='slides'>
				<div>
			<div>
		</div>
	</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php imageview(); ?>
	<?php uploader(); ?>
	<?php confirmbar(); ?>
</body>
</html>
