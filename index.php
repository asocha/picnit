<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="css/index.css"/>
	<link href='http://fonts.googleapis.com/css?family=Concert+One' rel='stylesheet' type='text/css'>
	<title>welcome to picnit!</title>
	<script type="text/javascript" src="js/general.js"></script>
	<script type="text/javascript" src="js/libraries/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="js/member.js"></script>
	<script>
	window.onload = function() {
		document.getElementById('overlay').style.visibility="hidden";
		document.getElementById('signupbar').style.visibility="hidden";	
		document.getElementById('signupbut').addEventListener('click',showsignup,false);
		document.getElementById('cancel').addEventListener('click',hidesignup,false);
		document.getElementById('overlay').addEventListener('click',hidesignup,false);
	}

	function showsignup() {
		document.getElementById('overlay').style.visibility="visible";
		document.getElementById('signupbar').style.visibility="visible";
	}

	function hidesignup() {
		document.getElementById('overlay').style.visibility="hidden";
		document.getElementById('signupbar').style.visibility="hidden";
	}
	</script>
</head>
<body>
	<div id="menubar">
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
	</div>

	<img src="images/gui/largelogo.png" alt="picnit.net" height="150">

	<div id="searchbar">
		<form id="search" action="index.php" method="post">
			<span id="searchlabel"><label for="Searchterm">Search:</label></span><span><input type="text" name="Searchterm" id="Searchterm"/></span>		
			<span><input type="submit" name="search" id="search" value="Submit"/></span>
		</form>
	</div>
	<div id="gallery" name="gallery" class="gallery">
	</div>
	<div id="info" name="info">
		<div id="infotext">
			<div>picnit.net</div>
			<div>A PhotoDolo Project</div>
		</div>
	</div>
	<div id="overlay">
	</div>
	<div id="signupbar">
		<form id="signupform" onsubmit="return createUser();">
			<p><div>
				<label for="Newusername">username: </label>
				<input type="text" id="Newusername" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/>
			</div></p>
			<p><div>
				<label for="Newpassword">password: </label>
				<input type="password" id="Newpassword" pattern".{5,}" title="Must be at least 3 characters" required="required"/>
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
</body>
</html>