<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>Search Results</title>
	<script src="js/libraries/jquery-1.8.2.min.jss"></script>
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
		<h1>search</h1>
	</div>

	<?php include 'php/html/searchbar.php'; ?>
	<div id="results" class="panels">

	</div>
	<?php include 'php/html/infobar.php'; ?>
</body>
</html>
