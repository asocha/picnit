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

	}

	return null;
}

function getUserTaggedImages(id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getImages';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['tagged_user_id'] = id;

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

	}

	return false;
}

function addTag(image_id, tag, type) {
	//Make sure type is valid
	if(type != 'tag_username' && type != 'category')
		return null;

	//Gather req data
	var params = new Array();
	params['action'] = 'addTag';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = image_id;
	params[type] = tag;

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

function deleteTag(image_id, id, mem_or_cat) {
	if(mem_or_cat !== 'user_id' && mem_or_cat !== 'cat_id')
		return false;

	//Gather post request data
	var params = new Array();
	params['action'] = 'deleteTag';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = image_id;
	params[mem_or_cat] = id;

	//Send request
	request = picnitRequest(tagurl, params);

	//Good data, show image created
	if(request.status === 200) {
		return true;
	}
	//Error
	else {

	}

	return false;
}

function getTagsByImage(image_id) {
	//Gather req data
	var params = new Array();
	params['action'] = 'getTagsByImage';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = image_id;
	
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

function getCategoryTags(prefix) {
	//Gather req data
	var params = new Array();
	params['action'] = 'getCategoryTags';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['prefix'] = prefix;
	
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

function getUserTags(prefix) {
	//Gather req data
	var params = new Array();
	params['action'] = 'getUserTags';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['prefix'] = prefix;
	
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

function getTopCategories(user_id, num) {
	//Gather req data
	var params = new Array();
	params['action'] = 'getTopCategories';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['user_id'] = user_id;
	params['num'] = num;
	
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

function getImageDataByCategory(cat_id, num) {
	//Gather req data
	var params = new Array();
	params['action'] = 'getImages';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['cat_id'] = cat_id;
	params['num'] = num;
	
	//Send request
	request = picnitRequest(imageurl, params);

	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}
	else {
		alert(request.status + "\n" + request.responseText);
	}
	
	return null;
}
