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
	<script type="text/javascript" src="/picnit/js/libraries/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery.transit.min.js"></script>
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
				if(document.getElementById('albcancel')) {
					document.getElementById('albumbut').addEventListener('click',showAlbumCreator,false);
				}
			}
			else {
				if(document.getElementById('albcancel')) {
					document.getElementById('albumbut').addEventListener('click',showAlbumCreator,false);
				}
			}

			//Hashing system
			window.onhashchange = function() {
				var hash = window.location.hash;
				if(hash === '#favorites')
					changePanel(function() {
						createFavoritesElements(<?php echo $profile['member_id']; ?>, <?php if (isset($_COOKIE['member_id'])) echo $_COOKIE['member_id']; else echo -1;?>);
					});
				else if(hash === '#followers')
					changePanel(function() {
						createFollowersElements();
					});
				else if(hash === '#following')
					changePanel(function() {
						createFolloweesElements();
					});
				else if(hash === '#requests')
					changePanel(function() {
						createFollowReqElements();
					});
				else if(hash === '#tagged')
					changePanel(function() {
						createTaggedElements(<?php echo $profile['member_id']; ?>, "<?php echo $profile['username']; ?>", <?php if (isset($_COOKIE['member_id'])) echo $_COOKIE['member_id']; else echo -1;?>);
					});
				else if(hash === '#albums') 
					changePanel(function() {
						createAlbumElements(<?php echo $profile['member_id'];?>);
					});
				else
					window.location.hash = '#albums';
			};

			window.onhashchange();
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
				<input id="followuserbut" class="buttons louterbuttons" type="button" value="pending"/>
				<?php
					}
					else if($profile['isfollowing']) {
				?>
				<input id="followuserbut" class="buttons louterbuttons" type="button" value="unfollow"/>
				<?php
					}
					else if (isset($_COOKIE['member_id'])){
				?>
				<input id="followuserbut" class="buttons louterbuttons" type="button" value="follow"/>
				<?php
					}
					else {
						echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
					}
				?>
				<input id="albumsbut" class="buttons innerbuttons" type="button" value="albums"/>
				<input id="favoritebut" class="buttons innerbuttons" type="button" value="favorites"/>
				<input id="taggedbut" class="buttons innerbuttons" type="button" value="tagged"/>
				<input id="followersbut" class="buttons innerbuttons" type="button" value="followers"/>
				<input id="followeesbut" class="buttons innerbuttons" type="button" value="following"/>
				<input id="requestsbut" class="buttons innerbuttons" type="button" value="<?php echo $profile['request_count'];?> requests"/>
				<?php
					//Add suspend button
					if($admin && $profile['username'] !== $_COOKIE['username']) {
				?>
				<input id="suspenduserbut" class="buttons routerbuttons" type="button" value="<?php echo ($profile['is_suspended']==true)? 'unsuspend' : 'suspend'; ?>"/>
				<?php
					}
				?>
				<script type="text/javascript">
					$('#albumsbut').click(function() {
						window.location.hash = 'albums';
					});
					$('#favoritebut').click(function() {
						window.location.hash = 'favorites';
					});
					$('#taggedbut').click(function() {
						window.location.hash = 'tagged';
					});
					$('#followersbut').click(function() {
						window.location.hash = 'followers';
					});
					$('#followeesbut').click(function() {
						window.location.hash = 'following';
					});
					$('#requestsbut').click(function() {
						window.location.hash = 'requests';
					});
					if($('#followuserbut').length > 0)
						$('#followuserbut').click(function() {
							var val = $(this).val();
							if(val === 'follow') {
								if(requestFollow(<?php echo $profile['member_id']; ?>))
									$(this).val('pending');
							}
							else if(val === 'unfollow') {
								var id = $(this).attr('id').substring(9);
								var button = this;
                                                                showConfirm('Are you sure you want to unfollow this user?', (function(id, obj) {
                                                                        return function() {
                                                                                if(unfollow(<?php echo $profile['member_id'];?>))
                                                                                        $(button).val('follow');
                                                                        };
                                                                })(id, this));
							}
						});
					if($('#suspenduserbut').length > 0)
						$('#suspenduserbut').click(function() {
							var val = $(this).val();
							if(val === 'suspend') {
								if(suspendUser(<?php echo $profile['member_id']; ?>))
									$(this).val('unsuspend');
							}
							else if(val === 'unsuspend') {
								if(unsuspendUser(<?php echo $profile['member_id']; ?>))
									$(this).val('suspend');
							}
						});
				</script>
			</div>
			<div id="thumbnail-display">
			</div>
		</div>
	<?php info(); ?>
	<?php imageview(); ?>
	<?php if($profile['username'] === $_COOKIE['username']) albumcreator(); ?>
	<?php signup(); ?>
	<?php confirmbar(); ?>
</body>
</html>
