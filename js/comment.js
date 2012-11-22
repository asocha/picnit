/*
	comment .js
	Author: PhotoDolo

	Contains functions that communicate with the comment API
*/

//URL for member functions
var commenturl='api/comment.php';

//Request to be sent to the middleware
var request;

function addComment() {
	//Get user input, should be validated via html5
	var ctext = $("input#ctext").val();
	var image_id = $("input#image_id").val();

	//Gather post request data
	var params = new Array();
	params['action'] = 'addComment';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['comment'] = ctext;
	params['image_id'] = image_id;

	//Send request
	request = picnitRequest(commenturl, params);

	//Debug purposes
	alert(request.status + "\n" + request.responseText);

	//Good data, show comment created
	if(request.status === 200) {
		
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

	return false;
}

function deleteComment() {
	//Get user input, should be sent via GUI/other js call
	var comment_id = $("input#comment_id");
	
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

	//Good data, show album created
	if(request.status === 200) {
		
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
