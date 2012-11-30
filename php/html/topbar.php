<?php
//Function that returns the top bar
function menubar() {
?>
<div id="menubar" class="panels">
	<script type="text/javascript" charset="utf-8">
	  $(window).load(function() {
		//Set sign-out button
		$('#signoutbut').click(function() { logout(true); });

		//Load flexslider
		if($('.flexslider').length > 0)
			$('.flexslider').flexslider({
				animation: "slide",
				minItems: 1
			});
	  });
	function showsignup() {
		document.getElementById('overlay').style.visibility="visible";
		document.getElementById('signupbar').style.visibility="visible";
	}
	</script>
	<?php
		//Require general if not already
		//require_once('/picnit/php/general.php');

		//Only show this section if user isn't logged in
		if(!isLoggedIn()) {
	?>
	<form id="signupbut">
		<span><input type="button" id="sign" class="buttons" value="Sign Up"/></span>
	</form>

	<form id="signinform" action="/picnit/index.php" onsubmit="return login();">
		<span><label for="Username">username: </label><input type="text" id="Username" class="inputs" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/></span>
		<span><label for="Password">password: </label><input type="password" id="Password" class="inputs" pattern=".{5,}" title="Must be at least 5 characters" required="required"/></span>
		<span><input type="submit" id="signin" class="buttons" value="Sign In"/></span>
	</form>
	<form id="homebut" action="/picnit/index.php">
		<span><input type="submit" id="home" class="buttons" value="home"/></span>
	</form>
	<?php
		} //End if
		else {
	?>
	<form id="homebut" action="/picnit/index.php">
		<span><input type="submit" id="home" class="buttons" value="home"/></span>
	</form>
	
	<form id="signoutbut">
			<span><input type="button" id="sign" class="buttons" value="sign out"/></span>
		</form>
	<div id="userinfo">
		<a href="/picnit/profile/<?php echo $_COOKIE['username']; ?>"><span id="dispname"><?php echo $_COOKIE['username']; ?></a>
	</div>
		<?php
			}
		?>
	</div>
	<script type="text/javascript">
	$('#signupbut').click(showsignup);
	</script>
<?php
}

//Function that returns the signup section
function signup() {
?>
	<script>

	function hidesignup() {
		document.getElementById('overlay').style.visibility="hidden";
		document.getElementById('signupbar').style.visibility="hidden";
	}
	</script>
<?php
	if(!isLoggedIn()) {
?>
	<div id="overlay" class="overlays">
	</div>
	<div id="signupbar" class="panels">
		<form id="signupform" onsubmit="return createUser();">
		<p><div>
			<label for="Newusername">username: </label>
			<input type="text" id="Newusername" class="inputs" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/>
		</div></p>
		<p><div>
			<label for="Newpassword">password: </label>
			<input type="password" id="Newpassword" class="inputs" pattern=".{5,}" title="Must be at least 5 characters" required="required"/>
		</div>
		<div>
			<label for="Confirmpassword">confirm password: </label>
			<input type="password" id="Confirmpassword" class="inputs" required="required" oninput="validatePassword(document.getElementById('Newpassword'), this);"/>
		</div></p>
		<p><div>
			<label for="name">email: </label>
			<input type="email" id="email" class="inputs" required="required"/>
		</div></p>
			<p><div><input type="submit" id="signup" class="buttons" value="sign up"/></div></p>
			<p><div><input type="button" id="cancel" class="buttons" value="cancel"/></div><p>
		</form>
	</div>
</div>
	<script type="text/javascript">
	$('#cancel').click(hidesignup);
	$('#overlay').click(hidesignup);
	</script>
	<?php
		}
	}
	?>
<?php 
//Function that returns the info bar
function info() {
?>
<div id="info" name="info" class="panels">
	<div id="infotext">
		<div>picnit.net</div>
		<div>A PhotoDolo Project</div>
	</div>
</div>
	<?php
	}

//Function that returns the image viewer
function imageview() {
?>
<script>
	function hideViewer() {
		document.getElementById('imgoverlay').style.visibility="hidden";
		document.getElementById('imgviewer').style.visibility="hidden";
		document.getElementById('viewercontainer').style.visibility="hidden";
	}

	function showViewer() {
		document.getElementById('imgoverlay').style.visibility="visible";
		document.getElementById('imgviewer').style.visibility="visible";
		document.getElementById('viewercontainer').style.visibility="visible";
	}
</script>
<div id="imgoverlay" class="overlays">
</div>
<div id="viewercontainer">
<div id="imgviewer">
	<div id="editor">
		<div id="menu">
		</div>
		<div id="image" class="panels">
			<img id="theimage" src="/picnit/images/gui/test.jpg" alt="Pulpit rock"/>
		</div>
	</div>
	<div id="comments" class="panels">
		<h2>comments</h2>
	</div>
</div>
</div>
	<script type="text/javascript">
	$('#imgoverlay').click(hideViewer);
	$('#overlay').click(hideViewer);
	</script>
	<?php
	}

//Function that returns the search bar
function searchbar() {

?>
<div id="searchbar" class="panels">
	<form id="search" action="index.php" method="post">
		<span id="searchlabel"><label for="Searchterm">search:</label></span><span><input type="text" name="Searchterm" id="Searchterm" class="inputs"/></span>		
		<span><input type="submit" name="search" id="search" class="buttons" value="Submit"/></span>
	</form>
</div>
	<?php
	}

//Function that returns the image uploader
function uploader($album_id) {

?>
	<script>
	function hideUploader() {
		document.getElementById('uploadoverlay').style.visibility="hidden";
		document.getElementById('uploadbar').style.visibility="hidden";
	}

	function showUploader() {
		document.getElementById('uploadoverlay').style.visibility="visible";
		document.getElementById('uploadbar').style.visibility="visible";
	}
</script>
	<div id="uploadoverlay" class="overlays">
	</div>
	<div id="uploadbar" class="panels">
		<form id="uploadform" onsubmit="return saveImage();">
		<p><div><label for="imagename">image name: </label><input type="text" id="imagename" class="inputs" pattern=".{3,63}" title="Image Name must contain between 3 and 63 characters." required="required"/></div>
		<div><label for="imagedesc">description: </label><input type="text" id="imagedesc" class="inputs"/></div></p>
		<p><div>
			<select id="publicness" class="inputs">
				<option selected value="0">Public</option>
				<option value="1">Followers</option>
				<option value="2">Private</option>
			</select>
		</div></p>
		<p><div><input type="file" id="inpimage" class="buttons" required="required" accept="image/*" value="browse"/></div></p>
		<p><div><input type="submit" id="imgsubmit" class="buttons" value="submit"/></div></p>
		<p><div><input type="button" id="imgcancel" class="buttons" value="cancel"/></div></p>
		<input type="hidden" id="albumid" value="<?php echo $album_id; ?>"/>
		</form>
	</div>
	<script type="text/javascript">
	$('#imgcancel').click(hideUploader);
	$('#uploadoverlay').click(hideUploader);
	</script>
	<?php
	}

//Function that returns the album creator
function albumcreator() {
	?>
	<script>
	function hideAlbumCreator() {
		document.getElementById('albumoverlay').style.visibility="hidden";
		document.getElementById('albumbar').style.visibility="hidden";
	}

	function showAlbumCreator() {
		document.getElementById('albumoverlay').style.visibility="visible";
		document.getElementById('albumbar').style.visibility="visible";
	}
	</script>
	<div id="albumoverlay" class="overlays">
	</div>
	<div id="albumbar" class="panels">
		<form id="albumform" onsubmit="return createAlbum();">
		<p><div><label for="albumname">album name: </label><input type="text" id="albumname" class="inputs" pattern=".{3,63}" title="Album Name must contain between 3 and 63 characters." required="required"/></div>
		<div><label for="albumdesc">description: </label><textarea id="albumdesc"></textarea></div></p>
		<p><div><input type="submit" id="albsubmit" class="buttons" value="submit"/></div></p>
		<p><div><input type="button" id="albcancel" class="buttons" value="cancel"/></div></p>
		</form>
	</div>
	<script type="text/javascript">
	$('#albcancel').click(hideAlbumCreator);
	$('#albumoverlay').click(hideAlbumCreator);
	</script>

	<?php
	}
	
	//Function that returns the tag bar
function tagbar() {
	?>
	<script>
	function hideTag() {
		document.getElementById('tagoverlay').style.visibility="hidden";
		document.getElementById('tagbar').style.visibility="hidden";
	}

	function showTag() {
		document.getElementById('tagoverlay').style.visibility="visible";
		document.getElementById('tagbar').style.visibility="visible";
	}
	</script>
	<div id="tagoverlay" class="overlays">
	</div>
	<div id="tagbar" class="panels">
		<form id="tagform">
			<div>
			<span><input type="button" id="tagcancel" class="buttons" value="cancel"/></span>
			<span><input type="submit" id="tagsubmit" class="buttons" value="+"/></span>
			<span id="tagspan"><label for="tagname">tag: </label><input id="tagname" class="inputs" pattern="[a-zA-Z]{3,15}" title="Tag Name must contain between 3 and 15 letters." required="required"/></span>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		$(function() {
			$('#tagname').autocomplete({
				source: ["dogs","doggies","donuts"],
				minLength: 2,
				select: function( event, ui ) {
					log( ui.item? "Selected: " + ui.item.value  :  "Nothing selected, input was " + this.value );
				}
			});
		});
		$('#tagoverlay').click(hideTag);
		$('#tagcancel').click(hideTag);
		$('#tagsubmit').click( function() {
			var image_id = $('#tagname').attr('image_id');
			var mem_or_cat = $("#tagname").attr('tagtype');
			var tag = null;

			var res = addTag();
		});
	</script>
	
	<?php
	}
	?>
