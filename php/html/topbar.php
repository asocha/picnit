<?php
//Function that returns the top bar
function menubar() {
?>
	<div id="menubar">
		<?php
			//Only show this section if user isn't logged in
			if(!isLoggedIn()) {
		?>
		<form id="signupbut">
			<span><input type="button" id="sign" value="Sign Up"/></span>
		</form>

		<form id="signinform" action="index.php" onsubmit="return login();">
			<span>
				<label for="Username">username:</label>
				<input type="text" id="Username" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/>
			</span>
			<span>
				<label for="Password">password:</label>
				<input type="password" id="Password" pattern=".{5,}" title="Must be at least 5 characters" required="required"/>
			</span>
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
<?php
}

//Function that returns the signup section
function signup() {
?>
	<div id="signupbar">
		<form id="signupform" onsubmit="return createUser();">
		<p><div>
			<label for="Newusername">username: </label>
			<input type="text" id="Newusername" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/>
		</div></p>
		<p><div>
			<label for="Newpassword">password: </label>
			<input type="password" id="Newpassword" pattern=".{5,}" title="Must be at least 5 characters" required="required"/>
		</div>
		<div>
			<label for="Confirmpassword">confirm password: </label>
			<input type="password" id="Confirmpassword" required="required" oninput="validatePassword(document.getElementById('Newpassword'), this);"/>
		</div></p>
		<p><div>
			<label for="name">email: </label>
			<input type="email" id="email" required="required"/>
		</div></p>
			<p><div><input type="submit" id="signup" value="sign up"/></div></p>
			<p><div><input type="button" id="cancel" value="cancel"/></div><p>
		</form>
	</div>
<?php
}
?>
