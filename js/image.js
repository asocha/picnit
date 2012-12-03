/*
	image.js
	Author: PhotoDolo

	Contains functions that communicate with the image API
*/

//URL for member functions
var imageurl='/picnit/api/image.php';

//Request to be sent to the middleware
var request;

function saveImage() {
	//Get photo data
	var imgobj = $("input#inpimage")[0].files[0];
	var phototype = imgobj.type;

	//Make sure size is less thab 1MB
	if(imgobj.size > 1048576) {
		alert("Image must be less than 1MB");
		return false;
	}

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
	var desc = $("#imagedesc").val();

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
	//Error
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}

	return false;
}

function deleteImage(imgid) {
	var params = new Array();
	params['action'] = 'deleteImage';
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['is_admin'] = getCookie('is_admin');
	params['image_id'] = imgid;

	request = picnitRequest(imageurl, params);

	if(request.status === 200) {
		return true;
	}
	else {
		//Parse the JSON result
		var res = $.parseJSON(request.responseText);
		alert(request.status + "\n" + res["msg"]);
	}
	return false;
}

function getLastImages(num,user_id) {
	//Gather params
	var params = new Array();
	params['action'] = "getImages";
	params['username'] = getCookie('username');
	params['key'] = getCookie('key');
	params['num'] = num;
	params['user_id'] = user_id;

	//If user_id was passed, send with request
	if(typeof user_id == "number")
		params['user_id'] = user_id;

	//Send request
	request = picnitRequest(imageurl, params);

	//Good data, give back JSON
	if(request.status === 200) {
		return $.parseJSON(request.responseText);
	}

	return null;
}
