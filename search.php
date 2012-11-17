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
	<div id="menubar">
		<form id="signoutbut">
			<span><input type="button" id="sign" value="Sign Out"/></span>
		</form>
		<form id="homebut" action="index.php">
			<span><input type="submit" id="home" value="Home"/></span>
		</form>
	</div>
	<div>
		<h1>search</h1>
	</div>

	<div id="searchbar">
		<form id="search" action="index.php" method="post">
			<span id="searchlabel"><label for="Searchterm">Search:</label></span><span><input type="text" name="Searchterm" id="Searchterm"/></span>		
			<span><input type="submit" name="search" id="search" value="Submit"/></span>
		</form>
	</div>
	<div id="results">

	</div>
	<div id="info" name="info">
		<div id="infotext">
			<div>Picnit.net</div>
			<div>A PhotoDolo Project</div>
		</div>
	</div>
</body>
</html>
