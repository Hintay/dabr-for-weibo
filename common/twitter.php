<?php

menu_register(array(
  '' => array(
    'callback' => 'twitter_home_page',
    'accesskey' => '0',
  ),
  'status' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_status_page',
  ),
  'update' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_update',
  ),
  'twitter-retweet' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_retweet',
  ),
  'twitter-comment' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_comment',
  ),
  'replies' => array(
    'security' => true,
    'callback' => 'twitter_replies_page',
    'accesskey' => '1',
  ),
  'comments' => array(
    'security' => true,
    'callback' => 'twitter_comments_page',
    'accesskey' => '9',
  ),
  'favourite' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_mark_favourite_page',
  ),
  'unfavourite' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_mark_favourite_page',
  ),
  'directs' => array(
    'security' => true,
    'callback' => 'twitter_directs_page',
    'accesskey' => '2',
  ),
  'search' => array(
    'security' => true,
    'callback' => 'twitter_search_page',
    'accesskey' => '3',
  ),
  'public' => array(
    'security' => true,
    'callback' => 'twitter_public_page',
    'accesskey' => '4',
  ),
  'user' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_user_page',
  ),
  'follow' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_follow_page',
  ),
  'unfollow' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_follow_page',
  ),
  'confirm' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_confirmation_page',
  ),
  'block' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_block_page',
  ),
  'unblock' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_block_page',
  ),
  'spam' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_spam_page',
  ),
  'favourites' => array(
    'security' => true,
    'callback' =>  'twitter_favourites_page',
  ),
  'followers' => array(
    'security' => true,
    'callback' => 'twitter_followers_page',
  ),
  'friends' => array(
    'security' => true,
    'security' => true,
    'callback' => 'twitter_friends_page',
  ),
  	'blockings' => array(//黑名单
		'security' => true,
		'security' => true,
		'callback' => 'twitter_blockings_page',
		'title' => ("黑名单"),
	),
  'delete' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_delete_page',
  ),
  'retweet' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_retweet_page',
  ),
  'comment' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_comment_page',
  ),
  'flickr' => array(
    'security' => true,
    'hidden' => true,
    'callback' => 'generate_thumbnail',
  ),
  'moblog' => array(
    'security' => true,
    'hidden' => true,
    'callback' => 'generate_thumbnail',
  ),
  'hash' => array(
    'security' => true,
    'hidden' => true,
    'callback' => 'twitter_hashtag_page',
  ),
  'twitpic' => array(
    'security' => true,
    'callback' => 'twitter_twitpic_page',
  ),
    'trends' => array(
    'security' => true,
    'callback' => 'twitter_trends_page',
  ),
));

function long_url($shortURL)
{
	if (!defined('LONGURL_KEY'))
	{
		return $shortURL;
	}
	$url = "http://www.longurlplease.com/api/v1.1?q=" . $shortURL;
	$curl_handle=curl_init();
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl_handle,CURLOPT_URL,$url);
	$url_json = curl_exec($curl_handle);
	curl_close($curl_handle);

	$url_array = json_decode($url_json,true);
	
	$url_long = $url_array["$shortURL"];
	
	if ($url_long == null)
	{
		return $shortURL;
	}
	
	return $url_long;
}


function friendship_exists($user_a) {
  $request = 'http://twitter.com/friendships/show.json?target_screen_name=' . $user_a;
  $following = twitter_process($request);
  
  if ($following->relationship->target->following == 1) {
    return true;
  } else {
    return false;
  }
}

function twitter_block_exists($query) 
{
	//http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-blocks-blocking-ids
	//Get an array of all ids the authenticated user is blocking
	$request = 'http://twitter.com/blocks/blocking/ids.json';
	$blocked = (array) twitter_process($request);
	
	//bool in_array  ( mixed $needle  , array $haystack  [, bool $strict  ] )		
	//If the authenticate user has blocked $query it will appear in the array
	return in_array($query,$blocked);
}

function twitter_trends_page() {
theme('page', '话题', "本功能正在建设，请期待下一个版本的出现。");
}
/*function twitter_trends_page($query) 
{
  $trend_type = $query[1];
  if($trend_type == '') $trend_type = 'current';
  $request = 'http://twitter.com/trends/weekly.json';//. $trend_type . '.json'
  $trends = twitter_process($request);
  $search_url = 'search?query=';
  foreach($trends->trends as $temp) {
    foreach($temp as $trend) {
      $row = array('<strong><a href="' . $search_url . urlencode($trend->query) . '">' . $trend->name . '</a></strong>');
      $rows[] = $row;
    }
  }
  $headers = array('<p><a href="trends">Current</a> | <a href="trends/daily">Daily</a> | <a href="trends/weekly">Weekly</a></p>'); //output for daily and weekly not great at the moment
  $headers = array();
  $content = theme('table', $headers, $rows, array('class' => 'timeline'));
  theme('page', '话题', $content);
}*/

function js_counter($name)
{
  $script = '<script type="text/javascript">
  function updateCount()
  {
    document.getElementById("remaining").innerHTML = 140 -
document.getElementById("' . $name . '").value.length;
    setTimeout(updateCount, 400);
  }
  updateCount();</script>';
  return $script;
}

function twitter_twitpic_page($query) {
  if (user_type() == 'oauth') {
    return theme('page', 'Error', '<p>You can\'t use Twitpic uploads while accessing Dabr using an OAuth login.</p>');
  }
  if ($_POST['message']) {
    $response = twitter_process('http://twitpic.com/api/uploadAndPost', array(
      'media' => '@'.$_FILES['media']['tmp_name'],
      'message' => stripslashes($_POST['message']),
      'username' => user_current_username(),
      'password' => $GLOBALS['user']['password'],
    ));
    if (preg_match('#mediaid>(.*)</mediaid#', $response, $matches)) {
      $id = $matches[1];
      twitter_refresh("twitpic/confirm/$id");
    } else {
      twitter_refresh('twitpic/fail');
    }
  } elseif ($query[1] == 'confirm') {
    $content = "<p>Upload success.</p><p><img src='http://twitpic.com/show/thumb/{$query[2]}' alt='' /></p>";
  } elseif ($query[1] == 'fail') {
    $content = '<p>Twitpic upload failed. No idea why!</p>';
  } else {
    $content = '<form method="post" action="twitpic" enctype="multipart/form-data">Image <input type="file" name="media" /><br />Message: <input type="text" name="message" maxlength="120" /><br /><input type="submit" value="Upload" /></form>';
  }
  return theme('page', 'Twitpic Upload', $content);
}

function endsWith( $str, $sub ) {
  return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

function twitter_process($url, $post_data = false) {
  if ($post_data === true) $post_data = array();
  if (user_type() == 'oauth' && strpos($url, '/twitter.com') !== false) {
    user_oauth_sign($url, $post_data);
  } elseif (strpos($url, 'twitter.com') !== false && is_array($post_data)) {
    // Passing $post_data as an array to twitter.com (non-oauth) causes an error :(
    $s = array();
    foreach ($post_data as $name => $value)
      $s[] = $name.'='.urlencode($value);
    $post_data = implode('&', $s);
  }
  //if ($post_data == false) {
    if (endsWith($url, ".json") || endsWith($url, ".xml"))
        $url = $url . "?";
    else
        $url = $url . "&";
    $url = $url . "source=".SINA_SOURCE."";
  //}
  $url = str_replace("http://twitter.com/", "http://api.t.sina.com.cn/", $url);

  $ch = curl_init($url);

  if($post_data !== false && !$_GET['page']) {
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
  }

  if (user_type() != 'oauth' && user_is_authenticated())
    curl_setopt($ch, CURLOPT_USERPWD, user_current_username().':'.$GLOBALS['user']['password']);

  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_USERAGENT, 'dabr');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

  $response = curl_exec($ch);
  $response_info=curl_getinfo($ch);
  curl_close($ch);

  switch( intval( $response_info['http_code'] ) ) {
    case 200:
      $json = json_decode($response);
      if ($json) return $json;
      return $response;
    case 403:
      user_logout();
      theme('error', '<p>账号或者密码不正确。<a href="'.BASE_URL.'">重新登录</a></p>');
    case 0:
      theme('error', '<h2>连接超时</h3><p>请稍等几分钟后刷新重新连接。</p>');
    default:
      $result = json_decode($response);
      $result = $result->error ? $result->error : $response;
      if (strlen($result) > 500) $result = '出现错误。';
      theme('error', "<h2>在调用API时遇到错误。</h2><p>{$response_info['http_code']}: {$result}</p><hr><p>$url</p>");
  }
}

function twitter_url_shorten($text) {
  return preg_replace_callback('#((\w+://|www)[\w\#$%&~/.\-;:=,?@\[\]+]{33,1950})(?<![.,])#is', 'twitter_url_shorten_callback', $text);
}

function twitter_url_shorten_callback($match) {
  if (preg_match('#http://www.flickr.com/photos/[^/]+/(\d+)/#', $match[0], $matches)) {
    return 'http://flic.kr/p/'.flickr_encode($matches[1]);
  }
  if (!defined('BITLY_API_KEY')) return $match[0];
  $request = 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.urlencode($match[0]).'&login='.BITLY_LOGIN.'&apiKey='.BITLY_API_KEY;
  $json = json_decode(twitter_fetch($request));
  if ($json->errorCode == 0) {
    $results = (array) $json->results;
    $result = array_pop($results);
    return $result->shortUrl;
  } else {
    return $match[0];
  }
}

function twitter_fetch($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}

function twitter_parse_links_callback($matches) {
  $url = $matches[1];
  if (substr($url, 0, strlen(BASE_URL)) == BASE_URL) return "<a href='$url'>$url</a>";
  if (setting_fetch('gwt') == 'on') {
    $encoded = urlencode($url);
    return "<a href='http://google.com/gwt/n?u={$encoded}' target='_blank'>{$url}</a>";
  } else {
    return theme('external_link', $url);
  }
}

/*function twitter_parse_tags($input) {
  $out = preg_replace_callback('#(\w+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)(?<![.,])#is', 'twitter_parse_links_callback', $input);
  $out = preg_replace('#(^|\s)@([a-z_A-Z0-9]+)/([\w\d-]+)#', '$1@<a href="user/$2">$2</a>/<a href="lists/$2/$3">$3</a>', $out);
  $out = preg_replace('#(^|\s)@([a-z_A-Z0-9]+)#', '$1@<a href="user/$2">$2</a>', $out);
  $out = preg_replace('#(^|\s)(\\#([a-z_A-Z0-9:_-]+))#', '$1<a href="hash/$3">$2</a>', $out);
  if (!in_array(setting_fetch('browser'), array('text', 'worksafe'))) {
    $out = twitter_photo_replace($out);
  }
  return $out;
}*/
function twitter_parse_tags($input) {
	$out = preg_replace_callback('#(\w+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)(?<![.,])#is', 'twitter_parse_links_callback', $input);
	$out = preg_replace('#(^|\s)@([a-z_A-Z0-9]+)/([\w\d-]+)#', '$1@<a href="user/$2">$2</a>/<a href="lists/$2/$3">$3</a>', $out);
	$out = preg_replace('#(^|\s)@([a-z_A-Z0-9]+)#', '$1@<a href="user/$2">$2</a>', $out);
	$out = preg_replace('#(^|\s)(\\#([a-z_A-Z0-9:_-]+))#', '$1<a href="hash/$3">$2</a>', $out);
	if (setting_fetch('showthumbs', 'yes') == 'yes') {
		$out = twitter_photo_replace($out);
	}
	return $out;
}

function flickr_decode($num) {
  $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
  $decoded = 0;
  $multi = 1;
  while (strlen($num) > 0) {
    $digit = $num[strlen($num)-1];
    $decoded += $multi * strpos($alphabet, $digit);
    $multi = $multi * strlen($alphabet);
    $num = substr($num, 0, -1);
  }
  return $decoded;
}

function flickr_encode($num) {
  $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
  $base_count = strlen($alphabet);
  $encoded = '';
  while ($num >= $base_count) {
    $div = $num/$base_count;
    $mod = ($num-($base_count*intval($div)));
    $encoded = $alphabet[$mod] . $encoded;
    $num = intval($div);
  }
  if ($num) $encoded = $alphabet[$num] . $encoded;
  return $encoded;
}

function twitter_photo_replace($text) {
  $images = array();
  $tmp = strip_tags($text);
  
  // List of supported services. Array format: pattern => thumbnail url
  $services = array(
    '#youtube\.com\/watch\?v=([_-\d\w]+)#i' => 'http://i.ytimg.com/vi/%s/1.jpg',
    '#twitpic.com/([\d\w]+)#i' => 'http://twitpic.com/show/thumb/%s',
    '#twitgoo.com/([\d\w]+)#i' => 'http://twitgoo.com/show/thumb/%s',
    '#yfrog.com/([\w\d]+)#i' => 'http://yfrog.com/%s.th.jpg',
    '#moblog.net/view/([\d]+)/#' => 'moblog/%s',
    '#hellotxt.com/i/([\d\w]+)#i' => 'http://hellotxt.com/image/%s.s.jpg',
    '#ts1.in/(\d+)#i' => 'http://ts1.in/mini/%s',
    '#moby.to/\??([\w\d]+)#i' => 'http://moby.to/%s:square',
    '#mobypicture.com/\?([\w\d]+)#i' => 'http://mobypicture.com/?%s:square',
    '#twic.li/([\w\d]{2,7})#' => 'http://twic.li/api/photo.jpg?id=%s&size=small',
  );
  
  // Only enable Flickr service if API key is available
  if (defined('FLICKR_API_KEY')) {
    $services['#flickr.com/[^ ]+/([\d]+)#i'] = 'flickr/%s';
    $services['#flic.kr/p/([\w\d]+)#i'] = 'flickr/%s';
  }
  
  // Loop through each service and show images for matching URLs
  foreach ($services as $pattern => $thumbnail_url) {
    if (preg_match_all($pattern, $tmp, $matches, PREG_PATTERN_ORDER) > 0) {
      foreach ($matches[1] as $key => $match) {
        $images[] = theme('external_link', 'http://'.$matches[0][$key], '<img src="'.sprintf($thumbnail_url, $match).'" />');
      }
    }
  }
 
  // Twitxr is handled differently because of their folder structure
  if (preg_match_all('#twitxr.com/[^ ]+/updates/([\d]+)#', $tmp, $matches, PREG_PATTERN_ORDER) > 0) {
    foreach ($matches[1] as $key => $match) {
      $thumb = 'http://twitxr.com/thumbnails/'.substr($match, -2).'/'.$match.'_th.jpg';
      $images[] = theme('external_link', "http://{$matches[0][$key]}", "<img src='$thumb' />");
    }
  }
  
  if (empty($images)) return $text;
  return implode('<br />', $images).'<br />'.$text;
}

function generate_thumbnail($query) {
  $id = $query[1];
  if ($id) {
    header('HTTP/1.1 301 Moved Permanently');
    if ($query[0] == 'flickr') {
      if (!is_numeric($id)) $id = flickr_decode($id);
      $url = "http://api.flickr.com/services/rest/?method=flickr.photos.getSizes&photo_id=$id&api_key=".FLICKR_API_KEY;
      $flickr_xml = twitter_fetch($url);
      if (setting_fetch('browser') == 'mobile') {
        $pattern = '#"(http://.*_t\.jpg)"#';
      } else {
        $pattern = '#"(http://.*_m\.jpg)"#';
      }
      preg_match($pattern, $flickr_xml, $matches);
      header('Location: '. $matches[1]);
    }
    if ($query[0] == 'moblog') {
      $url = "http://moblog.net/view/{$id}/";
      $html = twitter_fetch($url);
      if (preg_match('#"(/media/[a-zA-Z0-9]/[^"]+)"#', $html, $matches)) {
        $thumb = 'http://moblog.net' . str_replace(array('.j', '.J'), array('_tn.j', '_tn.J'), $matches[1]);
        $pos = strrpos($thumb, '/');
        $thumb = substr($thumb, 0, $pos) . '/thumbs' . substr($thumb, $pos);
      }
      header('Location: '. $thumb);
    }
  }
  exit();
}

function format_interval($timestamp, $granularity = 2) {
  $units = array(
    '年' => 31536000,
    '天' => 86400,
    '小时' => 3600,
    '分钟' => 60,
    '秒' => 1,
  );
  $output = '';
  foreach ($units as $key => $value) {
    if ($timestamp >= $value) {
      $output .= ($output ? ' ' : '').floor($timestamp / $value).' '.$key;
      $timestamp %= $value;
      $granularity--;
    }
    if ($granularity == 0) {
      break;
    }
  }
  return $output ? $output : '0 分钟';
}

function twitter_status_page($query) {
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = "http://twitter.com/statuses/show/{$id}.json";
    $status = twitter_process($request);
    $content = theme('status', $status);
    if (!$status->user->protected) {
      $thread = twitter_thread_timeline($id);
    }
    if ($thread) {
      $content .= '<p>以及实验对话模式</p>'.theme('timeline', $thread);
      $content .= "<p>不喜欢对话模式的顺序?请到<a href='/settings'>设置</a>里反转.但貌似日期/时间总是有问题的.</p>";
    }
    theme('page', "消息 $id", $content);
  }
}

function twitter_thread_timeline($thread_id) {
  $request = "http://search.twitter.com/search/thread/{$thread_id}";
  $tl = twitter_standard_timeline(twitter_fetch($request), 'thread');
  return $tl;
}

function twitter_retweet_page($query) {
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = "http://twitter.com/statuses/show/{$id}.json";
    $tl = twitter_process($request);
    $content = theme('retweet', $tl);
    theme('page', 'Retweet', $content);
  }
}

function twitter_comment_page($query) {
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = "http://twitter.com/statuses/show/{$id}.json";
    $tl = twitter_process($request);
    $content = theme('comment', $tl);
    theme('page', '评论', $content);
  }
}

function twitter_refresh($page = NULL) {
  if (isset($page)) {
    $page = BASE_URL . $page;
  } else {
    $page = $_SERVER['HTTP_REFERER'];
  }
  header('Location: '. $page);
  exit();
}

function twitter_delete_page($query) {
  twitter_ensure_post_action();
  
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = "http://twitter.com/statuses/destroy/{$id}.json?page=".intval($_GET['page']);
    $tl = twitter_process($request, true);
    twitter_refresh('user/'.user_current_username());
  }
}

function twitter_ensure_post_action() {
  // This function is used to make sure the user submitted their action as an HTTP POST request
  // It slightly increases security for actions such as Delete, Block and Spam
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Error: Invalid HTTP request method for this action.');
  }
}

function twitter_follow_page($query) {
  $user = $query[1];
  if ($user) {
    if($query[0] == 'follow'){
      $request = "http://twitter.com/friendships/create/{$user}.json";
    } else {
      $request = "http://twitter.com/friendships/destroy/{$user}.json";
    }
    twitter_process($request, true);
    twitter_refresh('friends');
  }
}

function twitter_block_page($query) {
  twitter_ensure_post_action();
  $user = $query[1];
  if ($user) {
    if($query[0] == 'block'){
      $request = "http://twitter.com/blocks/create/{$user}.json";
    } else {
      $request = "http://twitter.com/blocks/destroy/{$user}.json";
    }
    twitter_process($request, true);
    twitter_refresh("user/{$user}");
  }
}

function twitter_spam_page($query) 
{
	//http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-report_spam
	//We need to post this data
	twitter_ensure_post_action();
	$user = $query[1];

	//The data we need to post
	$post_data = array("screen_name" => $user);

	$request = "http://twitter.com/report_spam.json";
	twitter_process($request, $post_data);

	//Where should we return the user to?  Back to the user
	twitter_refresh("user/{$user}");
}


function twitter_confirmation_page($query) 
{
	// the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
	$action = $query[1];
	$target = $query[2];	//The name of the user we are doing this action on
	$target_id = $query[3];	//The targets's ID.  Needed to check if they are being blocked.

  switch ($action) {
    case 'block':
      if (twitter_block_exists($target_id)) //Is the target blocked by the user?
      {
        $action = 'unblock';
        $content  = "<p>你确定你要 <strong>取消屏蔽 $target</strong>?</p>";
        $content .= '<ul><li>如果对方重新关注你,那么你的消息会显示在他们的首页上.</li><li>你<em>可以</em>随时重新屏蔽对方.</li></ul>';		
      }
      else
      {
        $content = "<p>你确定你要 <strong>$action $target</strong>?</p>";
        $content .= "<ul><li>你将不会出现在他们的好友列表中.</li><li>他们将无法看到你的消息.</li><li>他们将无法关注你.</li><li>你<em>可以</em>取消屏蔽他们但你需要重新关注他们.</li></ul>";
      }
      break;
    
    case 'delete':
      $content = '<p>你确定要删除这条微博？</p>';
      $content .= "<ul><li>微博ID: <strong>$target</strong></li><li>删除之后将无法恢复。</li></ul>";
      break;

    case 'spam':
      $content  = "<p>你真的想要举报 <strong>$target</strong> 吗?</p>";
      $content .= "<p>他们将不能关注你。</p>";
      break;

  }    
  $content .= "<form action='$action/$target' method='post'>
						<input type='submit' value='确定' />
					</form>";
  theme('Page', '你确定吗', $content);
}

function twitter_friends_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = user_current_username();
  }
  $request = "http://twitter.com/statuses/friends.json?page=".intval($_GET['page']);
  //$request = "http://twitter.com/statuses/friends/{$user}.json?page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $content = theme('followers', $tl);
  theme('page', '关注', $content);
}

function twitter_followers_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = user_current_username();
  }
  $request ="http://twitter.com/statuses/followers.json?&page=".intval($_GET['page']);
  //$request ="http://twitter.com/statuses/followers/{$user}.json?page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $content = theme('followers', $tl);
  theme('page', '粉丝', $content);
}

function twitter_blockings_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = user_current_username();
  }
  $request ="http://twitter.com/blocks/blocking.json?&page=".intval($_GET['page']);
  //$request ="http://twitter.com/statuses/followers/{$user}.json?page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $content = theme('followers', $tl);
  theme('page', '黑名单', $content);
}

/*function twitter_update() {
  twitter_ensure_post_action();
  $status = twitter_url_shorten(stripslashes(trim($_POST['status'])));
  if ($status) {
    $request = 'http://twitter.com/statuses/update.json';
    $post_data = array('source' => ''.SINA_SOURCE.'', 'status' => $status);
    $in_reply_to_id = (string) $_POST['in_reply_to_id'];
    if (is_numeric($in_reply_to_id)) {
      $post_data['in_reply_to_status_id'] = $in_reply_to_id;
    }
    $b = twitter_process($request, $post_data);
  }
  twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}*/

function twitter_update() {
	twitter_ensure_post_action();
	$status = twitter_url_shorten(stripslashes(trim($_POST['status'])));
	if ($status) {
		$status = setting_fetch('fixedtagspre')." ".$status." ".setting_fetch('fixedtagspost');
		//if (function_exists('mb_strlen')) $status = sysSubStr($status, 140, true);
		$request = 'http://twitter.com/statuses/update.json';
		$post_data = array('source' => 'dabr', 'status' => $status);
		$in_reply_to_id = (string) $_POST['in_reply_to_id'];
		if (is_numeric($in_reply_to_id)) {
			$post_data['in_reply_to_status_id'] = $in_reply_to_id;
		}
		$b = twitter_process($request, $post_data);
	}
	twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_retweet($query) {
  twitter_ensure_post_action();
  $id = $query[1];
  $status = twitter_url_shorten(stripslashes(trim($_POST['status'])));
  if (is_numeric($id)) {
    $request = 'http://twitter.com/statuses/repost.json';
	$post_data = array('source' => ''.SINA_SOURCE.'', 'status' => $status, 'id' => $id);
    $b = twitter_process($request, $post_data);
  }
  twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_comment($query) {
  twitter_ensure_post_action();
  $comment = twitter_url_shorten(stripslashes(trim($_POST['comment'])));
  // $id = $query[1];
  $id = $_POST['id'];
  if (is_numeric($id)) {
    $request = 'http://twitter.com/statuses/comment.json';
    $post_data = array('source' => ''.SINA_SOURCE.'', 'comment' => $comment, 'id' => $id);
    $b = twitter_process($request, $post_data);
  }
  twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_public_page() {
  $count = setting_fetch('tpp', 20); 
  $request = "http://twitter.com/statuses/public_timeline.json?count=$count&page=".intval($_GET['page']);
  $content = theme('status_form');
  $tl = twitter_standard_timeline(twitter_process($request), 'public');
  $content .= theme('timeline', $tl);
  theme('page', '随便看看', $content);
}

function twitter_replies_page() {
  $count = setting_fetch('tpp', 20);
  $request = "http://twitter.com/statuses/mentions.json?count=$count&page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'replies');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', '@我的', $content);
}

function twitter_comments_page() {
  $count = setting_fetch('tpp', 20);
  $request = "http://twitter.com/statuses/comments_timeline.json?count=$count&page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'replies');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', '评论', $content);
}

function twitter_directs_page($query) {
  $action = strtolower(trim($query[1]));
  switch ($action) {
    case 'delete':
      $id = $query[2];
      if (!is_numeric($id)) return;
      $request = "http://twitter.com/direct_messages/destroy/$id.json";
      twitter_process($request, true);
      twitter_refresh();
      
    case 'create':
      $to = $query[2];
      $content = theme('directs_form', $to);
      theme('page', '新私信', $content);
    
    case 'send':
      twitter_ensure_post_action();
      $to = trim(stripslashes($_POST['to']));
      $message = trim(stripslashes($_POST['message']));
      $request = 'http://twitter.com/direct_messages/new.json';
      twitter_process($request, array('user' => $to, 'text' => $message));
      twitter_refresh('directs/sent');
    
    case 'sent':
      $request = 'http://twitter.com/direct_messages/sent.json?page='.intval($_GET['page']);
      $tl = twitter_standard_timeline(twitter_process($request), 'directs_sent');
      $content = theme_directs_menu();
      $content .= theme('timeline', $tl);
      theme('page', '已发送', $content);

    case 'inbox':
    default:
      $request = 'http://twitter.com/direct_messages.json?page='.intval($_GET['page']);
      $tl = twitter_standard_timeline(twitter_process($request), 'directs_inbox');
      $content = theme_directs_menu();
      $content .= theme('timeline', $tl);
      theme('page', '收件箱', $content);
  }
}

function theme_directs_menu() {
  return '<p><a href="directs/create">新私信</a> | <a href="directs/inbox">收件箱</a> | <a href="directs/sent">已发送</a></p>';
}

function theme_directs_form($to) {
  if ($to) {
	
	if (friendship_exists($to) != 1)
	{
		$html_to = "<em>Warning</em> <b>" . $to . "</b> 没有关注你 你不能发私信给 TA :-(<br/>";
	}
    $html_to .= "发私信给 <b>$to</b><input name='to' value='$to' type='hidden'>";
  } else {
    $html_to .= "收信人: <input name='to'><br />消息:";
  }
   $content = "<form action='directs/send' method='post'>$html_to<br><textarea name='message' cols='50' rows='3' id='message'></textarea><br><input type='submit' value='发送'><span id='remaining'>140</span></form>";
   $content .= js_counter("message");
   return $content;
}

function twitter_search_page() {
  $search_query = $_GET['query'];
  $content = theme('search_form', $search_query);
  if (isset($_POST['query'])) {
    $duration = time() + (3600 * 24 * 365);
    setcookie('search_favourite', $_POST['query'], $duration, '/');
    twitter_refresh('search');
  }
  if (!isset($search_query) && array_key_exists('search_favourite', $_COOKIE)) {
    $search_query = $_COOKIE['search_favourite'];
  }
  if ($search_query) {
    $tl = twitter_search($search_query);
    if ($search_query !== $_COOKIE['search_favourite']) {
      $content .= '<form action="search/bookmark" method="post"><input type="hidden" name="query" value="'.$search_query.'" /><input type="submit" value="Save as default search" /></form>';
    }
    $content .= theme('timeline', $tl);
  }
  theme('page', '搜索', $content);
}

function twitter_search($search_query) {
  $page = (int) $_GET['page'];
  if ($page == 0) $page = 1;
  $request = 'http://search.twitter.com/search.json?q=' . urlencode($search_query).'&page='.$page;
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl->results, 'search');
  return $tl;
}

function twitter_user_page($query) {
  $screen_name = $query[1];
  if ($screen_name) {
    $content = '';
    if ($query[2] == 'reply') {
      $in_reply_to_id = (string) $query[3];
      if (is_numeric($in_reply_to_id)) {
        $content .= "<p>In reply to tweet ID $in_reply_to_id...</p>";
      }
    } else {
      $in_reply_to_id = 0;
    }
    $user = twitter_user_info($screen_name);
    if (!user_is_current_user($user->screen_name)) {
      $status = "@{$user->screen_name} ";
    } else {
      $status = '';
    }
    $content .= theme('status_form', $status, $in_reply_to_id);
    $content .= theme('user_header', $user);
    
    if (isset($user->status)) {
      $request = "http://twitter.com/statuses/user_timeline.json?screen_name={$screen_name}&page=".intval($_GET['page']);
      $tl = twitter_process($request);
      $tl = twitter_standard_timeline($tl, 'user');
      $content .= theme('timeline', $tl);
    }
    theme('page', "用户 {$screen_name}", $content);
  } else {
    // TODO: user search screen
  }
}

function twitter_favourites_page($query) {
  $screen_name = $query[1];
  if (!$screen_name) {
    user_ensure_authenticated();
    $screen_name = user_current_username();
  }
  //$request = "http://twitter.com/favorites/{$screen_name}.json?page=".intval($_GET['page']);
  $request = "http://twitter.com/favorites.json?page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'favourites');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', '收藏', $content);
}

function twitter_mark_favourite_page($query) {
  $id = (string) $query[1];
  if (!is_numeric($id)) return;
  if ($query[0] == 'unfavourite') {
    $request = "http://twitter.com/favorites/destroy/$id.json";
  } else {
    $request = "http://twitter.com/favorites/create/$id.json";
  }
  twitter_process($request, true);
  twitter_refresh();
}

function twitter_home_page() {
  user_ensure_authenticated();
  $count = setting_fetch('tpp');
  $request = "http://twitter.com/statuses/friends_timeline.json?&count=$count&page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'friends');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', '首页', $content);
}

function twitter_hashtag_page($query) {
  if (isset($query[1])) {
    $hashtag = '#'.$query[1];
    $content = theme('status_form', $hashtag.' ');
    $tl = twitter_search($hashtag);
    $content .= theme('timeline', $tl);
    theme('page', $hashtag, $content);
  } else {
    theme('page', 'Hashtag', 'Hash hash!');
  }
}

function theme_status_form($text = '', $in_reply_to_id = NULL) {
  if (user_is_authenticated()) {
    return "<form method='post' action='update'><input name='status' value='{$text}' maxlength='140' /> <input name='in_reply_to_id' value='{$in_reply_to_id}' type='hidden' /><input type='submit' value='发送'></form>";
  }
}

function theme_status($status) {
  $time_since = theme('status_time_link', $status);
  $parsed = twitter_parse_tags($status->text);
  $avatar = theme('avatar', $status->user->profile_image_url, 1);

  $out = theme('status_form', "@{$status->user->screen_name} ");
  $out .= "<p>$parsed</p>
<table align='center'><tr><td>$avatar</td><td><a href='user/{$status->user->screen_name}'>{$status->user->screen_name}</a>
<br />$time_since</td></tr></table>";
  if (user_is_current_user($status->user->screen_name)) {
    $out .= "<form action='delete/{$status->id}' method='post'><input type='submit' value='Delete without confirmation' /></form>";
  }
  return $out;
}

function theme_retweet($status) {
  $text = "";
  if($status->retweeted_status)
	{
	 $text = "//"."@{$status->retweeted_status->user->screen_name}".":".twitter_parse_tags($status->text);
	}
  $length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen($text);
  $from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));
  /*$content = "<p>Old style \"organic\" retweet:</p><form action='update' method='post'><input type='hidden' name='from' value='$from' /><textarea name='status' cols='50' rows='3' id='status'>$text</textarea><br><input type='submit' value='Retweet'><span id='remaining'>" . (140 - $length) ."</span></form>";*/
  $content .= js_counter("status");  
	    $content.="<br /> 转发理由，不填写则默认为“转发微博”<br /><form action='twitter-retweet/{$status->id}' method='post'><input type='hidden' name='from' value='$from' /><textarea name='status' cols='50' rows='3' id='status'>$text</textarea><br><input type='submit' value='转发'></form>";
  return $content;
}

function theme_comment($status) {
  $text = "";
  $length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen($text);
  $from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));
  $content = "<form action='twitter-comment/{$status->id}' method='post'><input type='hidden' name='id' value='$status->id' /><input type='hidden' name='from' value='$from' /><textarea name='comment' cols='50' rows='3' id='comment'>$text</textarea><br><input type='submit' value='评论'></form>";
  $content .= js_counter("status");
        /*if($status->user->protected == 0){
    $content.="<br />Or Twitter's new style retweets<br /><form action='twitter-retweet/{$status->id}' method='post'><input type='hidden' name='from' value='$from' /><input type='submit' value='Twitter Retweet'></form>";
  }*/
  return $content;
}

function twitter_tweets_per_day($user, $rounding = 1) {
  // Helper function to calculate an average count of tweets per day
  $days_on_twitter = (time() - strtotime($user->created_at)) / 86400;
  return round($user->statuses_count / $days_on_twitter, $rounding);
}

function theme_user_header($user) {
  $name = theme('full_name', $user);
  $full_avatar = str_replace('_normal.', '.', $user->profile_image_url);
  $link = theme('external_link', $user->url);
  $raw_date_joined = strtotime($user->created_at);
  $date_joined = date('Y-m-d', $raw_date_joined);
  $tweets_per_day = twitter_tweets_per_day($user, 1);
  $out = "<table><tr><td>".theme('external_link', $full_avatar, theme('avatar', $user->profile_image_url, 1))."</td>
<td><b>{$name}</b>
<small>";
  if ($user->verified == true) {
    $out .= '<strong>新浪认证</strong>';
  }
  $out .= "
<br />个人简介：{$user->description}
<br />博客地址：{$link}
<br />所在位置：{$user->location}
<br />于{$date_joined}注册，每天约{$tweets_per_day}条微博。
</small>
<br />
{$user->statuses_count}微博|
<a href='followers/{$user->screen_name}'>{$user->followers_count}粉丝</a> |
<a href='friends/{$user->screen_name}'>{$user->friends_count}关注</a> ";
  if ($user->following !== true) {
    $out .= "| <a href='follow/{$user->screen_name}'>加关注</a>";
  } else {
    $out .= " | <a href='unfollow/{$user->screen_name}'>取消关注</a>";
  }
  	//We need to pass the User Name and the User ID.  The Name is presented in the UI, the ID is used in checking
	$out.= " | <a href='confirm/block/{$user->screen_name}/{$user->id}'>黑名单</a>";
	$out .= " | <a href='confirm/spam/{$user->screen_name}/{$user->id}'>举报</a>";
  $out.= " | <a href='favourites/{$user->screen_name}'>{$user->favourites_count}条收藏</a>
| <a href='directs/create/{$user->screen_name}'>发私信</a>
</td></table>";
  return $out;
}

/*function theme_avatar($url, $force_large = false) {
  $size = $force_large ? 48 : 24;
  return "<img src='$url' height='$size' width='$size' />";
}*/
function theme_avatar($url, $force_large = false) {
	$size = $force_large ? 48 : 24;
	if (setting_fetch('avataro', 'yes') !== 'yes') {
		return "<img class='shead' src='$url' height='$size' width='$size' />";
	} else {
		return '';
	}
}

function theme_status_time_link($status, $is_link = true) {
  $time = strtotime($status->created_at);
  if ($time > 0) {
    if (twitter_date('dmy') == twitter_date('dmy', $time)) {
      $out = format_interval(time() - $time, 1). '前';
    } else {
      $out = twitter_date('H:i', $time);
    }
  } else {
    $out = $status->created_at;
  }
  if ($is_link)
    $out = "$out";
  return "<small>$out</small>";
}

function twitter_date($format, $timestamp = null) {
  static $offset;
  if (!isset($offset)) {
    if (user_is_authenticated()) {
      if (array_key_exists('utc_offset', $_COOKIE)) {
        $offset = $_COOKIE['utc_offset'];
      } else {
        $user = twitter_user_info();
        $offset = $user->utc_offset;
        setcookie('utc_offset', $offset, time() + 3000000, '/');
      }
    } else {
      $offset = 0;
    }
  }
  if (!isset($timestamp)) {
    $timestamp = time();
  }
  return gmdate($format, $timestamp + $offset);
}

function twitter_standard_timeline($feed, $source) {
  $output = array();
  if (!is_array($feed) && $source != 'thread') return $output;
  switch ($source) {
    case 'favourites':
    case 'friends':
    case 'public':
    case 'replies':
    case 'user':
      foreach ($feed as $status) {
        $new = $status;
        $new->from = $new->user;
        unset($new->user);
        $output[(string) $new->id] = $new;
      }
      return $output;
    
    case 'search':
      foreach ($feed as $status) {
        $output[(string) $status->id] = (object) array(
          'id' => $status->id,
          'text' => $status->text,
          'source' => strpos($status->source, '&lt;') !== false ? html_entity_decode($status->source) : $status->source,
          'from' => (object) array(
            'id' => $status->from_user_id,
            'screen_name' => $status->from_user,
            'profile_image_url' => $status->profile_image_url,
          ),
          'to' => (object) array(
            'id' => $status->to_user_id,
            'screen_name' => $status->to_user,
          ),
          'created_at' => $status->created_at,
        );
      }
      return $output;
    
    case 'directs_sent':
    case 'directs_inbox':
      foreach ($feed as $status) {
        $new = $status;
        if ($source == 'directs_inbox') {
          $new->from = $new->sender;
          $new->to = $new->recipient;
        } else {
          $new->from = $new->recipient;
          $new->to = $new->sender;
        }
        unset($new->sender, $new->recipient);
        $new->is_direct = true;
        $output[] = $new;
      }
      return $output;
    
    case 'thread':
      // First pass: extract tweet info from the HTML
      $html_tweets = explode('</li>', $feed);
      foreach ($html_tweets as $tweet) {
        $id = preg_match_one('#msgtxt(\d*)#', $tweet);
        if (!$id) continue;
        $output[$id] = (object) array(
          'id' => $id,
          'text' => strip_tags(preg_match_one('#</a>: (.*)</span>#', $tweet)),
          'source' => preg_match_one('#>from (.*)</span>#', $tweet),
          'from' => (object) array(
            'id' => preg_match_one('#profile_images/(\d*)#', $tweet),
            'screen_name' => preg_match_one('#twitter.com/([^"]+)#', $tweet),
            'profile_image_url' => preg_match_one('#src="([^"]*)"#' , $tweet),
          ),
          'to' => (object) array(
            'screen_name' => preg_match_one('#@([^<]+)#', $tweet),
          ),
          'created_at' => str_replace('about', '', preg_match_one('#info">\s(.*)#', $tweet)),
        );
      }
      // Second pass: OPTIONALLY attempt to reverse the order of tweets
      if (setting_fetch('reverse') == 'yes') {
        $first = false;
        foreach ($output as $id => $tweet) {
          $date_string = str_replace('later', '', $tweet->created_at);
          if ($first) {
            $attempt = strtotime("+$date_string");
            if ($attempt == 0) $attempt = time();
            $previous = $current = $attempt - time() + $previous;
          } else {
            $previous = $current = $first = strtotime($date_string);
          }
          $output[$id]->created_at = date('r', $current);
        }
        $output = array_reverse($output);
      }
      return $output;

    default:
      echo "<h1>$source</h1><pre>";
      print_r($feed); die();
  }
}

function preg_match_one($pattern, $subject, $flags = NULL) {
  preg_match($pattern, $subject, $matches, $flags);
  return trim($matches[1]);
}

function twitter_user_info($username = null) {
  if (!$username) {
    //error_log("twitter_user_info");
//	 debug_print_backtrace  ();
return null;//    $username = user_current_username();
  }
 
//  $username_e = urlencode($username); 
  $request = "http://twitter.com/users/show.json?screen_name=$username";
  $user = twitter_process($request);
  return $user;
}

function theme_timeline($feed) {
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
          'data' => "<small><b>$date</b></small>",
          'colspan' => 2
        ));
      }
    } else {
      $date = $status->created_at;
    }
    if ($status->in_reply_to_status_id) {
      $source .= " in reply to <a href='status/{$status->in_reply_to_status_id}'>{$status->in_reply_to_screen_name}</a>";
    }
    if($status->retweeted_status){
      $avatar = theme('avatar', $status->from->profile_image_url);
     if (setting_fetch('buttonfrom', 'yes') == 'yes') {
		if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
			$source = $status->source ? "来自 {$status->source}" : '';
		}else{
			$source = $status->source ? " 来自 ".strip_tags($status->source) ."" : '';
		}
	} else {
		$source = NULL;
	}
	  //$source = $status->source ? " 来自{$status->source}" : '';
      $text = twitter_parse_tags($status->retweeted_status->text);
	  if (setting_fetch('buttontime', 'yes') == 'yes') {
	     $link = theme('status_time_link', $status, !$status->is_direct);
	  }
	  $actions = theme('action_icons', $status);
	  $zhuanfa = twitter_parse_tags($status->text);
	  //if ($status->retweeted_status)
       //$zhuanfa .= $status->text;
      $row = array(
       // "<b><a href='user/{$status->retweeted_status->user->screen_name}'>{$status->retweeted_status->user->screen_name}</a></b> $link<br />{$text} <small>$source</small><br /><small>由<a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a>转发  $actions</small>",
		"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link<small>$source</small><br />{$zhuanfa}<br /><small> <a href='user/{$status->retweeted_status->user->screen_name}'>{$status->retweeted_status->user->screen_name}</a>：{$text}</small> ",
	  ); 
    }
    else{
      $text = twitter_parse_tags($status->text);
      if ($status->thumbnail_pic)
        $text .= "<br/> <a href='$status->original_pic' target=_blank><img src='$status->thumbnail_pic' /></a> <br />";
      if (setting_fetch('buttontime', 'yes') == 'yes') {
	    $link = theme('status_time_link', $status, !$status->is_direct);
	  }
      $actions = theme('action_icons', $status);
      $avatar = theme('avatar', $status->from->profile_image_url);
       if (setting_fetch('buttonfrom', 'yes') == 'yes') {
	   if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
			$source = $status->source ? "来自 {$status->source}" : '';
		 }else{
			$source = $status->source ? " 来自 ".strip_tags($status->source) ."" : '';
		 }
	     } else {
		$source = NULL;
	   }
	  //$source = $status->source ? " 来自{$status->source}" : '';
      $row = array(
        "<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link <small>$source</small><br />{$text}",
      );
    }

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

function twitter_is_reply($status) {
  if (!user_is_authenticated()) {
    return false;
  }
  $user = user_current_username();
  return preg_match("#@$user#i", $status->text);
}

function theme_followers($feed, $hide_pagination = false) {
  $rows = array();
  if (count($feed) == 0 || $feed == '[]') return '<p>没有用户可以显示</p>';
   foreach ($feed as $user) {
    $test = "";
     /*
     foreach ($user as $usera) {
      foreach ($usera as $uk => $uv) {
        $test .= $uk;
	 $test .= ",";
	 $test .= $uv;
        $test .= ",";
      }
     }*/
     $name = theme('full_name', $user);
     $tweets_per_day = twitter_tweets_per_day($user);
     $rows[] = array(
      theme('avatar', $user->profile_image_url),
      "{$name} - {$user->location}<br />" .
      "<small>{$user->description}<br />" .
      "  {$test} {$user->statuses_count} 条微博 | 关注 {$user->friends_count} 人 | 粉丝 {$user->followers_count} 人 | 每天约 {$tweets_per_day} 条微博</small>"
     );
  }
  $content = theme('table', array(), $rows, array('class' => 'followers'));
  if (!$hide_pagination)
    $content .= theme('pagination');
  return $content;
}


function theme_full_name($user) {
  $name = "<a href='user/{$user->screen_name}'>{$user->screen_name}</a>";
  if ($user->name && $user->name != $user->screen_name) {
    $name .= " ({$user->name})";
  }
  return $name;
}

function theme_no_tweets() {
  return '<p>没有微博可以显示</p>';
}

function theme_search_results($feed) {
  $rows = array();
  foreach ($feed->results as $status) {
    $text = twitter_parse_tags($status->text);
    $link = theme('status_time_link', $status);
    $actions = theme('action_icons', $status);

    $row = array(
      theme('avatar', $status->profile_image_url),
      "<a href='user/{$status->from_user}'>{$status->from_user}</a> $actions - {$link}<br />{$text}",
    );
    if (twitter_is_reply($status)) {
      $row = array('class' => 'reply', 'data' => $row);
    }
    $rows[] = $row;
  }
  $content = theme('table', array(), $rows, array('class' => 'timeline'));
  $content .= theme('pagination');
  return $content;
}

function theme_search_form($query) {
  $query = stripslashes(htmlentities($query,ENT_QUOTES,"UTF-8"));
  return "<form action='search' method='get'><input name='query' value=\"$query\" /><input type='submit' value='搜索' /></form>";
}

/*function theme_external_link($url, $content = null) {
	//Long URL functionality.  Also uncomment function long_url($shortURL)
	if (!$content) 
	{	
		return "<a href='$url' target='_blank'>".long_url($url)."</a>";
	}
	else
	{
		return "<a href='$url' target='_blank'>$content</a>";
	}
	
}*/

function theme_external_link($url) {
	if (setting_fetch('longurl') == 'yes') {
		$lurl = long_url($url);
	} else {
		$lurl = $url;
	}
	if (setting_fetch('linktrans') == 'yes') {
		return "<a href='$lurl'>[link]</a>";
	} else {
		return "<a href='$lurl'>$lurl</a>";
	}
}

function theme_pagination() {
  $page = intval($_GET['page']);
  if (preg_match('#&q(.*)#', $_SERVER['QUERY_STRING'], $matches)) {
    $query = $matches[0];
  }
  if ($page == 0) $page = 1;
  $links[] = "<a href='{$_GET['q']}?page=".($page+1)."$query' accesskey='9'>下页</a> ";
  if ($page > 1) $links[] = "<a href='{$_GET['q']}?page=".($page-1)."$query' accesskey='8'>上页</a> ";
  return '<p>'.implode(' | ', $links).'</p>';
}


/*function theme_action_icons($status) {
  $from = $status->from->screen_name;
  $actions = array();
 
  if (!$status->is_direct) {
    $actions[] = theme('action_icon', "user/{$from}/reply/{$status->id}", 'images/reply.png', '@');
  }
  if (!user_is_current_user($from)) {
    $actions[] = theme('action_icon', "directs/create/{$from}", 'images/dm.png', 'DM');
  } 
  if (!$status->is_direct) {
    if ($status->favorited == '1') {
      $actions[] = theme('action_icon', "unfavourite/{$status->id}", 'images/star.png', '不收');
    } else {
      $actions[] = theme('action_icon', "favourite/{$status->id}", 'images/star_grey.png', '收藏');
    }
    $actions[] = theme('action_icon', "retweet/{$status->id}", 'images/retweet.png', '转发');
   $actions[] = theme('action_icon', "comment/{$status->id}", 'images/reply.png', '评论');
    if (user_is_current_user($from)) {
      $actions[] = theme('action_icon', "confirm/delete/{$status->id}", 'images/trash.gif', '删除');
    }
  } else {
    $actions[] = theme('action_icon', "directs/delete/{$status->id}", 'images/trash.gif', '删除');
  }

  return implode(' ', $actions);
}*/

function theme_action_icons($status) {
	$from = $status->from->screen_name;
	$actions = array();
	/*if (!$status->is_direct) {
		if (setting_fetch('buttonre', 'yes') == 'yes') {
			$actions[] = theme('action_icon', BASE_URL."user/{$from}/reply/{$status->id}", 'images/reply.png', '@');
		}
	}*/
		if (!user_is_current_user($from)) {
			$actions[] = theme('action_icon', BASE_URL."directs/create/{$from}", 'images/dm.png', '私信');
		}
	if (!$status->is_direct) {
		if (setting_fetch('buttonfav', 'yes') == 'yes') {
			if ($status->favorited == '1') {
				$actions[] = theme('action_icon', BASE_URL."unfavourite/{$status->id}", 'images/star.png', '不收藏');
			} else {
				$actions[] = theme('action_icon', BASE_URL."favourite/{$status->id}", 'images/star_grey.png', '收藏');
			}
		}
		if (setting_fetch('buttonrt', 'yes') == 'yes') {
			$actions[] = theme('action_icon', BASE_URL."retweet/{$status->id}", 'images/retweet.png', '转发');
		}
		if (setting_fetch('buttonco', 'yes') == 'yes') {
		    $actions[] = theme('action_icon', BASE_URL."comment/{$status->id}", 'images/reply.png', '评论');
		}
		if (setting_fetch('buttondel', 'yes') == 'yes') {
			if (user_is_current_user($from)) {
				$actions[] = theme('action_icon', BASE_URL."confirm/delete/{$status->id}", 'images/trash.gif', '删除');
			}
		}
	} else {
		$actions[] = theme('action_icon', BASE_URL."directs/delete/{$status->id}", 'images/trash.gif', '删除');
	}
	return implode(' ', $actions);
}
/*
function theme_action_icons2($status) {
  $from = $status->from->screen_name;
  $actions = array();
 /* 
  if (!$status->is_direct) {
    $actions[] = theme('action_icon', "user/{$from}/reply/{$status->id}", 'images/reply.png', '@');
  }
  if (!user_is_current_user($from)) {
    $actions[] = theme('action_icon', "directs/create/{$from}", 'images/dm.png', 'DM');
  } */
/*  if (!$status->retweeted_status) {
    if ($$status->retweeted_status->favorited == '1') {
      $actions2[] = theme('action_icon', "unfavourite/{$status->retweeted_status->id}", 'images/star.png', '不收');
    } else {
      $actions2[] = theme('action_icon', "favourite/{$status->retweeted_status->id}", 'images/star_grey.png', '收藏');
    }
    $actions2[] = theme('action_icon', "retweet/{$status->retweeted_status->id}", 'images/retweet.png', '转发');
   $actions2[] = theme('action_icon', "comment/{$status->retweeted_status->id}", 'images/reply.png', '评论');
    if (user_is_current_user($from)) {
      $actions2[] = theme('action_icon', "confirm/delete/{$status->retweeted_status->id}", 'images/trash.gif', '删除');
    }
  } else {
    $actions2[] = theme('action_icon', "directs/delete/{$status->retweeted_status->id}", 'images/trash.gif', '删除');
  }

  return implode(' ', $actions2);
}
*/
function theme_action_icon($url, $image_url, $text) {
  // alt attribute left off to reduce bandwidth by about 720 bytes per page
  	if (setting_fetch('buttonintext', 'yes') == 'yes') {
		return "<a href='$url'>$text</a>";
	} else {
        return "<a href='$url'><img src='$image_url' /></a>";
	}
}

?>