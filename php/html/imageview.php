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
<div id="imgoverlay" class="overlays">
</div>
<div id="imgviewer">
	<div id="editor">
		<div id="menu">
		</div>
		<div id="image" class="panels">
			<div id="next" class="viewerbuttons">
			</div>
			<div id="prev" class="viewerbuttons">
			</div>
			<img id="theimage" src="images/gui/test.jpg" alt="Pulpit rock" height="200px" width="200px"/>
		</div>
	</div>
	<div id="comments" class="panels">
	</div>
</div>