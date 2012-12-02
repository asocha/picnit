<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf8">
  <title>Editor</title>

  <?php require_once('php/general.php'); ?>
	<?php require_once('php/html/topbar.php'); ?>
	<link href='http://fonts.googleapis.com/css?family=Source+Code+Pro|Raleway:400,200,700' rel='stylesheet' type='text/css'>	
	<script type='text/javascript' src='js/editorjs/9ec45af399351c8877c311e247b40e64.js'></script>
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
</head>
<body>
	<?php menubar(); ?>
<div id="Content" class="panels">
  <h3>Image Editor</h3>
  
  <img id="example" src="images/gui/picture3.jpg">

  <div id="Filters">
  
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

  <div id="PresetFilters">
  
    <button type="button"><a data-preset="vintage">Vintage</a></button>
  
    <button type="button"><a data-preset="clarity">Clarity</a></button>
  
    <button type="button"><a data-preset="sinCity">Sin City</a></button>
  
    <button type="button"><a data-preset="sunrise">Sunrise</a></button>
  
    <button type="button"><a data-preset="crossProcess">Cross Process</a></button>
  
    <button type="button"><a data-preset="jarques">Jarques</a></button>
  
    <button type="button"><a data-preset="pinhole">Pinhole</a></button>
  
    <button type="button"><a data-preset="oldBoot">Old Boot</a></button>
  
  </div>

  <div class="Clear"></div>
  </div>
  	
</div>
	<?php info(); ?>
	<?php signup(); ?>
	<?php confirmbar(); ?>
</body>
</html>
