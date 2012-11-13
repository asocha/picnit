var loginurl='../api/member.php';

function login() {
	var request = new XMLHttpRequest();
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
	request.open('POST',loginurl,false);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var parms = 'username='+username+'&password='+password;
	request.send(parms);
	alert(request.responseText);
	if(request.status === 200) {
		alert('200');
	}
	else if(request.status === 204) {
		alert('204');
	}
	else {
		alert('other');
	}
		
	}
	return true;
}

function isValid(str) {
	return /^\w+$/.test(str);
}