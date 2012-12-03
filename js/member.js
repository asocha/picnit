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
	//Get user inputs
	var username = $("input#Username").val();
	var password = $("input#Password").val();

	//Put data into associative array, include action
	var params = new Array();
	params['action'] = 'login';
	params['username'] = username;
	params['password'] = password;

	//Send request to the API
	request = picnitRequest(memberurl, params);

	//Parse the JSON result
	var res = $.parseJSON(request.responseText);

	//Good data, proceed to login
	if(request.status === 200) {
		//Check for suspension
		if(res["is_suspended"] == "1") {
			logout(false);
			window.location = "/picnit/suspended.php";
			return false;
		}

		//Add cookies to the array
		for(var index in res)
			setCookie(index, res[index], 7);

		//Redirect to profile page
		window.location = "/picnit/profile/" + escape(res['username']);
	}
	//Error
	else {
		alert(request.status + "\n" + res["msg"]);
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
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}


	return false;
}

function validatePassword(p1, p2) {
	if(p1.value != p2.value)
		p2.setCustomValidity("Doesn't match");
	else
		p2.setCustomValidity('');
}

function logout(redirect) {
	//To logout, we delete all cookies
	deleteCookie('username');
	deleteCookie('member_id');
	deleteCookie('is_suspended');
	deleteCookie('is_admin');
	deleteCookie('key');

	//Go back to the index
	if(redirect)
		window.location = "/picnit/";
}

function getFollowers(user_id) {
	//Gather data
	var params = new Array();
	params['action'] = 'getFollowers';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = user_id;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return null;
}

function getFollowees(user_id) {
	//Gather data
	var params = new Array();
	params['action'] = 'getFollowees';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = user_id; 

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return null;
}

function getFollowRequests(user_id) {
	//Gather data
	var params = new Array();
	params['action'] = 'getFollowRequests';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = user_id;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return null;
}

function follow(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'follow';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function unfollow(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'unfollow';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function requestFollow(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'requestFollow';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function removeFollower(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'removeFollower';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function refuseFollow(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'refuseFollow';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function suspendUser(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'suspendUser';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function unsuspendUser(uid) {
	//Gather data
	var params = new Array();
	params['action'] = 'unsuspendUser';
	params['key'] = getCookie('key');
	params['username'] = getCookie('username');
	params['user_id'] = uid;

	//Send request
	request = picnitRequest(memberurl, params);

	//Success
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}
