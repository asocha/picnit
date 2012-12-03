<?php
//Function that returns the top bar
function menubar() {
?>
<div id="menubar" class="panels">
	<script type="text/javascript" charset="utf-8">
	  $(window).load(function() {
		//Set sign-out button
		$('#signoutbut').click(function() { logout(true); });

		//Load flexslider
		if($('.flexslider').length > 0)
			$('.flexslider').flexslider({
				animation: "slide",
				minItems: 1
			});
	  });
	function showsignup() {
		document.getElementById('overlay').style.visibility="visible";
		document.getElementById('signupbar').style.visibility="visible";
	}
	</script>
	<?php
		//Require general if not already
		//require_once('/picnit/php/general.php');

		//Only show this section if user isn't logged in
		if(!isLoggedIn()) {
	?>
	<form id="signupbut">
		<span><input type="button" id="sign" class="buttons" value="Sign Up"/></span>
	</form>

	<form id="signinform" action="/picnit/index.php" onsubmit="return login();">
		<span><label for="Username">username: </label><input type="text" id="Username" class="inputs" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/></span>
		<span><label for="Password">password: </label><input type="password" id="Password" class="inputs" pattern=".{5,}" title="Must be at least 5 characters" required="required"/></span>
		<span><input type="submit" id="signin" class="buttons" value="Sign In"/></span>
	</form>
	<form id="homebut" action="/picnit/index.php">
		<span><input type="submit" id="home" class="buttons" value="home"/></span>
	</form>
	<?php
		} //End if
		else {
	?>
	<form id="homebut" action="/picnit/index.php">
		<span><input type="submit" id="home" class="buttons" value="home"/></span>
	</form>
	
	<form id="signoutbut">
			<span><input type="button" id="sign" class="buttons" value="sign out"/></span>
		</form>
	<div id="userinfo">
		<a href="/picnit/profile/<?php echo $_COOKIE['username']; ?>"><span id="dispname"><?php echo $_COOKIE['username']; ?></a>
	</div>
		<?php
			}
		?>
	</div>
	<script type="text/javascript">
	$('#signupbut').click(showsignup);
	</script>
<?php
}

//Function that returns the signup section
function signup() {
?>
	<script>

	function hidesignup() {
		document.getElementById('overlay').style.visibility="hidden";
		document.getElementById('signupbar').style.visibility="hidden";
	}
	</script>
<?php
	if(!isLoggedIn()) {
?>
	<div id="overlay" class="overlays">
	</div>
	<div id="signupbar" class="panels">
		<form id="signupform" onsubmit="return createUser();">
		<p><div>
			<label for="Newusername">username: </label>
			<input type="text" id="Newusername" class="inputs" pattern="[\w]{3,15}" title="Must be between 3 and 15 letters, numbers, or underscores" required="required"/>
		</div></p>
		<p><div>
			<label for="Newpassword">password: </label>
			<input type="password" id="Newpassword" class="inputs" pattern=".{5,}" title="Must be at least 5 characters" required="required"/>
		</div>
		<div>
			<label for="Confirmpassword">confirm password: </label>
			<input type="password" id="Confirmpassword" class="inputs" required="required" oninput="validatePassword(document.getElementById('Newpassword'), this);"/>
		</div></p>
		<p><div>
			<label for="name">email: </label>
			<input type="email" id="email" class="inputs" required="required"/>
		</div></p>
			<p><div><input type="submit" id="signup" class="buttons" value="sign up"/></div></p>
			<p><div><input type="button" id="cancel" class="buttons" value="cancel"/></div><p>
		</form>
	</div>
</div>
	<script type="text/javascript">
	$('#cancel').click(hidesignup);
	$('#overlay').click(hidesignup);
	</script>
	<?php
		}
	}
	?>
<?php 
//Function that returns the info bar
function info() {
?>
<div id="info" name="info" class="panels">
	<div id="infotext">
		<div>picnit.net</div>
		<div>A PhotoDolo Project</div>
	</div>
</div>
	<?php
	}

//Function that returns the image viewer
function imageview() {
?>
<script>
	function hideViewer() {
		document.getElementById('imgoverlay').style.visibility="hidden";
		document.getElementById('imgviewer').style.visibility="hidden";

		$("body").css("overflow","auto");

		$("#comments").empty();
	}

	function showViewer(src, image_id) {
		$('#theimage').attr('alt', image_id);
		$('#theimage').attr('src', src);

		document.getElementById('imgoverlay').style.visibility="visible";
		document.getElementById('imgviewer').style.visibility="visible";

		//Load comments
		var comments = getComments(image_id);

		var disp = $('#comments');
		for(x in comments) {
			var line = "<div class='comment' id='comment"+comments[x]['comment_id']+"'>";
			line += "<div class='commentown'>"+comments[x]['commenter']+"</div>";
			line += "<div class='commenttext'>"+comments[x]['comment_text']+"</div>";
			line += "<div class='commentdel'>";
			line += "<input type='button' class='buttons commentdelbut' id='commentdelbut"+comments[x]['comment_id']+"' value='delete'/>";
			line += "</div>";
			line += "</div>";

			disp.append(line);
		}

		$("body").css("overflow","hidden");

		$("#viewercontainer").scroll(function() {
			document.getElementById("viewercontainer").style.display='none';
			document.getElementById("viewercontainer").offsetHeight; 
			document.getElementById("viewercontainer").style.display='block';
		});
	}

	function imageViewAddComment() {
		var image_id = $('#theimage').attr('alt');
		var text = $('#commenttext').val();

		if(text.length === 0)
			return false;

		var data = addComment(image_id, text);
		if(data) {
			//Clear text box
			$('#commenttext').val('');
			
			//Add new comment
			var line = "<div class='comment' id='comment"+data['comment_id']+"'>";
			line += "<div class='commentown'>"+data['commenter']+"</div>";
			line += "<div class='commenttext'>"+data['comment_text']+"</div>";
			line += "<div class='commentdel'>";
			line += "<input type='button' class='buttons commentdelbut' id='commentdelbut"+data['comment_id']+"' value='delete'/>";
			line += "</div>";
			line += "</div>";
			
			$('#comments').prepend(line);

			return false;
		}

		return false;
	}
</script>
<div id="imgoverlay" class="overlays">
</div>
<div id="viewercontainer">
<div id="imgviewer">
	<img id="theimage"/>
	<div id="commentarea" class="panels">
		<h2>comments</h2>
		<div id="createcommentarea">
			<form id='createcomment' onsubmit='return imageViewAddComment();'>
				<div>
					<label for="commenttext">add comment:</label>
					<textarea id="commenttext"></textarea>
				</div>
				<input type="submit" class="buttons" value='add comment'/>
			</form>
		</div>
		<div id='comments'>
		</div>
	</div>
</div>
</div>
	<script type="text/javascript">
	//Hide on click out
	$('#imgoverlay').click(hideViewer);
	</script>
	<?php
	}

//Function that returns the search bar
function searchbar() {

?>
<div id="searchbar" class="panels">
	<form id="search" action="/picnit/search.php" method="get">
		<span id="searchlabel"><label for="q">search:</label></span>
		<span id="searchspan"><input type="text" name="q" id="Searchterm" class="inputs"/></span>
		<span id="searchdroplabel"><label for="what">type:</label>
		<span>
			<select name="what" id="searchtype" class="dropdown">
				<option>member</option>
				<option>photo</option>
				<option>category</option>
				<option>album</option>
			</select>
		</span>
		<span><input type="submit" id="search" class="buttons" value="submit"/></span>
	</form>
</div>
	<?php
	}

//Function that returns the image uploader
function uploader($album_id) {

?>
	<script>
	function hideUploader() {
		document.getElementById('uploadoverlay').style.visibility="hidden";
		document.getElementById('uploadbar').style.visibility="hidden";
	}

	function showUploader() {
		document.getElementById('uploadoverlay').style.visibility="visible";
		document.getElementById('uploadbar').style.visibility="visible";
	}
</script>
	<div id="uploadoverlay" class="overlays">
	</div>
	<div id="uploadbar" class="panels">
		<form id="uploadform" onsubmit="return saveImage();">
		<p><div><label for="imagename">image name: </label><input type="text" id="imagename" class="inputs" pattern=".{3,63}" title="Image Name must contain between 3 and 63 characters." required="required"/></div>
		<div><label for="imagedesc">description: </label><textarea id="imagedesc"></textarea></div></p>
		<p><div>
			<select id="publicness" class="inputs">
				<option selected value="0">Public</option>
				<option value="1">Followers</option>
				<option value="2">Private</option>
			</select>
		</div></p>
		<p><div id="filesel"><input type="file" id="inpimage" class="buttons" required="required" accept="image/*" value="browse"/></div></p>
		<p><div><input type="submit" id="imgsubmit" class="buttons" value="submit"/></div></p>
		<p><div><input type="button" id="imgcancel" class="buttons" value="cancel"/></div></p>
		<input type="hidden" id="albumid" value="<?php echo $album_id; ?>"/>
		</form>
	</div>
	<script type="text/javascript">
	$('#imgcancel').click(hideUploader);
	$('#uploadoverlay').click(hideUploader);
	</script>
	<?php
	}

//Function that returns the album creator
function albumcreator() {
	?>
	<script>
	function hideAlbumCreator() {
		document.getElementById('albumoverlay').style.visibility="hidden";
		document.getElementById('albumbar').style.visibility="hidden";
	}

	function showAlbumCreator() {
		document.getElementById('albumoverlay').style.visibility="visible";
		document.getElementById('albumbar').style.visibility="visible";
	}
	</script>
	<div id="albumoverlay" class="overlays">
	</div>
	<div id="albumbar" class="panels">
		<form id="albumform" onsubmit="return createAlbum();">
		<p><div><label for="albumname">album name: </label><input type="text" id="albumname" class="inputs" pattern=".{3,63}" title="Album Name must contain between 3 and 63 characters." required="required"/></div>
		<div><label for="albumdesc">description: </label><textarea id="albumdesc"></textarea></div></p>
		<p><div><input type="submit" id="albsubmit" class="buttons" value="submit"/></div></p>
		<p><div><input type="button" id="albcancel" class="buttons" value="cancel"/></div></p>
		</form>
	</div>
	<script type="text/javascript">
	$('#albcancel').click(hideAlbumCreator);
	$('#albumoverlay').click(hideAlbumCreator);
	</script>

	<?php
	}
	
	//Function that returns the tag bar
function tagbar() {
	?>
	<script type="text/javascript">
	function hideTag() {
		document.getElementById('tagoverlay').style.visibility="hidden";
		document.getElementById('tagbar').style.visibility="hidden";
		if(document.getElementById('ui-autocomplete'))
			document.getElementById('ui-autocomplete').style.visibility="hidden";

		$('#tagname').val();
		$('#tagname').blur();
	}

	function showTag(image_id, mem_or_cat) {
		document.getElementById('tagoverlay').style.visibility="visible";
		document.getElementById('tagbar').style.visibility="visible";
		if(document.getElementById('ui-autocomplete'))
			document.getElementById('ui-autocomplete').style.visibility="visible";

		$('#tagname').focus();

		$('label[for="tagname"]').text(mem_or_cat+" tag:");
		$('#tagname').attr('image_id', image_id).attr('tagtype', (mem_or_cat === "category")? "category" : "tag_username");
	}

	function submitTag() {
		var tag = $("input#tagname").val().toLowerCase();
		var mem_or_cat = $("#tagname").attr('tagtype');
		var image_id = $('#tagname').attr('image_id');

		var tags = addTag(image_id, tag, mem_or_cat);
		if(tags) {
			if(mem_or_cat === 'category') {
				var area = $('#imagesection'+image_id).find('.cattagarea');
				if(area.find('.nocattags'))
					area.find('.nocattags').remove();
				var line="<div class='categorytag' id='categorytag"+tags['category_id']+"'>";
				//line+="<a href='/picnit/profile/"+tags['cat_tags'][n]['category']+"'>";
				line+=tags['category'];//+'</a>';
				line+="<a href='javascript:void(0);' onclick='deleteCategoryTag("+tags['category_id']+","+image_id+");'>";
				line+="<span class='tagdelete'>   delete</span>";
				line+="</a>";
				line+='</div>';

				area.append(line);
			}
			else {
				var area = $('#imagesection'+image_id).find('.memtagarea');
				if(area.find('.nomemtags'))
					area.find('.nomemtags').remove();
				
				var line="<div class='membertag' id='membertag"+tags['member_id']+"'>";
				line+="<a href='/picnit/profile/"+tags['username']+"'>"
				line+=tags['username']+'</a>'
				line+="<a href='javascript:void(0);' onclick='deleteMemberTag("+tags['member_id']+","+image_id+");'>";
				line+="<span class='tagdelete'>   delete</span>";
				line+="</a>";
				line+='</div>';

				area.append(line);
			}

			hideTag();
		}

		return false;
	}
	</script>
	<div id="tagoverlay" class="overlays">
	</div>
	<div id="tagbar" class="panels">
		<form id="tagform" onsubmit='return submitTag();'>
			<div>
			<span><input type="button" id="tagcancel" class="buttons" value="cancel"/></span>
			<span><input type="submit" id="tagsubmit" class="buttons" value="+"/></span>
			<span id="tagspan"><label for="tagname"></label><input id="tagname" class="inputs" pattern="[a-zA-Z]{3,15}" title="Tag Name must contain between 3 and 15 letters." required="required"/></span>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		$(function() {
			$('#tagname').autocomplete({
				source: function(request, response) {
					var toswtich;
					var tags;
					
					if($('#tagname').attr('tagtype') === 'category') {
						tags = getCategoryTags(request.term);
						toswitch = 'category';
					}
					else {
						tags = getUserTags(request.term);
						toswitch = 'username';
					}
					
					for(x in tags) {
						tags[x]['label'] = tags[x][toswitch];
						tags[x]['value'] = tags[x][toswitch];
					}

					response(tags);
				},
				minLength: 0,
			});
		});
		$('#tagoverlay').click(hideTag);
		$('#tagcancel').click(hideTag);
	</script>
	
	<?php
	}

	//Function that returns the confirm bar 
	function confirmbar() {
	?>
	<div id="confirmoverlay" class="overlays">
	</div>
	<div id="confirmbar" class="panels">
		<form id="confirmform">
		<div id="confmess"></div>
		<input type="button" id="confyes" class="buttons" value="yes"/>
		<input type="button" id="confno" class="buttons" value="no"/>
		</form>
	</div>
	<script type="text/javascript">
		$('#confno').click(hideConfirm);
		$('#confirmoverlay').click(hideConfirm);
		function showConfirm(msg, callback) {
			//Set up display stuff and click handlers
			$('#confmess').text(msg);
			$('#confyes').click(function() {
				callback();
				hideConfirm();
			});

			document.getElementById('confirmoverlay').style.visibility="visible";
			document.getElementById('confirmbar').style.visibility="visible";
		}
		function hideConfirm() {
			document.getElementById('confirmoverlay').style.visibility="hidden";
			document.getElementById('confirmbar').style.visibility="hidden";
			$('#confyes').unbind('click');
		}
	</script>
	<?php
	}
	?>
