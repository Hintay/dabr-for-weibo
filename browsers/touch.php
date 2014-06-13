<?php
require 'desktop.php';
function touch_theme_status_form($text = '', $in_reply_to_id = NULL) {
	return desktop_theme_status_form($text, $in_reply_to_id);
}
function touch_theme_search_form($query) {
	return desktop_theme_search_form($query);
}
function touch_theme_avatar($url, $force_large = false) {
	return "<img class='shead' src='$url' width='48' height='48' />";
}
function touch_theme_page($title, $content) {
	$body = theme('menu_top');
	$body .= $content;
	$page = ($_GET['page'] == 0 ? null : " - Page ".$_GET['page'])." - ";
	echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=320"/><link href="'.BASE_URF.'favicon.ico" rel="shortcut icon" type="image/x-icon" /><title>'.$title.$page.NETPUTWEETS_TITLE.'</title><base href="'.BASE_URF.'" />'.theme('css').'</head><body id="thepage">'.$body.'</body></html>';
	exit();
}
function touch_theme_menu_top() {
	$links = array();
	$main_menu_titles = array(("首页"), ("@我的"), ("评论"), ("私信"), ("搜索"));
	foreach (menu_visible_items() as $url => $page) {
		$title = $url ? $page['title'] : ("首页");
		$type = in_array($title, $main_menu_titles) ? 'main' : 'extras';
		$links[$type][] = "<a href='".BASE_URL."$url'>$title</a>";
	}
	if (user_is_authenticated()) {
		$user = user_current_username();
		array_unshift($links['extras'], "<b><a href='".BASE_URL."user/$user'>$user</a></b>");
	}
	array_push($links['main'], '<a href="#" onclick="return toggleMenu()">'.('更多').'</a>');
	$html = '<div id="menu" class="menu">';
	$html .= theme('list', $links['main'], array('id' => 'menu-main'));
	$html .= theme('list', $links['extras'], array('id' => 'menu-extras'));
	$html .= '</div>';
	return $html;
}

function touch_theme_menu_bottom() {
	return '';
}

function touch_theme_css() {
	$out = '<link rel="stylesheet" href="'.BASE_URF.'browsers/touch.css" />'.theme_css().'<script type="text/javascript">'.file_get_contents('browsers/touch.js').'</script>';
	//~ $out .= '<style type="text/css">body { word-wrap: break-word; text-overflow: ellipsis; } table {width: 320px;}</style>';
	return $out;
}
?>