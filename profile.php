<!DOCTYPE html>
<?php
	//Get general.php for DB calls
	require_once('php/general.php');

	//Get username from url
	$username = substr($_SERVER['REQUEST_URI'], strripos($_SERVER['REQUEST_URI'], "/") + 1);

	//Make sure they send in a username
	if($username === "")
		if(isLoggedIn())
			$_GET['username'] = $_COOKIE['username'];
		else
			header("Location: /picnit/index.php");

	//See if this user exists
	$fields = array(
		'action' => 'memberData',
		'username' => urlencode($_COOKIE['username']),
		'key' => urlencode($_COOKIE['key']),
		'tusername' => urlencode($username)
	);

	$res = picnitRequest('api/member.php', $fields);

	//If does, get user info
	if($res['status'] == 200) 
		$profile = json_decode($res['result'], true);
	else
		header('Location: /picnit/404.php');
	
	//See if we are an admin
	$fields = array(
		'action' => 'memberData',
		'username' => urlencode($_COOKIE['username']),
		'key' => urlencode($_COOKIE['key']),
		'tusername' => $_COOKIE['username']
	);

	$res = picnitRequest('api/member.php', $fields);

	$admin = false;
	if($res['status'] == 200) {
		$temp = json_decode($res['result'], true);
		$admin = ($temp['is_admin'] == '1')? true : false;
	}
?>
<html>
<title><?php echo  $profile['username']; ?>'s profile!</title>
<head>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<link rel="stylesheet" href="/picnit/css/style.css" type="text/css">
	<link rel="stylesheet" href="/picnit/css/flexslider.css" type="text/css">
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="/picnit/js/general.js"></script>
	<script type="text/javascript" src="/picnit/js/member.js"></script>
	<script type="text/javascript" src="/picnit/js/album.js"></script>
	<script type="text/javascript" src="/picnit/js/image.js"></script>
	<script type="text/javascript" src="/picnit/js/tag.js"></script>
	<script type="text/javascript" src="/picnit/js/profile.js"></script>
	<script>
		window.onload = function() {
			if(isLoggedIn()) {
				document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
				if(document.getElementById('albcancel')) {
					document.getElementById('albcancel').addEventListener('click',hideAlbumCreator,false);
					document.getElementById('albumoverlay').addEventListener('click',hideAlbumCreator,false);
					document.getElementById('albumbut').addEventListener('click',showAlbumCreator,false);
				}
			}
			else {
				document.getElementById('signupbut').addEventListener('click',showsignup,false);
				document.getElementById('cancel').addEventListener('click',hidesignup,false);
				document.getElementById('overlay').addEventListener('click',hidesignup,false);
				document.getElementById('imgoverlay').addEventListener('click',hideViewer,false);
				if(document.getElementById('albcancel')) {
					document.getElementById('albcancel').addEventListener('click',hideAlbumCreator,false);
					document.getElementById('albumoverlay').addEventListener('click',hideAlbumCreator,false);
					document.getElementById('albumbut').addEventListener('click',showAlbumCreator,false);
				}
			}

			$('#albumsbut').click();
		}
	</script>
</head>
<body>
	<?php menubar(); ?>
	<div>
		<h1><?php echo  $profile['username']; ?>'s profile</h1>
	</div>
	<?php searchbar(); ?>
	<div id="slideshow" class="flexslider">
		<ul class="slides">
	  	
		</ul>
		<script type="text/javascript">createFlexsliderElements(5,<?php echo $profile['member_id']; ?>);</script>
	</div>
	<div id="user-image-collection">
		<div id="collection" class="panels">
			<div id="usermenu" class="panels">
				<?php
					//Add addalbum or follow button
					if($profile['username'] === $_COOKIE['username']) {
				?>
				<input id="albumbut" class="buttons louterbuttons" type="button" value="add album"/>
				<?php
					}
					else if($profile['requestsent']) {
				?>
				<input id="followuserbut" class="buttons louterbuttons" type="button" value="follow pending"/>
				<?php
					}
					else if($profile['isfollowing']) {
				?>
				<input id="followuserbut" class="buttons louterbuttons" type="button" value="unfollow"/>
				<?php
					}
					else {
				?>
				<input id="followuserbut" class="buttons louterbuttons" type="button" value="follow"/>
				<?php
					}
				?>
				<input id="albumsbut" class="buttons innerbuttons" type="button" value="albums"/>
				<input id="favoritebut" class="buttons innerbuttons" type="button" value="favorites"/>
				<input id="taggedbut" class="buttons innerbuttons" type="button" value="tagged"/>
				<input id="followersbut" class="buttons innerbuttons" type="button" value="followers"/>
				<input id="followeesbut" class="buttons innerbuttons" type="button" value="following"/>
				<?php
					//Add suspend button
					if($admin && $profile['username'] !== $_COOKIE['username']) {
				?>
				<input id="requestsbut" class="buttons innerbuttons" type="button" value="requests"/>
				<input id="suspenduserbut" class="buttons routerbuttons" type="button" value="suspend"/>
				<?php
					}
					else {
				?>
				<input id="requestsbut" class="buttons routerbuttons" type="button" value="requests"/>
				<?php
					}
				?>
				<script type="text/javascript">
					$('#albumsbut').click(function() {
						createAlbumElements(<?php echo $profile['member_id']; ?>)
					});
					$('#favoritebut').click(function() {
						createFavoritesElements();
					});
					$('#taggedbut').click(function() {
						createTaggedElements();
					});
					$('#followersbut').click(function() {
						createFollowersElements();
					});
					$('#followeesbut').click(function() {
						createFolloweesElements();
					});
					$('#requestsbut').click(function() {
						createFollowReqElements();
					});
					if($('#followuserbut').length > 0)
						$('#followuserbut').click(function() {
							var val = $(this).val();
							if(val === 'follow')
								if(requestFollow(<?php echo $profile['member_id']; ?>))
									$(this).val('follow pending');
							else if(val === 'unfollow')
								if(unfollow(<?php echo $profile['member_id']; ?>))
									$(this).val('follow');
						});
					if($('#suspenduserbut').length > 0)
						$('#suspenduserbut').click(function() {

						});
				</script>
			</div>
			<div id="thumbnail-display">
			</div>
		</div>
	</div>
	<?php info(); ?>
	<?php imageview(); ?>
	<?php if($profile['username'] === $_COOKIE['username']) albumcreator(); ?>
	<?php signup(); ?>
	
</body>
</html>
