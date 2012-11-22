<?php
//Function that returns the top bar
function menubar() {
?>
<div id="menubar" class="panels">
	<?php
		//Require general if not already
		//require_once('/picnit/php/general.php');

		//Only show this section if user isn't logged in
		if(!isLoggedIn()) {
	?>
	<form id="signupbut">
		<span><input type="button" id="sign" class="buttons" value="Sign Up"/></span>
	</form>

	<form id="signinform" action="index.php" onsubmit="return login();">
		<span><label for="Username">username: </label><input type="text" id="Username" class="inputs" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/></span>
		<span><label for="Password">password: </label><input type="password" id="Password" class="inputs" pattern=".{5,}" title="Must be at least 5 characters" required="required"/></span>
		<span><input type="submit" id="signin" class="buttons" value="Sign In"/></span>
	</form>
	<form id="homebut" action="index.php">
		<span><input type="submit" id="home" class="buttons" value="Home"/></span>
	</form>
	<?php
		} //End if
		else {
	?>
	<div id="userinfo">
		<a href="profile.php?username=<?php echo $_COOKIE['username']; ?>"><span id="dispname"><?php echo $_COOKIE['username']; ?></a>
	</div>
		<?php
			}
		?>
	</div>
<?php
}

//Function that returns the signup section
function signup() {
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
	}

	function showViewer() {
		document.getElementById('imgoverlay').style.visibility="visible";
		document.getElementById('imgviewer').style.visibility="visible";
	}
</script>
<div id="imgoverlay" class="overlays">
</div>
<div id="imgviewer">
	<div id="editor">
		<div id="menu">
		</div>
		<div id="image" class="panels">
			<div id="next" class="viewerbuttons">
			</div>
			<div id="prev" class="viewerbuttons">
			</div>
			<img id="theimage" src="images/gui/test.jpg" alt="Pulpit rock" height="200px" width="200px"/>
		</div>
	</div>
	<div id="comments" class="panels">
	</div>
</div>

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
	?>