/*
 *	profile.js
 *	Author: PhotoDolo
 *
 *	Creates the dynamic content
 */

/***** album.php *****/

function createAlbumImagesElements(album_id, is_owner, logged_in) {
	//Get the photos
	var list = getImages(album_id);

	//Get display area
	var disp = $('#image-holder');

	//Clear current content
	disp.empty();

	//Insert code
	var line="";
	for(x in list) {
		//Get tag info
		var tags = getTagsByImage(list[x]['image_id']);

		line+="<div class='imagesection' id='imagesection"+list[x]['image_id']+"'>";
		line+="<table class='imagetable'>";
		line+="<tr>";
		line+="<td class='tableleft'>";
		line+="<div class='dispimage' id='dispimage"+list[x]['image_id']+"'>";
		line+="<div class='imgname'>"+list[x]['name']+"</div>";
		line+="<div class='imgdate'>"+list[x]['date_added']+"</div>";
		line+="<div class='imgdesc'>"+list[x]['description']+"</div>";
		line+="<img class='albumimage' src='data:" + list[x]['image_type'] + ";base64," + list[x]['image'] + "' alt='" + list[x]['name'] + "'/>";
		line+="</div>";
		line+="</td>";
		line+="<td class='tableright'>";
		line+="<div class='disppanel'>";
		line+="<div id='tagarea'>";
		line+="<div id='memtagarea'>";
		line+="<div class='tagtitle'>member tags</div>";
		if(tags && tags['member_tags'])
			for(n in tags['member_tags'])
				line+="<div class='membertag'>"+tags['member_tags'][n]['username']+'</div>';
		else
			line+="<div class='nomemtags'>no member tags :(</div>";
		line+="</div><div class='cattagarea'>";
		line+="<div class='tagtitle'>category tags</div>";
		if(tags && tags['cat_tags'])
			for(n in tags['cat_tags'])
				line+="<div class='categorytag'>"+tags['cat_tags'][n]['category']+'</div>';
		else
			line+="<div class='nocattags'>no category tags :(</div>";
		line+="</div></div>";
		line+="<div class='imgbuts'>";
		if (is_owner) line+="<input type='button' id='pictagbut"+list[x]['image_id']+"' class='buttons pictagbut' value='tag'/>";
		if (logged_in) line+="<input type='button' id='picfavbut"+list[x]['image_id']+"' class='buttons picfavbut' value='"+((list[x]['favorited'])? "unfavorite" : "favorite")+"'/>";
		if (is_owner) line+="<input type='button' id='picdelbut"+list[x]['image_id']+"' class='buttons picdelbut' value='delete'/>";
		line+="</div>";
		line+="</div>";
		line+="</td>";
		line+="</tr>";
		line+="</table>";
		line+="</div>";
	}
	disp.html(line);

	//Add event handlers
	//Buttons
	$(".picdelbut").click(function() {
		var id = $(this).attr('id').substring(9);
		if(deleteImage(id)) {
			$('#imagesection'+id).remove();
		}
	});
	$(".picfavbut").click(function() {
		var id = $(this).attr('id').substring(9);

		if($(this).val() == 'favorite') {
			if(addFavorite(id))
				$(this).val('unfavorite');
		}
		else {
			if(deleteFavorite(id))
				$(this).val('favorite');
		}
	});
	$(".pictagbut").click(function() {
		var id = $(this).attr('id').substring(9);

		showTag(id, 'category');
	});
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

/***** profile.php *****/

function changePanel(elementFunction) {
	$('#thumbnail-display').transition({ opacity: 0 }, 'fast', elementFunction);
}

function createAlbumElements(uid) {
	//Get display area
	var disp = $("#thumbnail-display");
	
	//Get the album information of this user
	var list = getAlbums(uid);

	//Build new html in meantime
	var line="<h2>albums</h2>";
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
	
	//Clear the elements, should be invisible
	disp.empty();

	//Write new html
	disp.html(line);
	
	//Make visible
	disp.transition({opacity: 1}, 'fast');
}

function createFavoritesElements(fuser_id, member_id) {
	//Get display area
	var disp = $("#thumbnail-display");
	
	//Get the album information of this user
	var list = getFavorites(fuser_id);

	//Generate html
	var line="<h2>favorites</h2>";
	for(x in list) {
		line+="<div class='dispimage' id='dispimage"+list[x]['image_id']+"'>";
		line+="<div class='imgdate'>"+list[x]['date_added']+"</div>";
		line+="<div class='imgname'>"+list[x]['name']+"</div>";
		line+="<div class='imgdesc'>"+list[x]['description']+"</div>";
		line+="<img src='data:" + list[x]['image_type'] + ";base64," + list[x]['image'] + "' alt='" + list[x]['name'] + "'/>";
		if (member_id == fuser_id){
			line+="<input type='button' id='profavbut"+list[x]['image_id']+"' class='buttons profavbut' value='unfavorite'/>";
		}
		line+="</div>";
	}
	//Clear the current contents
	disp.empty();

	//Write new html
	disp.html(line);

	//Re display
	disp.transition({opacity: 1}, 'fast');

	//Add button events
	$('.profavbut').click(function() {
		var id = $(this).attr('id').substring(9);
		if(deleteFavorite(id))
			$(this).parent().remove();
	});
}

function createTaggedElements(user_id) {
	//Get the tagged elements
	var list = getUserTaggedImages(user_id);

	//Get display area
	var disp = $("#thumbnail-display");

	var line="<h2>pictures of you</h2>";
	for(x in list) {
		line+="<div class='dispimage' id='dispimage"+list[x]['image_id']+"'>";
		line+="<div class='imgdate'>"+list[x]['date_added']+"</div>";
		line+="<div class='imgname'>"+list[x]['name']+"</div>";
		line+="<div class='imgdesc'>"+list[x]['description']+"</div>";
		line+="<img src='data:" + list[x]['image_type'] + ";base64," + list[x]['image'] + "' alt='" + list[x]['name'] + "'/>";
		line+="<input type='button' id='protagbut"+list[x]['image_id']+"' class='buttons protagbut' value='untag me'/>";
		line+="</div>";
	}
	//Clear the current contents
	disp.empty();

	//Write new html
	disp.html(line);

	//Re display
	disp.transition({opacity: 1}, 'fast');

	//Add button events
	$('.protagbut').click(function() {
		var id = $(this).attr('id').substring(9);
		if(deleteTag(id, user_id, 'user_id'))
			$(this).parent().remove();
	});
}

function createFollowersElements() {
	//Get the tagged elements
	var list = getFollowers();

	//Get display area
	var disp = $("#thumbnail-display");

	var line="";
	line+="<h2>followers</h2>";
	for(x in list) {
		line += "<div class='dispfollowers'>";
		line += "<a href='/picnit/profile/"+list[x]['username']+"'>";
		line += "<span class='follower'>"+list[x]['username']+"</span>";
		line += "</a>";
		line+="<input type='button' id='folrembut"+list[x]['user_id']+"' class='buttons folrembut' value='remove'/>";
		line += "</div>";
	}
	//Clear the current contents
	disp.empty();

	//Write new html
	disp.html(line);

	//Re display
	disp.transition({opacity: 1}, 'fast');

	//Set clicks
	$('.folrembut').click(function() {
		var id = $(this).attr('id').substring(9);
		if(removeFollower(id))
			$(this).parent().remove();
	});
}

function createFolloweesElements() {
	//Get the tagged elements
	var list = getFollowees();

	//Get display area
	var disp = $("#thumbnail-display");

	var line="";
	line+="<h2>following</h2>";
	for(x in list) {
		line += "<div class='dispfollowees'>";
		line += "<a href='/picnit/profile/"+list[x]['username']+"'>";
		line += "<span class='follower'>"+list[x]['username']+"</span>";
		line += "</a>";
		line+="<input type='button' id='unfollbut"+list[x]['user_id']+"' class='buttons unfollbut' value='unfollow'/>";
		line += "</div>";
	}
	//Clear the current contents
	disp.empty();

	//Write new html
	disp.html(line);

	//Re display
	disp.transition({opacity: 1}, 'fast');

	//Set clicks
	$('.unfollbut').click(function() {
		var id = $(this).attr('id').substring(9);
		if(unfollow(id))
			$(this).parent().remove();
	});
}

function createFollowReqElements() {
	//Get the tagged elements
	var list = getFollowRequests();

	//Get display area
	var disp = $("#thumbnail-display");

	var line="<h2>follow requests</h2>";
	for(x in list) {
		line += "<div class='dispfollowreq'>";
		line += "<a href='/picnit/profile/"+list[x]['username']+"'>";
		line += "<span class='follower'>"+list[x]['username']+"</span>";
		line += "</a>";
		line += "<input type='button' id='accfolreq"+list[x]['user_id']+"' class='buttons accfolreq' value='accept'/>";
		line += "<input type='button' id='decfolreq"+list[x]['user_id']+"' class='buttons decfolreq' value='decline'/>";
		line += "</div>";
	}
	//Clear the current contents
	disp.empty();

	//Write new html
	disp.html(line);

	//Re display
	disp.transition({opacity: 1}, 'fast');

	//Set clicks
	$('.accfolreq').click(function() {
		var id = $(this).attr('id').substring(9);
		if(follow(id))
			$(this).parent().remove();
	});
	$('.decfolreq').click(function() {
		var id = $(this).attr('id').substring(9);
		if(refuseFollow(id))
			$(this).parent().remove();
	});
}
