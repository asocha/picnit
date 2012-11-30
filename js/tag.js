/*
	tag.js
	Author: PhotoDolo

	Contains functions that communicate with the tag API
*/

//URL for member functions
var tagurl='/picnit/api/tag.php';
var imageurl='/picnit/api/image.php';

//Request to be sent to the middleware
var request;

function getFavorites(fuser_id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getImages';
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
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"];
	}

	return null;
}

function getUserTaggedImages(id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getImages';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['user_id'] = id;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"];
	}

	return null;
}

function addFavorite(id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'addFavorite';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = id;

	//Send request
	request = picnitRequest(tagurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"];
	}

	return false;
}

function deleteFavorite(id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'deleteFavorite';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = id;

	//Send request
	request = picnitRequest(tagurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return true;
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"];
	}

	return false;
}

function addTag(image_id, id, type) {
	//Make sure type is valid
	if(type !== 'user_id' && type !== 'cat_id')
		return null;

	//Gather req data
	var params = new Array();
	params['action'] = 'addTag';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = image_id;
	params[type] = id;

	//Send request
	request = picnitRequest(tagurl, params);

	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	else {
		alert(request.status + "\n" + request.responseText);
	}

	return null;
}

function getTagsByImage() {
	//Gather req data
	var params = new Array();
	params['action'] = 'getTagsByImage';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	
	//Send request
	request = picnitRequest(tagurl, params);

	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	else {
		alert(request.status + "\n" + request.responseText);
	}
	
	return null;
}
