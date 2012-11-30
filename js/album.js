/*
	album.js
	Author: PhotoDolo

	Contains functions that communicate with the album API
*/

//URL for member functions
var albumurl='/picnit/api/album.php';
var imageurl='/picnit/api/image.php';

//Request to be sent to the middleware
var request;

function createAlbum() {
	//Get user input, should be validated via html5
	var albumname = $("input#albumname").val();
	var albumdesc = $("#albumdesc").val();

	//Gather post request data
	var params = new Array();
	params['action'] = 'createAlbum';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['name'] = albumname;
	params['description'] = albumdesc;

	//Send request
	request = picnitRequest(albumurl, params);

	//Good data, show album created
	if(request.status === 200) {
		window.location = '/picnit/profile/' + getCookie('username');
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"];
	}

	return false;
}

function deleteAlbum(aid) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'deleteAlbum';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['album_id'] = aid;

	//Send request
	request = picnitRequest(albumurl, params);

	//Good data, show album created
	if(request.status === 200) {
		alert("Album Deleted");
		return true;
	}
	//Error
	else {
	}

	return false;
}

function getAlbums(user_id) {
	var params = new Array();
	params['action'] = 'getAlbums';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['user_id'] = user_id;

	//Send request
	request = picnitRequest(albumurl, params);

	//Parse the JSON result
        var res = $.parseJSON(request.responseText);

	//Good data, list of albums
	if(request.status === 200) {
		//Return the list
		return res['list'];
	}
	//No albums
	else if(request.status === 204) {
		return null;
	}
	//Error
	else {
		alert(request.status + "\n" + res["msg"];
	}

	return null;
}

function getImages(album_id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getImages';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['album_id'] = album_id;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, request contains data
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
