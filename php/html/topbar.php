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
		<span><label for="Username">username: </label><input type="text" id="Username" class="inputs"/></span>
		<span><label for="Password">password: </label><input type="password" id="Password" class="inputs"/></span>
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
