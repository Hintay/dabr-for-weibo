<?php

$GLOBALS['colour_schemes'] = array(
	1 => __("Facebook Blue").'|3B5998,F7F7F7,000,555,D8DFEA,EEE,FFA,DD9,3B5998,FFF,FFF',
	2 => __("Digu Orange").'|b50,ddd,111,555,fff,eee,ffa,dd9,e81,c40,fff',
	3 => __("Fanfou Blue").'|13819F,E7F2F5,333,555,fff,E7F2F5,FFA,DD9,00CCFF,333,333',
	4 => __("Colorful").'|535F74,D1D0B4,000,555,FFEDED,FFD3D3,FFA,DD9,D33D3E,FFF,FFF',
	5 => __("Twitter Blue").'|1481B1,FFF,333,555,FFF,EEE,FFA,DD9,9AE4E8,333,333',
	6 => __("Whimsical Pink").'|c06,fcd,623,c8a,fee,fde,ffa,dd9,C06,fee,fee',
	7 => __("Green").'|293C03,ccc,000,555,fff,eee,CCE691,ACC671,495C23,919C35,fff',
	8 => __("Purple").'|BAAECB,1F1530,9C8BB5,6D617E,362D45,4C4459,4A423E,5E5750,191432,6D617E,6D617E',
	9 => __("Dabr Red").'|d12,ddd,111,555,fff,eee,ffa,dd9,c12,fff,fff',
);

menu_register(array(
	'settings' => array(
		'callback' => 'settings_page',
		'title' => __("Settings"),
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
	return theme('page', __("Cookie Monster"), "<p>".__("The cookie monster has cleared all settings, maybe need logging in again.")."</p>");
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
		$settings['locale']      = $_POST['locale'];
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
		$settings['css'] = $_POST['css'];
		$settings['tpp'] = $_POST['tpp'];
		$settings['longtext'] = $_POST['longtext'];
		
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
		'mobile' => __("Normal phone"),
		'touch' => __("Touch phone"),
		'desktop' => __("PC/Laptop"),
		'text' => __("Text Only"),
		'blackberry' => __("BlackBerry (Pagination At Bottom)"),
		'worksafe' => __("Work Safe"),
		'bigtouch' => __("Big Touch"),
	);

	$gwt = array(
		'off' => __("direct"),
		'on' => __("via GWT"),
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
	);*/

	$locale = array(
		'zh_CN' => 简体中文,
		'en_US' => English,
		'zh_TW' => 繁體中文,
	);
	$longtext = array(
		'a' => __("Automatic Cut"),
		'r' => __("Return Error"),
	);
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
	$content .= '<p><b>'.__("Menu Settings").'</b></p>';
	$content .= '<small>'.__("Choose what you want to display on the Top Bar.").'</small><br />';
	$content .= '<label>　<input type="checkbox" name="topuser" value="yes" '. (setting_fetch('topuser') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("User").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="tophome" value="yes" '. (setting_fetch('tophome', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Home").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="topreplies" value="yes" '. (setting_fetch('topreplies', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Mentions").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="topcomments" value="yes" '. (setting_fetch('topcomments', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Comments").'</label><br />';
	if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'mobile') || (setting_fetch('browser') == 'worksafe') || (setting_fetch('browser') == 'text') || (setting_fetch('browser') == 'naiping')) {
		$content .= '<span>';
	}else{
		$content .= '<span style="display:none;">';
	}
	//$content .= '<label>　<input type="checkbox" name="topdirects" value="yes" '. (setting_fetch('topdirects', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="topsearch" value="yes" '. (setting_fetch('topsearch') == 'yes' ? ' checked="checked" ' : '') .' /> '.("搜索").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="toppublic" value="yes" '. (setting_fetch('toppublic', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("随便看看").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="topfavourites" value="yes" '. (setting_fetch('topfavourites', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Favourites").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="topfollowers" value="yes" '. (setting_fetch('topfollowers', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Followers").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="topfriends" value="yes" '. (setting_fetch('topfriends', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Friends").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="toptwitpic" value="yes" '. (setting_fetch('toptwitpic') == 'yes' ? ' checked="checked" ' : '') .' /> '.("Twitpic").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="toplogout" value="yes" '. (setting_fetch('toplogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Logout").'</label><br />';
	$content .= '</span>';
	
	if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'mobile') || (setting_fetch('browser') == 'text') || (setting_fetch('browser') == 'naiping')) {
		$content .= '<span>';
	}else{
		$content .= '<span style="display:none;">';
	}
	$content .= '<small>'.__("Choose what you want to display on the Bottom Bar.").'</small><br />';
	$content .= '<label>　<input type="checkbox" name="replies" value="yes" '. (setting_fetch('replies') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Mentions").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="comments" value="yes" '. (setting_fetch('comments') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Comments").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="directs" value="yes" '. (setting_fetch('directs') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="search" value="yes" '. (setting_fetch('search') == 'yes' ? ' checked="checked" ' : '') .' /> '.("搜索").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="favourites" value="yes" '. (setting_fetch('favourites') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Favourites").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="lists" value="yes" '. (setting_fetch('lists') == 'yes' ? ' checked="checked" ' : '') .' /> '.("频道").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="followers" value="yes" '. (setting_fetch('followers') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Followers").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="friends" value="yes" '. (setting_fetch('friends') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Friends").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="blockings" value="yes" '. (setting_fetch('blockings') == 'yes' ? ' checked="checked" ' : '') .' /> '.("黑名单").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="public" value="yes" '. (setting_fetch('public') == 'yes' ? ' checked="checked" ' : '') .' /> '.("随便看看").'</label><br />';
	//$content .= '<label>　<input type="checkbox" name="trends" value="yes" '. (setting_fetch('trends') == 'yes' ? ' checked="checked" ' : '') .' /> '.("话题").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="about" value="yes" '. (setting_fetch('about', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("About").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="ssettings" value="yes" '. (setting_fetch('ssettings', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Settings").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="slogout" value="yes" '. (setting_fetch('slogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Logout").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="srefresh" value="yes" '. (setting_fetch('srefresh', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Refresh").'</label>';
	$content .= '</span>';
	
	$content .= '<p><b>'.__("Status Settings").'</b></p>';
	$content .= '<label>　<input type="checkbox" name="buttonintext" value="yes" '. (setting_fetch('buttonintext', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Show @/DM/RT/FAV/DEL As Text instead of images").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="avataro" value="yes"'. (setting_fetch('avataro') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Do Not Show Avatar").'</label><br/>';
	if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'text')) {
		$content .= '<span style="display:none;">';
	}else{
		$content .= '<span>';
	}
	$content .= '<label>　<input type="checkbox" name="piclink" value="yes" '. (setting_fetch('piclink') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Show Images As Link").'</label><br />';
	$content .= '</span>';
	//$content .= '<label>　<input type="checkbox" name="reverse" value="yes" '. (setting_fetch('reverse') == 'yes' ? ' checked="checked" ' : '') .' /> 反转相关对话的顺序</label><br />';
	$content .= '<label>　<input type="checkbox" name="timestamp" value="yes" '. (setting_fetch('timestamp') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Show the timestamp ") . twitter_date('H:i') . __(" instead of 25 sec ago") .'</label><br />';
	if (function_exists('mb_strlen')) $content .= '<label>　'.__("When posting a 140+ chars weibo: ").'<select name="longtext">'.theme('options', $longtext, setting_fetch('longtext', 'r')).'</select></label><br />';
	//$content .= '<label>　<input type="checkbox" name="hide_inline" value="yes" '. (setting_fetch('hide_inline') == 'yes' ? ' checked="checked" ' : '') .' /> 隐藏链接媒体文件 (例如TwitPic缩略图)</label><br />';
	$content .= '<p>　'.__("Fixed Tag Pre").': <input type="text" id="fixedtagspre" name="fixedtagspre" value="'.setting_fetch('fixedtagspre').'" maxlength="70" style="width:40px;" /> '.__("Fixed Tag After Post").': <input type="text" id="fixedtagspost" name="fixedtagspost" value="'.setting_fetch('fixedtagspost').'" maxlength="70" style="width:40px;" /><br /><small>　'.__("Will automatically add the tags in your status.").'</small></p>';

	$content .= '<small>'.__("Choose what you want to display on the Status.").'</small><br />';
	$content .= '<label>　<input type="checkbox" name="buttonrt" value="yes" '. (setting_fetch('buttonrt', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("RT").'</label>';
	$content .= '<label>　<input type="checkbox" name="buttonco" value="yes" '. (setting_fetch('buttonco', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("CM").'</label>';
	//$content .= '<label>　<input type="checkbox" name="buttonre" value="yes" '. (setting_fetch('buttonre', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.("转发").'</label>';
	//$content .= '<label>　<input type="checkbox" name="buttondm" value="yes" '. (setting_fetch('buttondm') == 'yes' ? ' checked="checked" ' : '') .' /> '.("私信").'</label>';
	$content .= '<label>　<input type="checkbox" name="buttonfav" value="yes" '. (setting_fetch('buttonfav', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("FAV").'</label>';
	$content .= '<label>　<input type="checkbox" name="buttondel" value="yes" '. (setting_fetch('buttondel', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("DEL").'</label><br />';
	$content .= '<label>　<input type="checkbox" name="buttontime" value="yes" '. (setting_fetch('buttontime', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("Time").'</label>';
	$content .= '<label>　<input type="checkbox" name="buttonfrom" value="yes" '. (setting_fetch('buttonfrom', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> '.__("From").'</label><hr />';
	//$content .= '<p><label>　<input type="checkbox" name="buttonend" value="yes" '. (setting_fetch('buttonend') == 'yes' ? ' checked="checked" ' : '') .' /> '.("把按钮放在每条消息的最后").'</label></p><hr>';
	//$content .= '<p><label>　<input type="checkbox" name="moreinreply" value="yes" '. (setting_fetch('moreinreply') == 'yes' ? ' checked="checked" ' : '') .' /> '.("回复时显示对方的消息").'</label></p><hr>';
	
	$content .= '<p><b>'.__("Global Settings").'</b></p>';
	$content .= '<label>　'.__("Colour scheme: ").'<select name="colours">'.theme('options', $colour_schemes, setting_fetch('colours', 1)).'</select></label><br />';	
	$content .= '<label>　'.__("Mode: ").'<select name="browser">'.theme('options', $modes, $GLOBALS['current_theme']).'</select></label><br />';
	$content .= '<label>　'.__("Language: ").'<select name="locale">'.theme('options', $locale, setting_fetch('locale', 'zh_CN')).'</select></label><br />';
	
	$content .= '<label>　'.__("Tweets Per Page: ").'<input type="text" id="tpp" name="tpp" value="'.setting_fetch('tpp', 20).'" maxlength="3" style="width:20px;" /> (15-200)</label><br />';	
	$content .= '<label>　'.__("External links go:").'<select name="gwt">'.theme('options', $gwt, setting_fetch('gwt', $GLOBALS['current_theme'] == 'text' ? 'on' : 'off')).'</select><br /><small>　'.__("Google Web Transcoder (GWT) converts third-party sites into small, speedy pages suitable for older phones and people with less bandwidth.").'</small></label><p />';
	//$content .= '<p><label>现在UTC时间为 ' . gmdate('H:i') . '， 设置一个差值 <input type="text" name="utc_offset" value="'. $utc_offset .'" size="3" /> 使时间显示为 ' . twitter_date('H:i') . '.<br />如果时间显示错误请调整该值。</label></p><hr />';
	//$content .= '<p><label><input type="checkbox" name="linktrans" value="yes" '. (setting_fetch('linktrans') == 'yes' ? ' checked="checked" ' : '') .' /> '.("显示链接地址为 [link]").'</label></p><hr />';
	//$content .= '<p>'.("短链接").':<br /><select name="short">'.theme('options', $short, setting_fetch('short', 'is.gd')).'</select></p><hr />';
	//if (LONG_URL == 1) {$content .= '<p><label><input type="checkbox" name="longurl" value="yes" '. (setting_fetch('longurl') == 'yes' ? ' checked="checked" ' : '') .' /> '.("还原所有短链为原始地址").'</label></p><hr />';}

	$content .= '<p><label>'.__("Custom CSS: ").'<br /><textarea name="css" cols="50" rows="3" id="css" style="width:95%">'.setting_fetch('css').'</textarea></label></p>';	
	// Allow users to choose a Dabr password if accounts are enabled
	if (MYSQL_USERS == 'ON' && user_is_authenticated()) {
		$content .= '<fieldset><legend>Dabr account</legend><small>If you want to sign in to Dabr without going via Weibo.com in the future, create a password and we\'ll remember you.</small></p><p>Change Dabr password<br /><input type="password" name="newpassword" /><br /><small>Leave blank if you don\'t want to change it</small></fieldset>';
	}
	
	$content .= '<hr /><p><input type="submit" name="Submit" value="'.__("Save").'" /> <small>'.__('Visit ').'<a href="'.BASE_URL.'reset">'.__("Reset").'</a>'.__(' if things go horribly wrong - it will clear all settings.').'</small></p></form>';

	return theme('page', __("Settings"), $content);
}
