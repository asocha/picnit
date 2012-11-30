<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="/picnit/css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>Search Results</title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="/picnit/js/general.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/member.js"></script>
</head>
<body>
	<?php menubar(); ?>
	<div>
		<h1>search</h1>
	</div>

	<?php searchbar(); ?>
	<div id="results" class="panels">

	</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php imageview();?>
</body>
</html>
