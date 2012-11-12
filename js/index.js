var loginurl='../api/member.php';

function login() {
	
	var username = $("input#Username").val();
	var password = $("input#Password").val();
	if(password.length < 7) {
		alert('Password or Username is Invalid');
		return false;
	}
	if(username.length < 7 || username.length >15) {
		alert('Password or Username is Invalid');
		return false;
	}
	if(isValid(username)===false || isValid(password)===false) {
		alert('Password or Username is Invalid');
		return false;
	}
	if(isValid(username)===true && isValid(password)===true) {
	
	}
	return true;
}

function register() {
	return true;
}

function isValid(str) {
	return /^\w+$/.test(str);
}