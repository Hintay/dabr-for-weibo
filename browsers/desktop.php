<?php
function desktop_theme_status_form($text = '', $in_reply_to_id = NULL) {
	if (user_is_authenticated()) {
		$output = '<form method="post" action="'.BASE_URL.'update"><textarea id="status" name="status" rows="3" style="width:100%; max-width: 400px;">'.$text.'</textarea><div><input name="in_reply_to_id" value="'.$in_reply_to_id.'" type="hidden" /><input type="submit" value="'.('发送').'" /> <span id="remaining">140</span></div></form>'.js_counter('status');
		return $output;
	}
}

function desktop_theme_search_form($query) {
	$query = stripslashes(htmlspecialchars($query));
	return "<form action='".BASE_URL."search' method='get'><input name='query' value=\"$query\" style='width:100%; max-width: 300px' /><input type='submit' value='Search' /></form>";
}
?>