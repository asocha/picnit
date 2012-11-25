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
		var line="";
		line+="<div class='dispalbum'>";
		line+="<a href='album.php?id="+list[x]['albumid']+"'>";
		line+=list[x]['name'];
		line+="</a>"
		line+=list[x]['date_created'];
		line+="<p><div>";
		line+=list[x]['description'];
		line+="</div></p>";
		line+="</div>";
		disp.append(line);
		//list[x]['description'];
		//list[x]['date_created'];
		//list[x]['owner_id'];
		//list[x]['album_id'];
	}
 }
