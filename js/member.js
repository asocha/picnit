/*
	member.js
	Author: PhotoDolo

	Contains functions that communicate with the member API
*/

//URL for member functions
var memberurl='/picnit/api/member.php';

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

	//Request to be sent to the middleware
	request = new XMLHttpRequest();

	//Create function that handles response
	request.onreadystatechange = function () {
		//Make sure request is ready, we only care about when done
		if(request.readyState === 4) {
			//Good data, proceed to login
			if(request.status === 200) {
				alert('200');
			}

			//Invalid username/password combo
			else if(request.status === 204) {
				alert('204');
			}

			//Our request messed up
			else if(request.status === 400) {
				alert('400');
			}

			//Something else went wrong
			else {
				alert('Unknown error: ' + request.status);
			}
		}
	};

	//Get and validate user inputs
	request.open('POST', memberurl, true);
		
	//Send request
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var parms = 'username='+username+'&password='+password;
	request.send(parms);
	
	//Return true, meaning the request was sent, NOT that the login was successful
	return false;
}

function register() {
	return true;
}

function isValid(str) {
	return /^\w+$/.test(str);
}
