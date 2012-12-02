/*
	tag.js
	Author: PhotoDolo

	Contains functions that communicate with the tag API
*/

//URL for member functions
var searchurl='/picnit/api/search.php';
var imageurl='/picnit/api/image.php';

//Request to be sent to the middleware
var request;

function filterMembers(input) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'filterMembers';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['fuser_id'] = fuser_id;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	//Error
	else {

	}

	return null;
}

