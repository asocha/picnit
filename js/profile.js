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

	for(x in list) {
		disp.append("<div class='dispalbum'>");
		disp.append("<a href='album.php?id='+list[x]['albumid']>");
		disp.append(list[x]['name']);
		disp.append("</a>");
		disp.append(list[x]['date_created']);
		disp.append("<p><div>");
		disp.append(list[x]['description']);
		disp.append("</div></p>");
		disp.append("</div>");
		//list[x]['description'];
		//list[x]['date_created'];
		//list[x]['owner_id'];
		//list[x]['album_id'];
	}
 }
