/*
	general.js
	Author: PhotoDolo

	Contains functions that are to be used throughout the website
	-Cookies
	-Login check
*/

//Retrieve cookie that's stored
function getCookie(NameOfCookie)
{
	//If we have cookies
	if (document.cookie.length > 0) {
		//Find it
		begin = document.cookie.indexOf(NameOfCookie+"=");

		//If found
		if (begin != -1) {
			//Get the ending
			begin += NameOfCookie.length+1;
			end = document.cookie.indexOf(";", begin);
			if (end == -1)
				end = document.cookie.length;

			//Return the cookie value
			return unescape(document.cookie.substring(begin, end));
		}
	}

	//No cookies or not found
	return null;
}

//Sets a cookies w/ expiration date
function setCookie(NameOfCookie, value, expiredays) {
	//Set the exipiration date
	var ExpireDate = new Date ();
	ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));

	//Add the cookie
	document.cookie = NameOfCookie + "=" + escape(value) + ((expiredays == null) ? "" : "; expires=" + ExpireDate.toGMTString());
}

//Deletes a cookie
function deleteCookie(NameOfCookie) {
	//If the cookie exists
	if (getCookie(NameOfCookie)) {
		//Set the expiration backwards in time, deleted immediately
		document.cookie = NameOfCookie + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

function isLoggedIn() {
	//Check to see if member_id cookie exists
	if(getCookie('member_id') && getCookie('username') && getCookie('is_suspended')) {
		//They are logged in
		return true;
	}

	//No users logged in
	return false;
}

function picnitRequest(url, data) {
	//Create request
	var request = new XMLHttpRequest();
  
        //Get and validate user inputs
        request.open('POST', url, false);

        //Get data string
  	var params = '';
	for(var ind in data)
		params += ind + "=" + data[ind] + "&";
	params = params.substring(0, params.length - 1);

	//Send request
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.send(params);

	//Return request after response
	return request;
}
