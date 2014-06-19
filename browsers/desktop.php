<?php
function desktop_theme_status_form($text = '', $in_reply_to_id = NULL) {
	if (user_is_authenticated()) {
		$fixedtags = ((setting_fetch('fixedtago', 'no') == "yes") && ($text == '')) ? "#".setting_fetch('fixedtagc')."#" : null;
		$output = '<form method="post" action="'.BASE_URL.'update"><textarea id="status" name="status" rows="3" style="width:100%; max-width: 400px;">'.$text.$fixedtags.'</textarea>';
		if (setting_fetch('buttongeo') == 'yes') {
			$output .= '
<br /><span id="geo" style="display: inline;"><input onclick="goGeo()" type="checkbox" id="geoloc" name="location" /> <label for="geoloc" id="lblGeo"></label></span>
<script type="text/javascript">
started = false;
chkbox = document.getElementById("geoloc");
if (navigator.geolocation) {
	geoStatus("'.__("Add my location").'");
	if ("'.$_COOKIE['geo'].'"=="Y") {
		chkbox.checked = true;
		goGeo();
	}
}
function goGeo(node) {
	if (started) return;
	started = true;
	geoStatus("'.__("Locating...").'");
	navigator.geolocation.getCurrentPosition(geoSuccess, geoStatus, {enableHighAccuracy: true});
}
function geoStatus(msg) {
	document.getElementById("geo").style.display = "inline";
	document.getElementById("lblGeo").innerHTML = msg;
}
function geoSuccess(position) {
	if(typeof position.address !== "undefined")
		geoStatus("'.__("Add my ").'<a href=\'https://maps.google.com/maps?q=loc:" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>location</a>" + " (" + position.address.country + position.address.region + "'.__(" Province ").'" + position.address.city + "'.__(" City").', '.__("accuracy: ").'" + position.coords.accuracy + "m)");
	else
		geoStatus("'.__("Add my ").'<a href=\'https://maps.google.com/maps?q=loc:" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>'.__("location").'</a>" + " ('.__("accuracy: ").'" + position.coords.accuracy + "m)");
	chkbox.value = position.coords.latitude + "," + position.coords.longitude;
}
</script>
';
        	}
		$output .= '<div><input type="submit" value="'.__('Update').'" /> <span id="remaining">140</span>  <a href="'.BASE_URL.'upload">'.__('Upload Picture').'</a></div></form>';
		$output .= js_counter('status');
		return $output;
	}
}

function desktop_theme_search_form($query) {
	$query = stripslashes(htmlentities($query,ENT_QUOTES,"UTF-8"));
	return "<form action='search' method='get'><input name='query' value=\"$query\" style='width:100%; max-width: 300px' /><input type='submit' value='Search' /></form>";
}
?>
