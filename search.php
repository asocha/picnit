<!DOCTYPE html>
<?php
	//Get general.php for DB calls
	require_once('php/general.php');

	//See if this user exists
	$fields = array(
		'action' => 'memberData',
		'username' => urlencode($_COOKIE['username']),
		'key' => urlencode($_COOKIE['key']),
		'tusername' => urlencode($_COOKIE['username'])
	);

	$res = picnitRequest('api/member.php', $fields);
	
	//If does, get user info
	if($res['status'] == 200) 
		$profile = json_decode($res['result'], true);
	else
		header('Location: /picnit/404.php');
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="/picnit/css/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>Search Results</title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery.transit.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="/picnit/js/general.js"></script>
	<script type="text/javascript" src="/picnit/js/member.js"></script>
	<script type="text/javascript" src="/picnit/js/album.js"></script>
	<script type="text/javascript" src="/picnit/js/image.js"></script>
	<script type="text/javascript" src="/picnit/js/tag.js"></script>
	<script type="text/javascript" src="/picnit/js/search.js"></script>
	<script type="text/javascript" src="/picnit/js/profile.js"></script>
	<script type="text/javascript">
		window.onload = function() {
			//Load the data into the form
			$("#Searchterm").val('<?php echo $_GET['q']; ?>');
			$("#searchtype").val('<?php echo $_GET['what'] ?>');
		};
	</script>
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
