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
		line+="<div class='dispimage'>";
		line+="<img src='data:" + list[x]['imgtype'] + ";base64," + list[x]['image'] + "' alt='" + list[x]['name'] + "'/>";
		line+="</div>";
	}
	disp.html(line);
 }
