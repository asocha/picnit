<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<link rel="stylesheet" href="/picnit/css/flexslider.css" type="text/css">
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>welcome to picnit!</title>
	<?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="/picnit/js/libraries/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script type="text/javascript" src="js/image.js"></script>
	<script type="text/javascript" src="js/tag.js"></script>
	<script type="text/javascript">
		window.onload = function() {
			//Load flexslider stuff
			
			//Create function to add images
			var addImages = function(cat_id, category) {
				var images = getImageDataByCategory(cat_id, 3);

				var line = "";
				for(x in images) {
					line += "<li>";
					line += "<img src='data:" + images[x]['image_type'] + ";base64," + images[x]['image'] + "' alt='" + images[x]['name'] + "' cat='"+category+"'/>";
					line += "</li>";
				}
				$(slideshow).append(line);
			};

			//Get the categories
			var cats = getTopCategories(getCookie('member_id')? getCookie('member_id') : "", 5);

			$('#catlabel').html(cats[0]['category']);
			for(x in cats) {
				addImages(cats[x]["category_id"], cats[x]["category"]);
			}

			$('.flexslider').flexslider({
				after: function(slider) {
					$('#catlabel').html(slider.slides[slider.currentSlide].children[0].attributes[2].value);
				}
			});
		};
	</script>
</head>
<body>
	<?php menubar(); ?>
	<div>
	<img src="images/gui/smalllogo.png" alt="picnit.net" height="150" id="logo">
	</div>
	<?php searchbar(); ?>
	<div id="gallery" name="gallery" class="panels">
		<div id="cat" class="indexcat">
			<div id="catlabel" class="catlabel"></div>
			<div class="flexslider">
				<ul id="slideshow" class='slides'>
				</ul>
			</div>
		</div>
	</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php imageview(); ?>
	<?php uploader(); ?>
	<?php confirmbar(); ?>
</body>
</html>
