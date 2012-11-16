/*
	member.js
	Author: PhotoDolo

	Contains functions that communicate with the member API
*/

//URL for member functions
var memberurl='api/member.php';

//Request to be sent to the middleware
var request;

function login() {
	//Get and validate user inputs
	var username = $("input#Username").val();
	var password = $("input#Password").val();
	if(password.length < 5) {
		alert('Password must be at least 6 characters.');
		return false;
	}
	if(username.length < 2 || username.length >15) {
		alert('Username must be between 3 and 15 characters.');
		return false;
	}
	if(isValid(username)===false || isValid(password)===false) {
		alert('Password or Username is Invalid');
		return false;
	}	

	//Getting here means that the inputs validated

	//Put data into associative array, include action
	var params = new Array();
	params['action'] = 'login';
	params['username'] = username;
	params['password'] = password;

	//Send request to the API
	request = picnitRequest(memberurl, params);

	//Good data, proceed to login
	if(request.status === 200) {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		
		//Add cookies to the array
		for(var index in res)
			setCookie(index, res[index], 7);

		//Redirect to profile page
		window.location = "./profile.php?username=" + escape(res['username']);
	}
	//Invalid username/password combo
	else if(request.status === 204) {
		alert('204');
	}
	//Our request messed up
	else if(request.status === 400) {
		alert('400\n' + request.responseText);
	}
	//Something else went wrong
	else {
		alert('Unknown error: ' + request.status);
	}

	//Return false to allow for redirection
	return false;
}

function createUser() {
	//Get user inputs, we know they are validated and that passwords match
	var params = new Array();
	params['username'] = $("input#Newusername").val();
	params['password'] = $("input#Newpassword").val();
	params['email'] = $("input#email").val();
	params['action'] = 'register';
	
	//Send request
	request = picnitRequest(memberurl, params);

	//Process requests
	if(request.status === 200) {
		alert("User created!");
		return true;
	}
	else if(request.status === 204) {
		alert("Username or email already in use!");
	}
	else {
		alert("Unknown error: "+request.status);
	}

	return false;
}

function validatePassword(p1, p2) {
	if(p1.value != p2.value)
		p2.setCustomValidity("Doesn't match");
	else
		p2.setCustomValidity('');
}

function isValid(str) {
	return /^\w+$/.test(str);
}
