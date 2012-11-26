/*
 *	profile.js
 *	Author: PhotoDolo
 *
 *	Creates the dynamic content of profile.php page
 */

 function createAlbumElements(uid) {
	//Get the album information of this user
	var list = getAlbums(uid);

	//Get display area
	var disp = $("#thumbnail-display");

	//Clear the current contents
	disp.empty();
	var line="";
	for(x in list) {
		line+="<div class='dispalbum'>";
		line+="<a href='/picnit/album/"+list[x]['album_id']+"'>";
		line+=list[x]['name'];
		line+="</a>"
		line+="<div class='albumdate'>"+list[x]['date_created']+"</div>";
		line+="<p><div>";
		line+=list[x]['description'];
		line+="</div></p>";
		line+="</div>";
	}
	disp.html(line);
 }

 function createAlbumImagesElements(album_id) {
	//Get the photos
	var list = getImages(album_id);

	//Get display area
	var disp = $('#image-holder');

	//Clear current content
	disp.empty();

	//Insert code
	var line="";
	for(x in list) {
		line+="<div class='dispimage' id='dispimage"+list[x]['image_id']+"'>";
		line+="<div class='imgdate'>"+list[x]['date_added']+"</div>";
		line+="<div class='imgname'>"+list[x]['name']+"</div>";
		line+="<div class='imgdesc'>"+list[x]['description']+"</div>";
		line+="<img src='data:" + list[x]['image_type'] + ";base64," + list[x]['image'] + "' alt='" + list[x]['name'] + "'/>";
		line+="<div class='imgbuts'>";
		line+="<input type='button' id='picdelbut"+list[x]['image_id']+"' class='buttons picdelbut' value='delete'/>";
		line+="<input type='button' id='picfavbut"+list[x]['image_id']+"' class='buttons picfavbut' value='favorite'/>";
		line+="<input type='button' id='pictagbut"+list[x]['image_id']+"' class='buttons pictagbut' value='tag'/>";
		line+="</div>"
		line+="</div>";
	}
	disp.html(line);

	//Add event handlers
	//This loop may be unnecessary... pending further testing
	for(x in list) {
		$("#picdelbut"+list[x]['image_id']).click(function() {
			var id = $(this).attr('id').substring(9);
			if(deleteImage(id)) {
				$('#dispimage'+id).remove();
			}
		});
		$("#picfavbut"+list[x]['image_id']).click(function() {
			var id = $(this).attr('id').substring(9);
		});
		$("#pictagbut"+list[x]['image_id']).click(function() {
			var id = $(this).attr('id').substring(9);
		});
	}
 }

 function createFlexsliderElements(num,user_id) {
	//Get the photos
	var list = getLastImages(num,user_id);

	//Get display area
	var disp = $('.slides');
	
	var line="";
	for(x in list) {
		line+="<li>";
		line+="<img src='data:" + list[x]['image_type'] + ";base64," + list[x]['image'] + "' alt='" + list[x]['name'] + "'/>";
		line+="</li>";
	}
	disp.html(line);
 }

 function createFavoritesElements() {
	//Get the album information of this user
	var list = getFavorites();

	//Get display area
	var disp = $("#thumbnail-display");

	//Clear the current contents
	disp.empty();
	var line="";
	for(x in list) {
		line += "<div class='dispfav'>";
		line += list[x].toString();
		line += "</div>";
	}
	disp.html(line);
 }

function createTaggedElements() {
	//Get the tagged elements
	var list = null;

	//Get display area
	var disp = $("#thumbnail-display");

	//Clear the current contents
	disp.empty();
	var line="";
	for(x in list) {
		line += "<div class='disptagged'>";
		line += list[x].toString();
		line += "</div>";
	}
	disp.html(line);
}

function createFollowersElements() {
	//Get the tagged elements
	var list = getFollowers();

	//Get display area
	var disp = $("#thumbnail-display");

	//Clear the current contents
	disp.empty();
	var line="";
	for(x in list) {
		line += "<div class='dispfollowers'>";
		line += "<a href='/picnit/profile/"+list[x]['username']+"'>";
		line += "<span class='follower'>"+list[x]['username']+"</span>";
		line += "</a>";
		line += "</div>";
	}
	disp.html(line);
}

function createFolloweesElements() {
	//Get the tagged elements
	var list = getFollowees();

	//Get display area
	var disp = $("#thumbnail-display");

	//Clear the current contents
	disp.empty();
	var line="";
	for(x in list) {
		line += "<div class='dispfollowees'>";
		line += "<a href='/picnit/profile/"+list[x]['username']+"'>";
		line += "<span class='follower'>"+list[x]['username']+"</span>";
		line += "</a>";
		line += "</div>";
	}
	disp.html(line);
}

function createFollowReqElements() {
	//Get the tagged elements
	var list = getFollowRequests();

	//Get display area
	var disp = $("#thumbnail-display");

	//Clear the current contents
	disp.empty();
	var line="";
	for(x in list) {
		line += "<div class='dispfollowreq'>";
		line += "<a href='/picnit/profile/"+list[x]['username']+"'>";
		line += "<span class='follower'>"+list[x]['username']+"</span>";
		line += "</a>";
		line += "</div>";
	}
	disp.html(line);
}
