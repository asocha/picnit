<script>
	function hideViewer() {
		document.getElementById('imgoverlay').style.visibility="hidden";
		document.getElementById('imgviewer').style.visibility="hidden";
	}

	function showViewer() {
		document.getElementById('imgoverlay').style.visibility="visible";
		document.getElementById('imgviewer').style.visibility="visible";
	}
</script>
<div id = "imgoverlay">
</div>
<div id= "imgviewer">
	<div id = "editor">
		<div id = "menu">
		</div>
		<div id = "image">
			<div id = "next">
			</div>
			<div id = "prev">
			</div>
			<img id = "theimage" src="images/gui/test.jpg" alt="Pulpit rock" height="200px" width="200px"/>
		</div>
	</div>
	<div id = "comments">
	</div>
</div>