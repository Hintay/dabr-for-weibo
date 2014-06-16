<?php

$menu_registry = array();

function menu_register($items) {
  foreach ($items as $url => $item) {
    $GLOBALS['menu_registry'][$url] = $item;
  }
}

function menu_execute_active_handler() {
  $query = (array) explode('/', $_GET['q']);
  $GLOBALS['page'] = $query[0];
  $page = $GLOBALS['menu_registry'][$GLOBALS['page']];
  if (!$page) {
    header('HTTP/1.0 404 Not Found');
    die('404 - Page not found.');
  }
  
  if ($page['security'])
    user_ensure_authenticated();

  if (function_exists('config_log_request'))
    config_log_request();
  
  if (function_exists($page['callback']))
    return call_user_func($page['callback'], $query);

  return false;
}

function menu_current_page() {
  return $GLOBALS['page'];
}

function menu_visible_items() {
  static $items;
  if (!isset($items)) {
    $items = array();
    foreach ($GLOBALS['menu_registry'] as $url => $page) {
      if ($page['security'] && !user_is_authenticated()) continue;
      if ($page['hidden']) continue;
      $items[$url] = $page;
    }
  }
  return $items;
}

/*function theme_menu_top() {
  return theme('menu_both', 'top');
}

function theme_menu_bottom() {
  return theme('menu_both', 'bottom');
}*/

function theme_menu_top() {
  return theme('menu_toptop', 'top');
}

function theme_menu_bottom() {
  return theme('menu_bottomtom', 'bottom');
}

function theme_menu_toptop($menu) {
	$links = array();
	if (user_is_authenticated()){
		if (setting_fetch('topuser') == 'yes') {
			$user = user_current_username();
			$links[] = "<a href='".BASE_URL."user/$user'>$user</a>";
		}
		if (setting_fetch('tophome', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."'>".("首页")."</a>";
		}
		if (setting_fetch('topreplies', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."mentions'>".("提到")."</a>";
		}
		if (setting_fetch('topcomments', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."cmts'>".("评论")."</a>";
		}
		if (setting_fetch('topdirects') == 'yes') {
			$links[] = "<a href='".BASE_URL."directs'>".("私信")."</a>";
		}
		if (setting_fetch('topsearch') == 'yes') {
			$links[] = "<a href='".BASE_URL."search'>".("搜索")."</a>";
		}
		if (setting_fetch('toppublic') == 'yes') {
			$links[] = "<a href='".BASE_URL."public'>".("随便看看")."</a>";
		}
		if (setting_fetch('topfavourites', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."favourites'>".("收藏")."</a>";
		}
		if (setting_fetch('topfollowers', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."followers'>".("粉丝")."</a>";
		}
		if (setting_fetch('topfriends', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."friends'>".("关注")."</a>";
		}
		if (setting_fetch('toptwitpic') == 'yes') {
			$links[] = "<a href='".BASE_URL."twitpic'>".("Twitpic")."</a>";
		}
		if (setting_fetch('topsettings', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."settings'>".("设置")."</a>";
		}
		if (setting_fetch('toplogout', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."logout'>".("登出")."</a>";
		}
	} else {
		$links[] = "<span style=font-weight:bold;color:#FFF;>".DABR_TITLE."</span>";
	}
	return "<div class='menu menu-$menu'>".implode(' | ', $links).'</div>';
}

function theme_menu_bottomtom($menu) {
	$links = array();
	$links[] = "<a href='".BASE_URL."'>".("首页")."</a>";
	if (user_is_authenticated()) {
		if (setting_fetch('replies') == 'yes') {
			$links[] = "<a href='".BASE_URL."mentions'>".("提到")."</a>";
		}
		if (setting_fetch('comments') == 'yes') {
			$links[] = "<a href='".BASE_URL."cmts'>".("评论")."</a>";
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
			$links[] = "<a href='".BASE_URL."trends'>".("话题")."</a>";
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
		if (setting_fetch('slogout', 'yes') == 'yes') {
			$links[] = "<a href='".BASE_URL."logout'>".("登出")."</a>";
		}
	}
	if (setting_fetch('srefresh', 'yes') == 'yes') {
		$links[] = "<a href='".BASE_URL."{$_GET['q']}' accesskey='5'>".("刷新")."</a> 5";
	}
	return '<img src="#" style="display:none;" /><div class="menu menu-$menu">'.implode(' | ', $links).'</div>';
}

function theme_menu_both($menu) {
  $links = array();
  foreach (menu_visible_items() as $url => $page) {
    $title = $url ? $url : 'home';
    if (!$url) $url = BASE_URL; // Shouldn't be required, due to <base> element but some browsers are stupid.
    if ($menu == 'bottom' && isset($page['accesskey'])) {
      $links[] = "<a href='$url' accesskey='{$page['accesskey']}'>$title</a> {$page['accesskey']}";
    } else {
      $links[] = "<a href='$url'>$title</a>";
    }
  }
  if (user_is_authenticated()) {
    $user = $GLOBALS['user']['screen_name'];
    array_unshift($links, "<b><a href='user/$user'>$user</a></b>");
  }
  if ($menu == 'bottom') {
    $links[] = "<a href='{$_GET['q']}' accesskey='5'>refresh</a> 5";
  }
  return "<div class='menu menu-$menu'>".implode(' | ', $links).'</div>';
}

?>
