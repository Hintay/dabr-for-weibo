<?php
$GLOBALS['colour_schemes'] = array(
	1 => 'Facebook Blue|3B5998,F7F7F7,000,555,D8DFEA,EEE,FFA,DD9,3B5998,FFF,FFF',
	2 => 'Digu Orange|b50,ddd,111,555,fff,eee,ffa,dd9,e81,c40,fff',
	3 => 'Fanfou Blue|13819F,E7F2F5,333,555,fff,E7F2F5,FFA,DD9,00CCFF,333,333',
	4 => 'Colorful|535F74,D1D0B4,000,555,FFEDED,FFD3D3,FFA,DD9,D33D3E,FFF,FFF',
	5 => 'Twitter Blue|1481B1,FFF,333,555,FFF,EEE,FFA,DD9,9AE4E8,333,333',
	6 => 'Whimsical Pink|c06,fcd,623,c8a,fee,fde,ffa,dd9,C06,fee,fee',
	7 => 'Green|293C03,ccc,000,555,fff,eee,CCE691,ACC671,495C23,919C35,fff',
	8 => 'Purple|BAAECB,1F1530,9C8BB5,6D617E,362D45,4C4459,4A423E,5E5750,191432,6D617E,6D617E',
);

menu_register(array(
	'settings' => array(
		'callback' => 'settings_page',
		'title' => ("设置"),
	),
	'reset' => array(
		'hidden' => true,
		'callback' => 'cookie_monster',
	),
));

function cookie_monster() {
	$cookies = array(
		'browser',
		'settings',
		'utc_offset',
		'search_favourite',
		'USER_AUTH',
	);
	$duration = time() - 3600;
	foreach ($cookies as $cookie) {
		setcookie($cookie, NULL, $duration, '/');
		setcookie($cookie, NULL, $duration);
	}
	return theme('page', ("清除Cookies"), "<p>".("您已经退出,同时清空了所有设置.请重新登录!")."</p>");
}

function setting_fetch($setting, $default = NULL) {
	$settings = (array) unserialize(base64_decode($_COOKIE['settings']));
	if (array_key_exists($setting, $settings)) {
		return $settings[$setting];
	} else {
		return $default;
	}
}

function settings_page($args) {
	if ($args[1] == 'save') {
		$settings['browser'] = $_POST['browser'];
		$settings['tpp'] = $_POST['tpp'];
		$settings['gwt'] = $_POST['gwt'];
		//$settings['ort'] = $_POST['ort'];
		//$settings['locale'] = $_POST['locale'];
		$settings['colours'] = $_POST['colours'];
		//$settings['reverse'] = $_POST['reverse'];

		$settings['topuser'] = $_POST['topuser'];
		$settings['tophome'] = $_POST['tophome'];
		$settings['topreplies'] = $_POST['topreplies'];
		$settings['topcomments'] = $_POST['topcomments'];
		$settings['topdirects'] = $_POST['topdirects'];
		$settings['topsearch'] = $_POST['topsearch'];
		$settings['toppublic'] = $_POST['toppublic'];
		$settings['topfavourites'] = $_POST['topfavourites'];
		$settings['topfollowers'] = $_POST['topfollowers'];
		$settings['topfriends'] = $_POST['topfriends'];
		$settings['toptwitpic'] = $_POST['toptwitpic'];

		$settings['replies'] = $_POST['replies'];
		$settings['comments'] = $_POST['comments'];
		$settings['directs'] = $_POST['directs'];
		$settings['search'] = $_POST['search'];
		$settings['public'] = $_POST['public'];
		$settings['favourites'] = $_POST['favourites'];
		$settings['lists'] = $_POST['lists'];
		$settings['followers'] = $_POST['followers'];
		$settings['friends'] = $_POST['friends'];
		$settings['blockings'] = $_POST['blockings'];
		$settings['trends'] = $_POST['trends'];
		$settings['twitpic'] = $_POST['twitpic'];
		$settings['about'] = $_POST['about'];
		$settings['ssettings'] = $_POST['ssettings'];
		$settings['slogout'] = $_POST['slogout'];
		$settings['srefresh'] = $_POST['srefresh'];

		$settings['linktrans'] = $_POST['linktrans'];
		$settings['avataro'] = $_POST['avataro'];
		$settings['buttonintext'] = $_POST['buttonintext'];
		//$settings['moreinreply'] = $_POST['moreinreply'];
		
		//$settings['buttonre'] = $_POST['buttonre'];
		$settings['buttonco'] = $_POST['buttonco'];
		$settings['buttondm'] = $_POST['buttondm'];
		$settings['buttonfav'] = $_POST['buttonfav'];
		$settings['buttonrt'] = $_POST['buttonrt'];
		$settings['buttondel'] = $_POST['buttondel'];
		$settings['buttontime'] = $_POST['buttontime'];
		$settings['buttonfrom'] = $_POST['buttonfrom'];
		//$settings['buttonend'] = $_POST['buttonend'];
		
		//$settings['short'] = $_POST['short'];
		//$settings['longurl'] = $_POST['longurl'];
		//$settings['showthumbs'] = $_POST['showthumbs'];
		$settings['fixedtagspre'] = $_POST['fixedtagspre'];
		$settings['fixedtagspost'] = $_POST['fixedtagspost'];
		//$settings['rtsyntax'] = $_POST['rtsyntax'];
		$settings['css'] = $_POST['css'];
		$duration = time() + (3600 * 24 * 365);
		setcookie('settings', base64_encode(serialize($settings)), $duration, '/');
		settings_refresh('');
	}
	
	$modes = array(
		'mobile' => ("普通手机"),
		//'touch' => ("触屏手机"),
		'desktop' => ("台式电脑/笔记本"),
		//'text' => ("纯文本模式(更适用于UCWeb)"),
		//'blackberry' => ("黑莓模式(翻页置最底)"),
		'worksafe' => ("Work Safe"),
		'naiping' => ("奶瓶模式(更适用于PC)"),
	);
	
	$gwt = array(
		'off' => ("直接打开"),
		'on' => ("通过 GWT 压缩"),
	);

	/*$ort = array(
		'no' => '不显示',
		'yes' => '显示',
	);*/

	$tpp = array(
		20 => '20',
		40 => '40',
		60 => '60',
		80 => '80',
		120 => '120',
		200 => '200',
	);

	/*$short = array(
		'no' => ("不使用"),
		'is.gd' => 'is.gd',
		'tr.im' => 'tr.im',
		'goo.gl' => 'goo.gl',
		'tinyurl.com' => 'tinyurl.com',
		'j.mp' => 'j.mp',
		'r.im' => 'r.im',
	);

	$locale = array(
		'zh_CN' => 简体中文,
		'en_US' => English,
		'zh_TW' => 繁體中文,
	);*/

	$colour_schemes = array();
	foreach ($GLOBALS['colour_schemes'] as $id => $info) {
		list($name, $colours) = explode('|', $info);
		$colour_schemes[$id] = $name;
	}

	$content .= '<form action="'.BASE_URL.'settings/save" method="post">';
	$content .= '<p>'.("个性化菜单选项").'<br />';
	$content .= '<small>'.("选择你想放置在页首菜单的按钮").'</small><br />';
	$content .= '<label><input type="checkbox" name="topuser" value="yes" '. (setting_fetch('topuser') == 'yes' ? ' checked="checked" ' : '') .' /> '.("用户").'</label><br />';
	$content .= '<label><input type="checkbox" name="tophome" value="yes" '. (setting_fetch('tophome', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("首页").'</label><br />';
	$content .= '<label><input type="checkbox" name="topreplies" value="yes" '. (setting_fetch('topreplies', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("@我的").'</label><br />';
	$content .= '<label><input type="checkbox" name="topcomments" value="yes" '. (setting_fetch('topcomments', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("评论").'</label><br />';
	$content .= '<label><input type="checkbox" name="topdirects" value="yes" '. (setting_fetch('topdirects', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label><br />';
	$content .= '<label><input type="checkbox" name="topsearch" value="yes" '. (setting_fetch('topsearch') == 'yes' ? ' checked="checked" ' : '') .' /> '.("搜索").'</label><br />';
	$content .= '<label><input type="checkbox" name="toppublic" value="yes" '. (setting_fetch('toppublic', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("随便看看").'</label><br />';
	$content .= '<label><input type="checkbox" name="topfavourites" value="yes" '. (setting_fetch('topfavourites', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("收藏").'</label><br />';
	$content .= '<label><input type="checkbox" name="topfollowers" value="yes" '. (setting_fetch('topfollowers', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("粉丝").'</label><br />';
	$content .= '<label><input type="checkbox" name="topfriends" value="yes" '. (setting_fetch('topfriends', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("关注").'</label><br />';
	$content .= '<label><input type="checkbox" name="toptwitpic" value="yes" '. (setting_fetch('toptwitpic') == 'yes' ? ' checked="checked" ' : '') .' /> '.("Twitpic").'</label><br />';
	$content .= '<label><input type="checkbox" name="toplogout" value="yes" '. (setting_fetch('toplogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("退出").'</label><br />';
	$content .= '<small>'.("选择你想放置在页尾菜单的按钮").'</small><br />';
	$content .= '<label><input type="checkbox" name="replies" value="yes" '. (setting_fetch('replies') == 'yes' ? ' checked="checked" ' : '') .' /> '.("@我的").'</label><br />';
	$content .= '<label><input type="checkbox" name="comments" value="yes" '. (setting_fetch('comments') == 'yes' ? ' checked="checked" ' : '') .' /> '.("评论").'</label><br />';
	$content .= '<label><input type="checkbox" name="directs" value="yes" '. (setting_fetch('directs') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label><br />';
	$content .= '<label><input type="checkbox" name="search" value="yes" '. (setting_fetch('search') == 'yes' ? ' checked="checked" ' : '') .' /> '.("搜索").'</label><br />';
	$content .= '<label><input type="checkbox" name="twitpic" value="yes" '. (setting_fetch('twitpic') == 'yes' ? ' checked="checked" ' : '') .' /> '.("Twitpic").'</label><br />';
	$content .= '<label><input type="checkbox" name="favourites" value="yes" '. (setting_fetch('favourites') == 'yes' ? ' checked="checked" ' : '') .' /> '.("收藏").'</label><br />';
	$content .= '<label><input type="checkbox" name="lists" value="yes" '. (setting_fetch('lists') == 'yes' ? ' checked="checked" ' : '') .' /> '.("频道").'</label><br />';
	$content .= '<label><input type="checkbox" name="followers" value="yes" '. (setting_fetch('followers') == 'yes' ? ' checked="checked" ' : '') .' /> '.("粉丝").'</label><br />';
	$content .= '<label><input type="checkbox" name="friends" value="yes" '. (setting_fetch('friends') == 'yes' ? ' checked="checked" ' : '') .' /> '.("关注").'</label><br />';
	$content .= '<label><input type="checkbox" name="blockings" value="yes" '. (setting_fetch('blockings') == 'yes' ? ' checked="checked" ' : '') .' /> '.("黑名单").'</label><br />';
	$content .= '<label><input type="checkbox" name="public" value="yes" '. (setting_fetch('public') == 'yes' ? ' checked="checked" ' : '') .' /> '.("随便看看").'</label><br />';
	$content .= '<label><input type="checkbox" name="trends" value="yes" '. (setting_fetch('trends') == 'yes' ? ' checked="checked" ' : '') .' /> '.("话题").'</label><br />';
	$content .= '<label><input type="checkbox" name="about" value="yes" '. (setting_fetch('about') == 'yes' ? ' checked="checked" ' : '') .' /> '.("关于").'</label><br />';
	$content .= '<label><input type="checkbox" name="ssettings" value="yes" '. (setting_fetch('ssettings', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("设置").'</label><br />';
	$content .= '<label><input type="checkbox" name="slogout" value="yes" '. (setting_fetch('slogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("退出").'</label><br />';
	$content .= '<label><input type="checkbox" name="srefresh" value="yes" '. (setting_fetch('srefresh', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("刷新").'</label></p><hr />';
	$content .= '<p>'.("主题").':<br /><select name="colours">'.theme('options', $colour_schemes, setting_fetch('colours', 1)).'</select></p><hr />';
	$content .= '<p>'.("模式").':<br /><select name="browser">'.theme('options', $modes, $GLOBALS['current_theme']).'</select></p><hr />';
	$content .= '<p><label><input type="checkbox" name="buttonintext" value="yes" '. (setting_fetch('buttonintext', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("显示按钮为纯文本").'</label><br />';
	$content .= '<small>'.("选择你想放置在消息上的按钮").'</small><br />';
	$content .= '<label><input type="checkbox" name="buttonrt" value="yes" '. (setting_fetch('buttonrt', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("转发").'</label>';
	$content .= '<label><input type="checkbox" name="buttonco" value="yes" '. (setting_fetch('buttonco', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("评论").'</label>';
	//$content .= '<label><input type="checkbox" name="buttonre" value="yes" '. (setting_fetch('buttonre', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("转发").'</label>';
	$content .= '<label><input type="checkbox" name="buttondm" value="yes" '. (setting_fetch('buttondm') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label>';
	$content .= '<label><input type="checkbox" name="buttonfav" value="yes" '. (setting_fetch('buttonfav', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("收藏").'</label>';
	$content .= '<label><input type="checkbox" name="buttondel" value="yes" '. (setting_fetch('buttondel', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("删除").'</label><br />';
	$content .= '<label><input type="checkbox" name="buttontime" value="yes" '. (setting_fetch('buttontime', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("时间").'</label>';
	$content .= '<label><input type="checkbox" name="buttonfrom" value="yes" '. (setting_fetch('buttonfrom', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("客户端").'</label></p><hr>';
	//$content .= '<p><label><input type="checkbox" name="buttonend" value="yes" '. (setting_fetch('buttonend') == 'yes' ? ' checked="checked" ' : '') .' /> '.("把按钮放在每条消息的最后").'</label></p><hr>';
	//$content .= '<p><label><input type="checkbox" name="moreinreply" value="yes" '. (setting_fetch('moreinreply') == 'yes' ? ' checked="checked" ' : '') .' /> '.("回复时显示对方的消息").'</label></p><hr>';
	$content .= '<p><label><input type="checkbox" name="avataro" value="yes" '. (setting_fetch('avataro', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("不显示头像").'</label></p><hr>';
	$content .= '<p>'.("每页消息数").' (15-200): <input type="text" id="tpp" name="tpp" value="'.setting_fetch('tpp', 20).'" maxlength="3" style="width:20px;"/><br><small><b>注意：</b>如果超出这个范围将会出现不可预料的后果！</small></p><hr />';
	
	//$content .= '<p>'.("在回复页面显示官方 RT 的结果").':<br /><select name="ort">'.theme('options', $ort, setting_fetch('ort', 'no')).'</select></p><hr />';
	
	$content .= '<p>'.("外部链接").':<br /><select name="gwt">'.theme('options', $gwt, setting_fetch('gwt', $GLOBALS['current_theme'] == 'text' ? 'on' : 'off')).'</select><br /><small>'.("Google 网页转换器(GWT)可以将第三方网页转换成更小更适合手机的页面,且节省流量.").'</small></p><hr />';
	$content .= '<p><label><input type="checkbox" name="linktrans" value="yes" '. (setting_fetch('linktrans') == 'yes' ? ' checked="checked" ' : '') .' /> '.("显示链接地址为 [link]").'</label></p><hr />';
	//$content .= '<p>'.("短链接").':<br /><select name="short">'.theme('options', $short, setting_fetch('short', 'is.gd')).'</select></p><hr />';
	//if (LONG_URL == 1) {$content .= '<p><label><input type="checkbox" name="longurl" value="yes" '. (setting_fetch('longurl') == 'yes' ? ' checked="checked" ' : '') .' /> '.("还原所有短链为原始地址").'</label></p><hr />';}
	//$content .= '<p><label><input type="checkbox" name="showthumbs" value="yes" '. (setting_fetch('showthumbs', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("在首页预览图片").'</label></p><hr>';
	$content .= '<p>'.("固定标签").': <input type="text" id="fixedtagspre" name="fixedtagspre" value="'.setting_fetch('fixedtagspre').'" maxlength="70" style="width:40px;" /> '.("消息内容").' <input type="text" id="fixedtagspost" name="fixedtagspost" value="'.setting_fetch('fixedtagspost').'" maxlength="70" style="width:40px;" /><br /><small>'.("介绍: 自动添加标签在你的消息中").'</small></p><hr />';
	//$content .= '<p>'.("RT 格式").':<br /><input type="text" id="rtsyntax" name="rtsyntax" value="'.setting_fetch('rtsyntax', 'RT [User]: [Content]').'" maxlength="140" /><br /><small>'.("默认RT格式: RT [User]: [Content]").'</small></p><hr />';
	$content .= '<p>'.("自定义 CSS").':<br /><textarea name="css" cols="50" rows="3" id="css" style="width:95%">'.setting_fetch('css').'</textarea></p><hr />';
	//$content .= '<p>'.("语言").':<br /><select name="locale">'.theme('options', $locale, setting_fetch('locale', 'zh_CN')).'</select></p><hr />';
	//$content .= '<p><label><input type="checkbox" name="reverse" value="yes" '. (setting_fetch('reverse') == 'yes' ? ' checked="checked" ' : '') .' /> '.("反转相关对话的顺序").'</label></p>';
	$content .= '<div><input type="submit" name="Submit" value="'.("保存").'" /></div></form><hr /><p>'.("如果设置出错,请<a href=\"reset\">重置</a>所有设置,但你需要重新登陆.").'</p>';
	return theme('page', ("设置"), $content);
}

function settings_refresh($page = NULL) {
	if (isset($page)) {
		$page = BASE_URL . $page;
	} else {
		$page = $_SERVER['HTTP_REFERER'];
	}
	header('Location: '. $page);
	exit();
}