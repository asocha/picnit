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

			executeSearch();
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
	<script type="text/javascript">
		function executeSearch() {
			var term = $("#Searchterm").val();
			if(term.length < 1)
				return false;

			//Clear prev search terms
			$("#result").empty();

			var type = $("#searchtype").val();
			if(type === 'member')
				executeMemberSearch(term);
			else if(type === 'photo')
				executePhotoSearch(term);
			else if(type === 'album')
				executeAlbumSearch(term);
			else if(type === 'category')
				executeCategorySearch(term);
			
			return true;
		}

		function executeMemberSearch(input) {
			var members = filterMembers(input);

			var line = "<div class='searchtitle'>members</div>";
			for(x in members) {
				line+="<div class='searchresult' id='memberresult"+members[x]['member_id']+"' username='"+members[x]['username']+"'>";
				line+="<div class='memrestitle'>"+members[x]['username']+"</div>";
				line+="</div>";
			}
			$("#results").html(line);
			
			$(".searchresult").click(function() {
				window.location = '/picnit/profile/' + $(this).attr('username');
			});
		}

		function executePhotoSearch(input) {
			var images = getImagesByName(input);

			var line = "<div class='searchtitle'>photos by name</div>";
			for(x in images) {
				line+="<div class='searchresult' id='photoresult"+images[x]['image_id']+"' goto='"+images[x]['album_id']+"#imagesection"+images[x]['image_id']+"'>";
				line+="<div class='photorestitle'>"+images[x]['name']+"</div>";
				line+="<div class='photoresuser'>"+images[x]['username']+"</div>";
				line+="<div class='photoresdate'>"+images[x]['date_added']+"</div>";
				line+="<div class='photoresdesc'>"+images[x]['description']+"</div>";
				line+="</div>";
			}
			$("#results").html(line);
			
			$(".searchresult").click(function() {
				window.location = '/picnit/album/' + $(this).attr('goto');
			});
		}
		
		function executeAlbumSearch(input) {
			var albums = getAlbumsByName(input);

			var line = "<div class='searchtitle'>albums</div>";
			for(x in albums) {
				line+="<div class='searchresult' id='albumresult"+albums[x]['album_id']+"' goto='"+albums[x]['album_id']+"'>";
				line+="<div class='albumrestitle'>"+albums[x]['name']+"</div>";
				line+="<div class='albumresuser'>"+albums[x]['username']+"</div>";
				line+="<div class='albumresdate'>"+albums[x]['date_created']+"</div>";
				line+="<div class='albumresdesc'>"+albums[x]['description']+"</div>";
				line+="</div>";
			}
			$("#results").html(line);

			$(".searchresult").click(function() {
				window.location = '/picnit/album/' + $(this).attr("goto");	
			});
		}

		function executeCategorySearch(input) {
			var images = getImagesByCategory(input);

			var line = "<div class='searchtitle'>photos by category</div>";
			for(x in images) {
				line+="<div class='searchresult' id='catresult"+images[x]['image_id']+"' goto='"+images[x]['album_id']+"#imagesection"+images[x]['image_id']+"'>";
				line+="<div class='catrestitle'>"+images[x]['name']+"</div>";
				line+="<div class='catresuser'>"+images[x]['username']+"</div>";
				line+="<div class='catresdate'>"+images[x]['date_added']+"</div>";
				line+="<div class='catresdesc'>"+images[x]['description']+"</div>";
				line+="</div>";
			}
			$("#results").html(line);
			
			$(".searchresult").click(function() {
				window.location = '/picnit/album/' + $(this).attr('goto');
			});
		}
	</script>
	<?php info(); ?>
	<?php signup(); ?>
	<?php imageview();?>
</body>
</html>
