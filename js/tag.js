/*
	tag.js
	Author: PhotoDolo

	Contains functions that communicate with the tag API
*/

//URL for member functions
var imageurl='/picnit/api/tag.php';

//Request to be sent to the middleware
var request;

function getFavorites(uid) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getFavorites';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return request.responseText;
	}
	//Unauthorized
	else if(request.status === 401) {
		
	}
	//Missing data
	else if(request.status === 400) {
		
	}
	//Unknown error
	else {
		
	}

	return null;
}

