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
		disp.append(list[x]['name']);
		disp.append("<br/>");
		//list[x]['description'];
		//list[x]['date_created'];
		//list[x]['owner_id'];
		//list[x]['album_id'];
	}
 }
