/*
	comment.js
	Author: PhotoDolo

	Contains functions that communicate with the comment API
*/

//URL for member functions
var commenturl='/picnit/api/comment.php';

//Request to be sent to the middleware
var request;

function addComment(image_id, ctext) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'addComment';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['comment'] = ctext;
	params['image_id'] = image_id;

	//Send request
	request = picnitRequest(commenturl, params);

	//Good data, show comment created
	if(request.status === 200) {
		return true;
		//return $.parseJSON(request.responseText);
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return null;
}

function deleteComment(comment_id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'deleteComment';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['comment_id'] = comment_id;

	//Send request
	request = picnitRequest(commenturl, params);

	//Debug purposes
	alert(request.status + "\n" + request.responseText);

	//Good data, comment deleted
	if(request.status === 200) {

	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function getComments(image_id) {
	//Gather post request data
	var params = new Array();
	params['action'] = 'getComments';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['image_id'] = image_id;

	//Send request
	request = picnitRequest(commenturl, params);

	//Debug purposes
	alert(request.status + "\n" + request.responseText);

	//Good data, request contains data
	if(request.status === 200) {
		return $parseJSON(request.responseText);
	}
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return null;
}
