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
		line+="<a href='album.php?id="+list[x]['album_id']+"'>";
		line+=list[x]['name'];
		line+="</a>"
		line+=list[x]['date_created'];
		line+="<p><div>";
		line+=list[x]['description'];
		line+="</div></p>";
		line+="</div>";
		//list[x]['description'];
		//list[x]['date_created'];
		//list[x]['owner_id'];
		//list[x]['album_id'];
	}
	disp.html(line);
 }
