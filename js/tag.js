/*
	tag.js
	Author: PhotoDolo

	Contains functions that communicate with the tag API
*/

//URL for member functions
var tagurl='/picnit/api/tag.php';

//Request to be sent to the middleware
var request;

function getFavorites() {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getFavorites';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');

	//Send request
	request = picnitRequest(tagurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
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

function getUserTagImages(id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getCategoryTagImages';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['user_id'] = id;

	//Send request
	request = picnitRequest(tagurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
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

