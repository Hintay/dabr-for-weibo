<?php
function blackberry_theme_pagination() {
	$page = intval($_GET['page']);
	if (preg_match('#&q(.*)#', $_SERVER['QUERY_STRING'], $matches)) {
		$query = $matches[0];
	}
	if ($page == 0) $page = 1;
	$ht = ( BASE_URL == BASE_URF ? "{$_GET['q']}?" : "index.php?q={$_GET['q']}&");
	if ($page > 1) $links[] = "<a href='".BASE_URL."{$_GET['q']}?page=".($page-1)."$query' accesskey='8'>".("上一页")."</a> 8";
	$links[] = "<a href='".BASE_URL.$ht."page=".($page+1)."$query' accesskey='9'>".("下一页")."</a> 9";
	return '<div>'.implode(' | ', $links).'</div>';
}
function blackberry_theme_menu_bottom() {
	$links = array();
	$links[] = "<a href='".BASE_URL."'>".("首页")."</a>";
	if (user_is_authenticated()) {
		if (setting_fetch('replies') == 'yes') {
			$links[] = "<a href='".BASE_URL."replies'>".("@我的")."</a>";
		}
		if (setting_fetch('retweets') == 'yes') {
			$links[] = "<a href='".BASE_URL."comments'>".("评论")."</a>";
		}
		if (setting_fetch('directs') == 'yes') {
			$links[] = "<a href='".BASE_URL."directs'>".("私信")."</a>";
		}
		if (setting_fetch('search') == 'yes') {
			$links[] = "<a href='".BASE_URL."search'>".("搜索")."</a>";
		}
		if (setting_fetch('twitpic') == 'yes') {
			$links[] = "<a href='".BASE_URL."twitpic'>".("Twitpic")."</a>";
		}
		if (setting_fetch('favourites') == 'yes') {
			$links[] = "<a href='".BASE_URL."favourites'>".("收藏")."</a>";
		}
		if (setting_fetch('lists') == 'yes') {
			$links[] = "<a href='".BASE_URL."lists'>".("频道")."</a>";
		}
		if (setting_fetch('followers') == 'yes') {
			$links[] = "<a href='".BASE_URL."followers'>".("粉丝")."</a>";
		}
		if (setting_fetch('friends') == 'yes') {
			$links[] = "<a href='".BASE_URL."friends'>".("关注")."</a>";
		}
		if (setting_fetch('blockings') == 'yes') {
			$links[] = "<a href='".BASE_URL."blockings'>".("黑名单")."</a>";
		}
		if (setting_fetch('public') == 'yes') {
			$links[] = "<a href='".BASE_URL."public'>".("随便看看")."</a>";
		}
		if (setting_fetch('trends') == 'yes') {
			$links[] = "<a href='".BASE_URL."trends'>".("频道")."</a>";
		}
	}

	if (user_is_authenticated()) {
		$user = user_current_username();
		array_unshift($links, "<b><a href='".BASE_URL."user/$user'>$user</a></b>");
		if (setting_fetch('about') == 'yes') {
			$links[] = "<a href='".BASE_URL."about'>".("关于")."</a>";
		}
		if (setting_fetch('ssettings', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."settings'>".("设置")."</a>";
		}
		if (setting_fetch('slogout') == 'yes') {
			$links[] = "<a href='".BASE_URL."logout'>".("退出")."</a>";
		}
	}
	if (setting_fetch('srefresh', 'yes') == 'yes') {
		$links[] = "<a href='".BASE_URL."{$_GET['q']}' accesskey='5'>".("刷新")."</a> 5";
	}
	return "<img src='http://img.tongji.linezing.com/1228959/tongji.gif' style='display:none;' /><div class='menu menu-$menu'>".implode(' | ', $links)."</div>".theme('pagination');
}
?>