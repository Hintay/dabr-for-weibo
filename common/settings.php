<?php

$GLOBALS['colour_schemes'] = array(
	1 => 'Facebook蓝|3B5998,F7F7F7,000,555,D8DFEA,EEE,FFA,DD9,3B5998,FFF,FFF',
	2 => '嘀咕橙|b50,ddd,111,555,fff,eee,ffa,dd9,e81,c40,fff',
	3 => '饭否蓝|13819F,E7F2F5,333,555,fff,E7F2F5,FFA,DD9,00CCFF,333,333',
	4 => '多彩|535F74,D1D0B4,000,555,FFEDED,FFD3D3,FFA,DD9,D33D3E,FFF,FFF',
	5 => 'Twitter蓝|1481B1,FFF,333,555,FFF,EEE,FFA,DD9,9AE4E8,333,333',
	6 => '粉色|c06,fcd,623,c8a,fee,fde,ffa,dd9,C06,fee,fee',
	7 => '绿色|293C03,ccc,000,555,fff,eee,CCE691,ACC671,495C23,919C35,fff',
	8 => '紫色|BAAECB,1F1530,9C8BB5,6D617E,362D45,4C4459,4A423E,5E5750,191432,6D617E,6D617E',
);

menu_register(array(
	'settings' => array(
		'callback' => 'settings_page',
		'title' => '设置',
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
	return theme('page', '清除Cookies', "<p>".("您清空了所有设置，可能需要重新登录。")."</p>");
}

function setting_fetch($setting, $default = NULL) {
	$settings = (array) unserialize(base64_decode($_COOKIE['settings']));
	if (array_key_exists($setting, $settings)) {
		return $settings[$setting];
	} else {
		return $default;
	}
}

function setcookie_year($name, $value) {
	$duration = time() + (3600 * 24 * 365);
	setcookie($name, $value, $duration, '/');
}

function settings_page($args) {
	if ($args[1] == 'save') {
		$settings['browser']     = $_POST['browser'];
		$settings['gwt']         = $_POST['gwt'];
		$settings['colours']     = $_POST['colours'];
		$settings['reverse']     = $_POST['reverse'];
		$settings['timestamp']   = $_POST['timestamp'];
		$settings['hide_inline'] = $_POST['hide_inline'];
		$settings['utc_offset']  = (float)$_POST['utc_offset'];
		
		//顶部设置
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
		
		//底部
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
		
		//消息上的按钮
		//$settings['buttonre'] = $_POST['buttonre'];
		$settings['buttonco'] = $_POST['buttonco'];
		$settings['buttondm'] = $_POST['buttondm'];
		$settings['buttonfav'] = $_POST['buttonfav'];
		$settings['buttonrt'] = $_POST['buttonrt'];
		$settings['buttondel'] = $_POST['buttondel'];
		$settings['buttontime'] = $_POST['buttontime'];
		$settings['buttonfrom'] = $_POST['buttonfrom'];
		//$settings['buttonend'] = $_POST['buttonend'];
		
		//额外
		//$settings['short'] = $_POST['short'];
		//$settings['longurl'] = $_POST['longurl'];
		//$settings['showthumbs'] = $_POST['showthumbs'];
		$settings['fixedtagspre'] = $_POST['fixedtagspre'];
		$settings['fixedtagspost'] = $_POST['fixedtagspost'];
		//$settings['rtsyntax'] = $_POST['rtsyntax'];
		$settings['css'] = $_POST['css'];
		$settings['tpp'] = $_POST['tpp'];
		
		//$settings['linktrans'] = $_POST['linktrans'];
		$settings['avataro'] = $_POST['avataro'];
		$settings['buttonintext'] = $_POST['buttonintext'];
		//$settings['moreinreply'] = $_POST['moreinreply'];
		$settings['piclink'] = $_POST['piclink'];
		
		// Save a user's oauth details to a MySQL table
		if (MYSQL_USERS == 'ON' && $newpass = $_POST['newpassword']) {
			user_is_authenticated();
			list($key, $secret) = explode('|', $GLOBALS['user']['password']);
			$sql = sprintf("REPLACE INTO user (username, oauth_key, oauth_secret, password) VALUES ('%s', '%s', '%s', MD5('%s'))",  mysql_escape_string(user_current_username()), mysql_escape_string($key), mysql_escape_string($secret), mysql_escape_string($newpass));
			mysql_query($sql);
		}
		
		setcookie_year('settings', base64_encode(serialize($settings)));
		twitter_refresh('');
	}

	$modes = array(
		'mobile' => ("普通手机"),
		'touch' => ("触屏手机"),
		'desktop' => ("台式电脑/笔记本"),
		'text' => ("纯文本模式"),
		//'blackberry' => ("黑莓模式(翻页置最底)"),
		'worksafe' => ("Work Safe"),
		'bigtouch' => 'Big Touch',
		//'naiping' => ("奶瓶模式(更适用于PC)"),
	);

	$gwt = array(
		'off' => ("直接打开"),
		'on' => ("通过 GWT 压缩"),
	);

	/*$ort = array(
		'no' => '不显示',
		'yes' => '显示',
	);*/
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
	
	$utc_offset = setting_fetch('utc_offset', 0);
/* returning 401 as it calls http://api.twitter.com/1/users/show.json?screen_name= (no username???)	
	if (!$utc_offset) {
		$user = twitter_user_info();
		$utc_offset = $user->utc_offset;
	}
*/
	if ($utc_offset > 0) {
		$utc_offset = '+' . $utc_offset;
	}

	$content .= '<form action="settings/save" method="post">';
	$content .= '<p>'.("个性化菜单选项").'<br />';
	if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'mobile') || (setting_fetch('browser') == 'worksafe') || (setting_fetch('browser') == 'text') || (setting_fetch('browser') == 'naiping')) {
		$content .= '<span>';
	}else{
		$content .= '<span style="display:none;">';
	}
	$content .= '<small>'.("选择你想放置在页首菜单的按钮").'</small><br />';
	$content .= '<label><input type="checkbox" name="topuser" value="yes" '. (setting_fetch('topuser') == 'yes' ? ' checked="checked" ' : '') .' /> '.("用户").'</label><br />';
	$content .= '<label><input type="checkbox" name="tophome" value="yes" '. (setting_fetch('tophome', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("首页").'</label><br />';
	$content .= '<label><input type="checkbox" name="topreplies" value="yes" '. (setting_fetch('topreplies', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("提到").'</label><br />';
	$content .= '<label><input type="checkbox" name="topcomments" value="yes" '. (setting_fetch('topcomments', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("评论").'</label><br />';
	//$content .= '<label><input type="checkbox" name="topdirects" value="yes" '. (setting_fetch('topdirects', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label><br />';
	//$content .= '<label><input type="checkbox" name="topsearch" value="yes" '. (setting_fetch('topsearch') == 'yes' ? ' checked="checked" ' : '') .' /> '.("搜索").'</label><br />';
	//$content .= '<label><input type="checkbox" name="toppublic" value="yes" '. (setting_fetch('toppublic', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("随便看看").'</label><br />';
	$content .= '<label><input type="checkbox" name="topfavourites" value="yes" '. (setting_fetch('topfavourites', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("收藏").'</label><br />';
	$content .= '<label><input type="checkbox" name="topfollowers" value="yes" '. (setting_fetch('topfollowers', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("粉丝").'</label><br />';
	$content .= '<label><input type="checkbox" name="topfriends" value="yes" '. (setting_fetch('topfriends', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("关注").'</label><br />';
	//$content .= '<label><input type="checkbox" name="toptwitpic" value="yes" '. (setting_fetch('toptwitpic') == 'yes' ? ' checked="checked" ' : '') .' /> '.("Twitpic").'</label><br />';
	$content .= '<label><input type="checkbox" name="toplogout" value="yes" '. (setting_fetch('toplogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("登出").'</label><br />';
	$content .= '</span>';
	
	if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'mobile') || (setting_fetch('browser') == 'text') || (setting_fetch('browser') == 'naiping')) {
		$content .= '<span>';
	}else{
		$content .= '<span style="display:none;">';
	}
	$content .= '<small>'.("选择你想放置在页尾菜单的按钮").'</small><br />';
	$content .= '<label><input type="checkbox" name="replies" value="yes" '. (setting_fetch('replies') == 'yes' ? ' checked="checked" ' : '') .' /> '.("提到").'</label><br />';
	$content .= '<label><input type="checkbox" name="comments" value="yes" '. (setting_fetch('comments') == 'yes' ? ' checked="checked" ' : '') .' /> '.("评论").'</label><br />';
	//$content .= '<label><input type="checkbox" name="directs" value="yes" '. (setting_fetch('directs') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label><br />';
	//$content .= '<label><input type="checkbox" name="search" value="yes" '. (setting_fetch('search') == 'yes' ? ' checked="checked" ' : '') .' /> '.("搜索").'</label><br />';
	//$content .= '<label><input type="checkbox" name="twitpic" value="yes" '. (setting_fetch('twitpic') == 'yes' ? ' checked="checked" ' : '') .' /> '.("Twitpic").'</label><br />';
	$content .= '<label><input type="checkbox" name="favourites" value="yes" '. (setting_fetch('favourites') == 'yes' ? ' checked="checked" ' : '') .' /> '.("收藏").'</label><br />';
	//$content .= '<label><input type="checkbox" name="lists" value="yes" '. (setting_fetch('lists') == 'yes' ? ' checked="checked" ' : '') .' /> '.("频道").'</label><br />';
	$content .= '<label><input type="checkbox" name="followers" value="yes" '. (setting_fetch('followers') == 'yes' ? ' checked="checked" ' : '') .' /> '.("粉丝").'</label><br />';
	$content .= '<label><input type="checkbox" name="friends" value="yes" '. (setting_fetch('friends') == 'yes' ? ' checked="checked" ' : '') .' /> '.("关注").'</label><br />';
	//$content .= '<label><input type="checkbox" name="blockings" value="yes" '. (setting_fetch('blockings') == 'yes' ? ' checked="checked" ' : '') .' /> '.("黑名单").'</label><br />';
	//$content .= '<label><input type="checkbox" name="public" value="yes" '. (setting_fetch('public') == 'yes' ? ' checked="checked" ' : '') .' /> '.("随便看看").'</label><br />';
	//$content .= '<label><input type="checkbox" name="trends" value="yes" '. (setting_fetch('trends') == 'yes' ? ' checked="checked" ' : '') .' /> '.("话题").'</label><br />';
	$content .= '<label><input type="checkbox" name="about" value="yes" '. (setting_fetch('about') == 'yes' ? ' checked="checked" ' : '') .' /> '.("关于").'</label><br />';
	$content .= '<label><input type="checkbox" name="ssettings" value="yes" '. (setting_fetch('ssettings', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("设置").'</label><br />';
	$content .= '<label><input type="checkbox" name="slogout" value="yes" '. (setting_fetch('slogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("登出").'</label><br />';
	$content .= '<label><input type="checkbox" name="srefresh" value="yes" '. (setting_fetch('srefresh', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("刷新").'</label></p><hr />';
	$content .= '</span>';
	
	$content .= '<p>'.("主题").':<br /><select name="colours">'.theme('options', $colour_schemes, setting_fetch('colours', 1)).'</select></p><hr />';
	
	$content .= '<p>'.("模式").':<br /><select name="browser">'.theme('options', $modes, $GLOBALS['current_theme']).'</select></p><hr />';
	
	$content .= '<p><label><input type="checkbox" name="buttonintext" value="yes" '. (setting_fetch('buttonintext', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("显示按钮为纯文本").'</label><br />';
	$content .= '<small>'.("选择你想放置在消息上的元素").'</small><br />';
	$content .= '<label><input type="checkbox" name="buttonrt" value="yes" '. (setting_fetch('buttonrt', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("转发").'</label>';
	$content .= '<label><input type="checkbox" name="buttonco" value="yes" '. (setting_fetch('buttonco', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("评论").'</label>';
	//$content .= '<label><input type="checkbox" name="buttonre" value="yes" '. (setting_fetch('buttonre', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("转发").'</label>';
	//$content .= '<label><input type="checkbox" name="buttondm" value="yes" '. (setting_fetch('buttondm') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label>';
	$content .= '<label><input type="checkbox" name="buttonfav" value="yes" '. (setting_fetch('buttonfav', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("收藏").'</label>';
	$content .= '<label><input type="checkbox" name="buttondel" value="yes" '. (setting_fetch('buttondel', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("删除").'</label><br />';
	$content .= '<label><input type="checkbox" name="buttontime" value="yes" '. (setting_fetch('buttontime', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("时间").'</label>';
	$content .= '<label><input type="checkbox" name="buttonfrom" value="yes" '. (setting_fetch('buttonfrom', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("客户端").'</label></p><hr>';
	//$content .= '<p><label><input type="checkbox" name="buttonend" value="yes" '. (setting_fetch('buttonend') == 'yes' ? ' checked="checked" ' : '') .' /> '.("把按钮放在每条消息的最后").'</label></p><hr>';
	//$content .= '<p><label><input type="checkbox" name="moreinreply" value="yes" '. (setting_fetch('moreinreply') == 'yes' ? ' checked="checked" ' : '') .' /> '.("回复时显示对方的消息").'</label></p><hr>';
	
	if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'text')) {
		$content .= '<span style="display:none;">';
	}else{
		$content .= '<span>';
	}
	$content .= '<p><label><input type="checkbox" name="piclink" value="yes" '. (setting_fetch('piclink') == 'yes' ? ' checked="checked" ' : '') .' /> 将微博图片显示为链接</label></p>';
	$content .= '</span>';
	$content .= '<p><label><input type="checkbox" name="avataro" value="yes"'. (setting_fetch('avataro') == 'yes' ? ' checked="checked" ' : '') .' /> '.("不显示头像").'</label></p><hr>';
	
	$content .= '<p>'.("每页消息数").' (15-200): <input type="text" id="tpp" name="tpp" value="'.setting_fetch('tpp', 20).'" maxlength="3" style="width:20px;"/><br><small><b>注意：</b>如果超出这个范围将会出现不可预料的后果！</small></p><hr />';
	
	$content .= '<p>'.("外部链接").':<br /><select name="gwt">'.theme('options', $gwt, setting_fetch('gwt', $GLOBALS['current_theme'] == 'text' ? 'on' : 'off')).'</select><br /><small>'.("Google 网页转换器(GWT)可以将第三方网页转换成更小更适合手机的页面,且节省流量.").'</small></p><hr />';

	$content .= '<p><label><input type="checkbox" name="reverse" value="yes" '. (setting_fetch('reverse') == 'yes' ? ' checked="checked" ' : '') .' /> 反转相关对话的顺序</label></p>';
	$content .= '<p><label><input type="checkbox" name="timestamp" value="yes" '. (setting_fetch('timestamp') == 'yes' ? ' checked="checked" ' : '') .' /> 显示时间戳 ' . twitter_date('H:i') . ' 而不是 25秒前</label></p>';
	$content .= '<p><label><input type="checkbox" name="hide_inline" value="yes" '. (setting_fetch('hide_inline') == 'yes' ? ' checked="checked" ' : '') .' /> 隐藏链接媒体文件 (例如TwitPic缩略图)</label></p>';
	//$content .= '<p><label>现在UTC时间为 ' . gmdate('H:i') . '， 设置一个差值 <input type="text" name="utc_offset" value="'. $utc_offset .'" size="3" /> 使时间显示为 ' . twitter_date('H:i') . '.<br />如果时间显示错误请调整该值。</label></p><hr />';
	//$content .= '<p><label><input type="checkbox" name="linktrans" value="yes" '. (setting_fetch('linktrans') == 'yes' ? ' checked="checked" ' : '') .' /> '.("显示链接地址为 [link]").'</label></p><hr />';
	//$content .= '<p>'.("短链接").':<br /><select name="short">'.theme('options', $short, setting_fetch('short', 'is.gd')).'</select></p><hr />';
	//if (LONG_URL == 1) {$content .= '<p><label><input type="checkbox" name="longurl" value="yes" '. (setting_fetch('longurl') == 'yes' ? ' checked="checked" ' : '') .' /> '.("还原所有短链为原始地址").'</label></p><hr />';}
	//$content .= '<p><label><input type="checkbox" name="showthumbs" value="yes" '. (setting_fetch('showthumbs', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("在首页预览图片").'</label></p><hr>';
	$content .= '<p>'.("固定标签").': <input type="text" id="fixedtagspre" name="fixedtagspre" value="'.setting_fetch('fixedtagspre').'" maxlength="70" style="width:40px;" /> '.("消息内容").' <input type="text" id="fixedtagspost" name="fixedtagspost" value="'.setting_fetch('fixedtagspost').'" maxlength="70" style="width:40px;" /><br /><small>'.("将自动添加标签在您的消息中").'</small></p><hr />';

	//$content .= '<p>'.("RT 格式").':<br /><input type="text" id="rtsyntax" name="rtsyntax" value="'.setting_fetch('rtsyntax', 'RT [User]: [Content]').'" maxlength="140" /><br /><small>'.("默认RT格式: RT [User]: [Content]").'</small></p><hr />';

	$content .= '<p>'.("自定义 CSS").':<br /><textarea name="css" cols="50" rows="3" id="css" style="width:95%">'.setting_fetch('css').'</textarea></p><hr />';
	//$content .= '<p>'.("语言").':<br /><select name="locale">'.theme('options', $locale, setting_fetch('locale', 'zh_CN')).'</select></p><hr />';
	
	// Allow users to choose a Dabr password if accounts are enabled
	if (MYSQL_USERS == 'ON' && user_is_authenticated()) {
		$content .= '<fieldset><legend>Dabr account</legend><small>If you want to sign in to Dabr without going via Weibo.com in the future, create a password and we\'ll remember you.</small></p><p>Change Dabr password<br /><input type="password" name="newpassword" /><br /><small>Leave blank if you don\'t want to change it</small></fieldset>';
	}
	
	$content .= '<p><input type="submit" value="保存" /></p></form>';

	$content .= '<hr /><p>如果设置出错，请 <a href="reset">重置</a> 所有设置，但您需要重新登陆。</p>';

	return theme('page', '设置', $content);
}
