/*
	image .js
	Author: PhotoDolo

	Contains functions that communicate with the image API
*/

//URL for member functions
var imageurl='/picnit/api/image.php';

//Request to be sent to the middleware
var request;

function getImage() {
	//Get user input, should be validated via html5
	var imagename = $("input#imagename").val();
	
	//Gather post request data
	var params = new Array();
	params['action'] = 'createAlbum';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['name'] = imagename;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, show image created
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

function saveImage() {
	//Get photo data
	var imgobj = $("input#inpimage")[0].files[0];
	var phototype = imgobj.type;

	//Make sure it's an image, save extension
	if(phototype.indexOf("image") !== 0) {
		alert("Not an image");
		return;
	}
	//Get the extension
	phototype = phototype.substring(phototype.indexOf("/")+1);
	if(phototype == "jpeg")
		phototype = "jpg";

	//Parse it into base64, must be asycn
	var reader = new FileReader();
	reader.onloadend = function(evt) {
		if (evt.target.readyState == FileReader.DONE) {
			var photo = evt.target.result;
			photo = photo.substring(photo.indexOf("base64,")+7);
			sendImage(encodeURIComponent(photo), phototype);
		}
	}
	reader.readAsDataURL(imgobj);

	//Never redirect
	return false;
}

//Finish sending the image to the database to be saved
function sendImage(photo, phototype) {
	//Get user input, should be validated via html5
	var imagename = $("input#imagename").val();
	var albumid = $("input#albumid").val();
	var publicness = $("select#publicness").val();
	var desc = $("input#imagedesc").val();

	//Gather post request data
	var params = new Array();
	params['action'] = 'saveImage';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['name'] = imagename;
	params['description'] = desc;
	params['photo'] = photo;
	params['phototype'] = phototype;
	params['album_id'] = albumid;
	params['publicness'] = publicness;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, show image created
	if(request.status === 200) {
		window.location = "/picnit/album/"+albumid;
	}
	//Unauthorized
	else if(request.status === 401) {
		
	}
	//Missing data
	else if(request.status === 400) {
		
	}
	//No album exists error
	else if(request.status === 404) {
		
	}
	//Unknown
	else {

	}

	return false;
}

function getLastImages(num,user_id) {
	//Gather params
	var params = new Array();
	params['action'] = "getLastImages";
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['num'] = num;

	//If user_id was passed, send with request
	if(typeof user_id == "number")
		params['user_id'] = user_id;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, give back JSON
	if(request.status === 200) {
		var resp = $.parseJSON(request.responseText);
		resp = resp['list'];

		return resp;
	}

	return null;
}
