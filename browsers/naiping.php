<?php
require 'desktop.php';
function naiping_theme_status_form($text = '', $in_reply_to_id = NULL) {
	return desktop_theme_status_form($text, $in_reply_to_id);
}
function naiping_theme_search_form($query) {
	return desktop_theme_search_form($query);
}

function naiping_theme_avatar($url, $force_large = false) {
	return "<img class='shead' src='$url' width='48' height='48' alt='' />";
}

function naiping_theme_page($title, $content) {
	$body = theme('menu_top');
	$body .= $content;
	$page = ($_GET['page'] == 0 ? null : " - Page ".$_GET['page'])." - ";
	echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=320"/><title>'.$title.$page.NETPUTWEETS_TITLE.'</title><base href="'.BASE_URF.'" />'.theme('css').'<link href="'.BASE_URF.'favicon.ico" rel="shortcut icon" type="image/x-icon" />';if(($_GET['q'] == 'directs/inbox') || ($_GET['q'] == 'directs') || ($_GET['q'] == 'directs/sent')){echo '<style type="text/css">a.tl-dm{display:block;}</style>';}echo '</head><body id="thepage">',$body,'</body></html>';
	exit();
}

function naiping_theme_menu_top() {
	$links = array();
	$main_menu_titles = array(("首页"), ("@我的"), ("评论"), ("私信"), ("搜索"));
	foreach (menu_visible_items() as $url => $page) {
		$title = $url ? $page['title'] : ("首页");
		$type = in_array($title, $main_menu_titles) ? 'main' : 'extras';
		$links[$type][] = "<a href='".BASE_URL."$url' class='$title'>$title</a>";
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

function naiping_theme_css() {
	$out = theme_css().'<link rel="stylesheet" href="'.BASE_URF.'browsers/naiping.css" /><script type="text/javascript">'.file_get_contents('browsers/touch.js').'</script>';
	return $out;
}

function naiping_theme_timeline($feed) {
	if (count($feed) == 0) return theme('no_tweets');
	$rows = array();
	$page = menu_current_page();
	$date_heading = false;
	foreach ($feed as $status) {
		$time = strtotime($status->created_at);
		if ($time > 0) {
			$date = twitter_date('l jS F Y', strtotime($status->created_at));
			if ($date_heading !== $date) {
				$date_heading = $date;
				$rows[] = array(array(
					'data' => "<small>$date</small>",
					'colspan' => 3
				));
			}
		} else {
			$date = $status->created_at;
		}
	$text = twitter_parse_tags($status->text);
	$link = theme('status_time_link', $status, !$status->is_direct);
	$actions = theme('action_icons', $status);
	$avatar = theme('avatar', $status->from->profile_image_url);
	$source = $status->source ? (" ".("来自")." {$status->source}") : '';
	if ($status->in_reply_to_status_id) {
		$replyto = __("in reply to")." <a href='status/{$status->in_reply_to_status_id}'>{$status->in_reply_to_screen_name}</a>";
	}
	if ($status->retweeted_by) {
		$retweeted_by = $status->retweeted_by->user->screen_name;
		//$retweeted = " <small class='sretweet'>".__("retweeted by")." <a href='user/{$retweeted_by}'>{$retweeted_by}</a> ".__("<span style='display:none;'>zhuanfa</span>")."</small>";
	}
	$html = "<b class='suser'><a href='".BASE_URL."user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> <span class='stext'>{$text}</span> <br /><small class='sbutton'>$link $source $retweeted</small></td><td><div class='actionlinks'>$actions</div>";
	$row = array($html);

	if ($page != 'user' && $avatar) {
		array_unshift($row, $avatar);
	}
	if ($page != 'replies' && twitter_is_reply($status)) {
		$row = array('class' => 'reply', 'data' => $row);
	}
	$rows[] = $row;
	}
	$content = theme('table', array(), $rows, array('class' => 'timeline'));
	if (count($feed) >= 15) {
		$content .= theme('pagination');
	}
	return $content;
}

function naiping_theme_action_icons($status) {
	$user = $status->from->screen_name;
	$actions = array();
	if (!$status->is_direct) {
		$actions[] = "<a class='tl-re' href='".BASE_URL."user/{$user}/reply/{$status->id}'>@</a>";
	}
	if ($status->user->screen_name != user_current_username()) {
		$actions[] = "<a class='tl-dm' href='".BASE_URL."directs/create/{$user}'><img src='".BASE_URF."images/dm.png' alt='' /></a>";
		$actions[] = " <a class='tl-dm' href='".BASE_URL."directs/delete/{$status->id}'><img src='".BASE_URF."images/trash.gif' alt='' /></a>";
	}
	if (!$status->is_direct) {
		if ($status->favorited == '1') {
			$actions[] = "<a class='tl-uf' href='".BASE_URL."unfavourite/{$status->id}'>UnFav</a>";
		} else {
			$actions[] = "<a class='tl-fa' href='".BASE_URL."favourite/{$status->id}'>Fav</a>";
		}
	$actions[] = "<a class='tl-rt' href='".BASE_URL."retweet/{$status->id}'>RT</a>";
	}
	return implode(' ', $actions);
}
?>