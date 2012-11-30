<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="/picnit/css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>welcome to picnit!</title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="/picnit/js/general.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/member.js"></script>
</head>
<body>
	<?php menubar(); ?>
	<img src="images/gui/largelogo.png" alt="picnit.net" height="150" id="logo">
	<h1 id="errorheader">ERROR 403: Forbidden</h1>
	<?php info(); ?>
	<?php signup(); ?>
</body>
</html>
