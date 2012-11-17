<div id="menubar">
	<?php
		//Require general if not already
		//require_once('/picnit/php/general.php');

		//Only show this section if user isn't logged in
		if(!isLoggedIn()) {
	?>
	<form id="signupbut">
		<span><input type="button" id="sign" value="Sign Up"/></span>
	</form>

	<form id="signinform" action="index.php" onsubmit="return login();">
		<span><label for="Username">username:</label><input type="text" id="Username"/></span>
		<span><label for="Password">password:</label><input type="password" id="Password"/></span>
		<span><input type="submit" id="signin" value="Sign In"/></span>
	</form>
	<form id="homebut" action="index.php">
		<span><input type="submit" id="home" value="Home"/></span>
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
