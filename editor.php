<!DOCTYPE html>
<?php
	require_once('php/general.php');

	//Make sure id has been passed
	if($_GET['image_id'] === "")
		header('Location: /picnit/404.php');

	//Get data of this album
	$fields = array(
		'action' => 'memberData',
		'username' => urlencode($_COOKIE['username']),
		'key' => urlencode($_COOKIE['key']),
		'tusername' => urlencode($_COOKIE['username'])
	);

	//Send request
	$res = picnitRequest('api/member.php', $fields);

	if($res['status'] === 200) {
		$profile = json_decode($res['result'], true);
		$profile = $albuminfo['list'][0];
	}

	$fields['image_id'] = urlencode($_GET['image_id']);
	$fields['action'] = 'getImages';
	unset($fields['tusername']);

	$res = picnitRequest('api/image.php', $fields);

	if($res['status'] === 200) {
		$image = json_decode($res['result'], true);
		$image = $image[0];

		if($profile['member_id'] != $image['owner_id'])
			header('Location: 403.php');
		else if(!$image)
			header('Location: 404.php');
	}
?>
<html lang="en">
<head>
  <meta charset="utf8">
  <title>Editor</title>

  <?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<link href='http://fonts.googleapis.com/css?family=Source+Code+Pro|Raleway:400,200,700' rel='stylesheet' type='text/css'>	
	<script type='text/javascript' src='/picnit/js/editorjs/9ec45af399351c8877c311e247b40e64.js'></script>
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
  <script src="js/editorjs/editor.js"></script>

  	<script type="text/javascript">
	</script>
</head>
<body>
	<?php menubar(); ?>
<div id="Content" class="panels">
  <h3>Image Editor</h3>
  <button type="button" class="buttons" id="savebut">save</button>
  <script type="text/javascript">
	$('#savebut').click(function() {
		var img = document.getElementById('example');
		var imgData = img.toDataURL();

		if(resaveImage(<?php echo $image['image_id']; ?>, imgData.substring(imgData.indexOf("base64,")+7)))
			window.location = "/picnit/album/<?php echo $image['album_id']; ?>#imagesection<?php echo $image['image_id']; ?>";
	});
  </script>
  <div id="canvascontainer">
  <img id="example" src="<?php echo "data:$image[image_type];base64,$image[image]"; ?>">
  </div>
  <div id="Filters">
  <div id="bars">
    <div class="Filter">
      <div class="FilterName">
        <p>brightness</p>
      </div>

      <div class="FilterSetting">
        <input
          type="range" 
          min="-100"
          max="100"
          step="1"
          value="0"
          data-filter="brightness"
        >
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>contrast</p>
      </div>

      <div class="FilterSetting">
        <input
          type="range" 
          min="-100"
          max="100"
          step="1"
          value="0"
          data-filter="contrast"
        >
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>saturation</p>
      </div>

      <div class="FilterSetting">
        <input
          type="range" 
          min="-100"
          max="100"
          step="1"
          value="0"
          data-filter="saturation"
        >
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>sepia</p>
      </div>

      <div class="FilterSetting">
        <input
          type="range" 
          min="0"
          max="100"
          step="1"
          value="0"
          data-filter="sepia"
        >
        <span class="FilterValue">0</span>
      </div>
    </div>
  
    <div class="Filter">
      <div class="FilterName">
        <p>gamma</p>
      </div>

      <div class="FilterSetting">
        <input
          type="range" 
          min="0"
          max="10"
          step="0.1"
          value="0"
          data-filter="gamma"
        >
        <span class="FilterValue">0</span>
      </div>
    </div>
</div>
  <div id="PresetFilters">
  
    <button type="button" class="buttons editorbuts"><a data-preset="vintage">Vintage</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="clarity">Clarity</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="sinCity">Sin City</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="sunrise">Sunrise</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="crossProcess">Cross Process</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="jarques">Jarques</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="pinhole">Pinhole</a></button>
  
    <button type="button" class="buttons editorbuts"><a data-preset="oldBoot">Old Boot</a></button>
  
  </div>

  <div class="Clear"></div>
  </div>
  	
</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php confirmbar(); ?>
</body>
</html>
