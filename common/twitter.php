<?php

require 'Autolink.php';
require 'Extractor.php';

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
	'weibo-recomment' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'weibo_recomment',
	),
	'mentions' => array(
		'security' => true,
		'callback' => 'twitter_replies_page',
		'accesskey' => '1',
	),
	'cmts' => array(
		'security' => true,
		'callback' => 'twitter_cmts_page',
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
	/*'search' => array(
		'security' => true,
		'callback' => 'twitter_search_page',
		'accesskey' => '3',
	),*/
	'public' => array(
		'security' => true,
		'hidden' => true,
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
		'callback' =>	'twitter_favourites_page',
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
	'delete' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_delete_page',
	),
	'delcmt' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_delcmt_page',
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
	'recomment' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'weibo_recomment_page',
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
	'upload' => array(
		'security' => true,
		'hidden' => false,
		'callback' => 'twitter_upload_page',
	),
	'trends' => array(
		'security' => true,
		'callback' => 'twitter_trends_page',
		'hidden' => true,
	),
	'blockings' => array(
		'security' => true,
		'hidden' => true,
		'callback' => 'twitter_blockings_page',
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
	$request = 'friendships/show';
	$following = twitter_process($request, array('target_screen_name'=>$user_a));
	
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
	
	//bool in_array	( mixed $needle	, array $haystack	[, bool $strict	] )		
	//If the authenticate user has blocked $query it will appear in the array
	return in_array($query,$blocked);
}

function twitter_trends_page($query) 
{
	$trend_type = $query[1];
	if($trend_type == '') $trend_type = 'hourly';
	$request = API_URL.'/2/trends/'.$trend_type.'.json';
	$trends = twitter_process($request);
	$search_url = 'search?query=';
	foreach($trends->trends as $temp) {
		foreach($temp as $trend) {
			$row = array('<strong><a href="' . $search_url . urlencode($trend->query) . '">' . $trend->name . '</a></strong>');
			$rows[] = $row;
		}
	}
	//$headers = array('<p><a href="trends">Current</a> | <a href="trends/daily">Daily</a> | <a href="trends/weekly">Weekly</a></p>'); //output for daily and weekly not great at the moment
	$headers = array();
	$content = theme('table', $headers, $rows, array('class' => 'timeline'));
	theme('page', '话题', $content);
}

function js_counter($name, $length='140')
{
	$script = '<script type="text/javascript">
function updateCount() {
var remaining = ' . $length . ' - document.getElementById("' . $name . '").value.length;
document.getElementById("remaining").innerHTML = remaining;
if(remaining < 0) {
 var colour = "#FF0000";
 var weight = "bold";
} else {
 var colour = "";
 var weight = "";
}
document.getElementById("remaining").style.color = colour;
document.getElementById("remaining").style.fontWeight = weight;
setTimeout(updateCount, 400);
}
updateCount();
</script>';
	return $script;
}

function twitter_upload_page($query) {
	if ($_POST['message']) {
		$response = twitter_process('statuses/upload', array(
			'pic' => '@'.$_FILES['media']['tmp_name'],
			'status' => stripslashes($_POST['message']),
		), "post");
		if ( $response->mid) {
			$id = $matches[1];
			twitter_refresh("upload/confirm/$id");
		} else {
			twitter_refresh('upload/fail');
		}
	} elseif ($query[1] == 'confirm') {
		$content = "<p>Upload success.</p>";
	} elseif ($query[1] == 'fail') {
		$content = '<p>Weibo pic upload failed. No idea why!</p>';
	} else {
		$content = '<form method="post" action="upload" enctype="multipart/form-data">Image <input type="file" name="media" /><br />Message: <textarea name="message" cols="80" rows="6"></textarea><br /><input type="submit" value="Upload" /></form>';
	}
	return theme('page', 'Twitpic Upload', $content);
}

function endsWith( $str, $sub ) {
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

function twitter_process($url, $post_data = false, $method = "get") {
	$url = str_replace("https://api.twitter.com/", "", $url);
	$url = str_replace("http://api.twitter.com/", "", $url);
	if(FILE_IO) file_put_contents('./tmp/session', var_export($_SESSION, true)."\n", FILE_APPEND);
    #$c = new WeiboClient(OAUTH_CONSUMER_KEY , OAUTH_CONSUMER_SECRET , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']);
    $c = new SaeTClientV2(OAUTH_CONSUMER_KEY , OAUTH_CONSUMER_SECRET , $_SESSION['token']['access_token']) ;

    $c->oauth->decode_json = false;
	//if(FILE_IO) file_put_contents('/tmp/dabr.log', $method." ".$url." ".json_encode($post_data)."\n", FILE_APPEND);
    if($method === "get") {
        $response = $c->oauth->get($url, $post_data);
    } else {
        $response = $c->oauth->post($url, $post_data, isset($post_data['pic']));
    }
	//if(FILE_IO) file_put_contents('/tmp/session', var_export($c->oauth->http_info, true)."\n", FILE_APPEND);
	
	if(FILE_IO) file_put_contents('./tmp/urls', $url." ".user_type(). " ".json_encode($post_data)."\n", FILE_APPEND);

	//if(FILE_IO) file_put_contents("./tmp/api_response.dump",  "$response <==== $url\n", FILE_APPEND);
	switch( intval( $c->oauth->http_info['http_code'] ) ) {
		case 200:
			$json = json_decode($response);
			if ($json) return $json;
			return $response;
		case 0:
			theme('error', '<h2>连接超时</h2><p>请稍等几分钟后刷新重新连接。</p>'."<p>$url</p><pre>".var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true).'</pre>');
		case 403:
			$result = json_decode($response);
			$result = $result->error ? $result->error : $response;
			theme('error', "<h2>因微博接口限制，该功能暂不可用</h2><p>{$c->oauth->http_info['http_code']}: {$result}</p><hr><p>$url</p>");
		default:
			$result = json_decode($response);
			$result = $result->error ? $result->error : $response;
			if (strlen($result) > 500) $result = '微博API返回结果出现错误';
			theme('error', "<h2>调用微博API时遇到错误</h2><p>{$c->oauth->http_info['http_code']}: {$result}</p><hr><p>$url</p><pre>".var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true).'</pre>');
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

class Dabr_Autolink extends Twitter_Autolink {
	function replacementURLs($matches) {
		$replacement	= $matches[2];
		$url = $matches[3];
		if (!preg_match("#^https{0,1}://#i", $url)) {
			$url = "http://{$url}";
		}
		if (setting_fetch('gwt') == 'on') {
			$encoded = urlencode($url);
			$replacement .= "<a href='http://google.com/gwt/n?u={$encoded}' target='_blank'>{$url}</a>";
		} else {
			$replacement .= theme('external_link', $url);
		}
		return $replacement;
	}
}

function twitter_parse_tags($input)
{
	$out = $input;

	$autolink = new Dabr_Autolink();
	$out = $autolink->autolink($out);

	//If this is worksafe mode - don't display any images
	if (!in_array(setting_fetch('browser'), array('text', 'worksafe')))
	{
		//Add in images
		$out = twitter_embed_thumbnails($out);
	}

	//Linebreaks.	Some clients insert \n for formatting.
	$out = nl2br($out);

	//Return the completed string
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

function twitter_embed_thumbnails($text) 
{
	if (setting_fetch('hide_inline')) {
		return $text;
	}
	$images = array();
	$tmp = strip_tags($text);
	
	//Using oEmbed from http://api.embed.ly/
	$embedly_re = "/http:\/\/(.*youtube\.com\/watch.*|.*\.youtube\.com\/v\/.*|youtu\.be\/.*|.*\.youtube\.com\/user\/.*|.*\.youtube\.com\/.*#.*\/.*|m\.youtube\.com\/watch.*|m\.youtube\.com\/index.*|.*\.youtube\.com\/profile.*|.*justin\.tv\/.*|.*justin\.tv\/.*\/b\/.*|.*justin\.tv\/.*\/w\/.*|www\.ustream\.tv\/recorded\/.*|www\.ustream\.tv\/channel\/.*|www\.ustream\.tv\/.*|qik\.com\/video\/.*|qik\.com\/.*|qik\.ly\/.*|.*revision3\.com\/.*|.*\.dailymotion\.com\/video\/.*|.*\.dailymotion\.com\/.*\/video\/.*|www\.collegehumor\.com\/video:.*|.*twitvid\.com\/.*|www\.break\.com\/.*\/.*|vids\.myspace\.com\/index\.cfm\?fuseaction=vids\.individual&videoid.*|www\.myspace\.com\/index\.cfm\?fuseaction=.*&videoid.*|www\.metacafe\.com\/watch\/.*|www\.metacafe\.com\/w\/.*|blip\.tv\/file\/.*|.*\.blip\.tv\/file\/.*|video\.google\.com\/videoplay\?.*|.*revver\.com\/video\/.*|video\.yahoo\.com\/watch\/.*\/.*|video\.yahoo\.com\/network\/.*|.*viddler\.com\/explore\/.*\/videos\/.*|liveleak\.com\/view\?.*|www\.liveleak\.com\/view\?.*|animoto\.com\/play\/.*|dotsub\.com\/view\/.*|www\.overstream\.net\/view\.php\?oid=.*|www\.livestream\.com\/.*|www\.worldstarhiphop\.com\/videos\/video.*\.php\?v=.*|worldstarhiphop\.com\/videos\/video.*\.php\?v=.*|teachertube\.com\/viewVideo\.php.*|www\.teachertube\.com\/viewVideo\.php.*|www1\.teachertube\.com\/viewVideo\.php.*|www2\.teachertube\.com\/viewVideo\.php.*|bambuser\.com\/v\/.*|bambuser\.com\/channel\/.*|bambuser\.com\/channel\/.*\/broadcast\/.*|www\.schooltube\.com\/video\/.*\/.*|bigthink\.com\/ideas\/.*|bigthink\.com\/series\/.*|sendables\.jibjab\.com\/view\/.*|sendables\.jibjab\.com\/originals\/.*|www\.xtranormal\.com\/watch\/.*|dipdive\.com\/media\/.*|dipdive\.com\/member\/.*\/media\/.*|dipdive\.com\/v\/.*|.*\.dipdive\.com\/media\/.*|.*\.dipdive\.com\/v\/.*|v\.youku\.com\/v_show\/.*\.html|v\.youku\.com\/v_playlist\/.*\.html|www\.snotr\.com\/video\/.*|snotr\.com\/video\/.*|.*yfrog\..*\/.*|tweetphoto\.com\/.*|www\.flickr\.com\/photos\/.*|flic\.kr\/.*|twitpic\.com\/.*|www\.twitpic\.com\/.*|twitpic\.com\/photos\/.*|www\.twitpic\.com\/photos\/.*|.*imgur\.com\/.*|.*\.posterous\.com\/.*|post\.ly\/.*|twitgoo\.com\/.*|i.*\.photobucket\.com\/albums\/.*|s.*\.photobucket\.com\/albums\/.*|phodroid\.com\/.*\/.*\/.*|www\.mobypicture\.com\/user\/.*\/view\/.*|moby\.to\/.*|xkcd\.com\/.*|www\.xkcd\.com\/.*|imgs\.xkcd\.com\/.*|www\.asofterworld\.com\/index\.php\?id=.*|www\.asofterworld\.com\/.*\.jpg|asofterworld\.com\/.*\.jpg|www\.qwantz\.com\/index\.php\?comic=.*|23hq\.com\/.*\/photo\/.*|www\.23hq\.com\/.*\/photo\/.*|.*dribbble\.com\/shots\/.*|drbl\.in\/.*|.*\.smugmug\.com\/.*|.*\.smugmug\.com\/.*#.*|emberapp\.com\/.*\/images\/.*|emberapp\.com\/.*\/images\/.*\/sizes\/.*|emberapp\.com\/.*\/collections\/.*\/.*|emberapp\.com\/.*\/categories\/.*\/.*\/.*|embr\.it\/.*|picasaweb\.google\.com.*\/.*\/.*#.*|picasaweb\.google\.com.*\/lh\/photo\/.*|picasaweb\.google\.com.*\/.*\/.*|dailybooth\.com\/.*\/.*|brizzly\.com\/pic\/.*|pics\.brizzly\.com\/.*\.jpg|img\.ly\/.*|www\.tinypic\.com\/view\.php.*|tinypic\.com\/view\.php.*|www\.tinypic\.com\/player\.php.*|tinypic\.com\/player\.php.*|www\.tinypic\.com\/r\/.*\/.*|tinypic\.com\/r\/.*\/.*|.*\.tinypic\.com\/.*\.jpg|.*\.tinypic\.com\/.*\.png|meadd\.com\/.*\/.*|meadd\.com\/.*|.*\.deviantart\.com\/art\/.*|.*\.deviantart\.com\/gallery\/.*|.*\.deviantart\.com\/#\/.*|fav\.me\/.*|.*\.deviantart\.com|.*\.deviantart\.com\/gallery|.*\.deviantart\.com\/.*\/.*\.jpg|.*\.deviantart\.com\/.*\/.*\.gif|.*\.deviantart\.net\/.*\/.*\.jpg|.*\.deviantart\.net\/.*\/.*\.gif|plixi\.com\/p\/.*|plixi\.com\/profile\/home\/.*|plixi\.com\/.*|www\.fotopedia\.com\/.*\/.*|fotopedia\.com\/.*\/.*|photozou\.jp\/photo\/show\/.*\/.*|photozou\.jp\/photo\/photo_only\/.*\/.*|instagr\.am\/p\/.*|skitch\.com\/.*\/.*\/.*|img\.skitch\.com\/.*|https:\/\/skitch\.com\/.*\/.*\/.*|https:\/\/img\.skitch\.com\/.*|share\.ovi\.com\/media\/.*\/.*|www\.questionablecontent\.net\/|questionablecontent\.net\/|www\.questionablecontent\.net\/view\.php.*|questionablecontent\.net\/view\.php.*|questionablecontent\.net\/comics\/.*\.png|www\.questionablecontent\.net\/comics\/.*\.png|picplz\.com\/user\/.*\/pic\/.*\/|twitrpix\.com\/.*|.*\.twitrpix\.com\/.*|www\.someecards\.com\/.*\/.*|someecards\.com\/.*\/.*|some\.ly\/.*|www\.some\.ly\/.*|pikchur\.com\/.*|achewood\.com\/.*|www\.achewood\.com\/.*|achewood\.com\/index\.php.*|www\.achewood\.com\/index\.php.*|www\.whitehouse\.gov\/photos-and-video\/video\/.*|www\.whitehouse\.gov\/video\/.*|wh\.gov\/photos-and-video\/video\/.*|wh\.gov\/video\/.*|www\.hulu\.com\/watch.*|www\.hulu\.com\/w\/.*|hulu\.com\/watch.*|hulu\.com\/w\/.*|.*crackle\.com\/c\/.*|www\.fancast\.com\/.*\/videos|www\.funnyordie\.com\/videos\/.*|www\.funnyordie\.com\/m\/.*|funnyordie\.com\/videos\/.*|funnyordie\.com\/m\/.*|www\.vimeo\.com\/groups\/.*\/videos\/.*|www\.vimeo\.com\/.*|vimeo\.com\/groups\/.*\/videos\/.*|vimeo\.com\/.*|vimeo\.com\/m\/#\/.*|www\.ted\.com\/talks\/.*\.html.*|www\.ted\.com\/talks\/lang\/.*\/.*\.html.*|www\.ted\.com\/index\.php\/talks\/.*\.html.*|www\.ted\.com\/index\.php\/talks\/lang\/.*\/.*\.html.*|.*nfb\.ca\/film\/.*|www\.thedailyshow\.com\/watch\/.*|www\.thedailyshow\.com\/full-episodes\/.*|www\.thedailyshow\.com\/collection\/.*\/.*\/.*|movies\.yahoo\.com\/movie\/.*\/video\/.*|movies\.yahoo\.com\/movie\/.*\/trailer|movies\.yahoo\.com\/movie\/.*\/video|www\.colbertnation\.com\/the-colbert-report-collections\/.*|www\.colbertnation\.com\/full-episodes\/.*|www\.colbertnation\.com\/the-colbert-report-videos\/.*|www\.comedycentral\.com\/videos\/index\.jhtml\?.*|www\.theonion\.com\/video\/.*|theonion\.com\/video\/.*|wordpress\.tv\/.*\/.*\/.*\/.*\/|www\.traileraddict\.com\/trailer\/.*|www\.traileraddict\.com\/clip\/.*|www\.traileraddict\.com\/poster\/.*|www\.escapistmagazine\.com\/videos\/.*|www\.trailerspy\.com\/trailer\/.*\/.*|www\.trailerspy\.com\/trailer\/.*|www\.trailerspy\.com\/view_video\.php.*|www\.atom\.com\/.*\/.*\/|fora\.tv\/.*\/.*\/.*\/.*|www\.spike\.com\/video\/.*|www\.gametrailers\.com\/video\/.*|gametrailers\.com\/video\/.*|www\.koldcast\.tv\/video\/.*|www\.koldcast\.tv\/#video:.*|techcrunch\.tv\/watch.*|techcrunch\.tv\/.*\/watch.*|mixergy\.com\/.*|video\.pbs\.org\/video\/.*|www\.zapiks\.com\/.*|tv\.digg\.com\/diggnation\/.*|tv\.digg\.com\/diggreel\/.*|tv\.digg\.com\/diggdialogg\/.*|www\.trutv\.com\/video\/.*|www\.nzonscreen\.com\/title\/.*|nzonscreen\.com\/title\/.*|app\.wistia\.com\/embed\/medias\/.*|https:\/\/app\.wistia\.com\/embed\/medias\/.*|hungrynation\.tv\/.*\/episode\/.*|www\.hungrynation\.tv\/.*\/episode\/.*|hungrynation\.tv\/episode\/.*|www\.hungrynation\.tv\/episode\/.*|indymogul\.com\/.*\/episode\/.*|www\.indymogul\.com\/.*\/episode\/.*|indymogul\.com\/episode\/.*|www\.indymogul\.com\/episode\/.*|channelfrederator\.com\/.*\/episode\/.*|www\.channelfrederator\.com\/.*\/episode\/.*|channelfrederator\.com\/episode\/.*|www\.channelfrederator\.com\/episode\/.*|tmiweekly\.com\/.*\/episode\/.*|www\.tmiweekly\.com\/.*\/episode\/.*|tmiweekly\.com\/episode\/.*|www\.tmiweekly\.com\/episode\/.*|99dollarmusicvideos\.com\/.*\/episode\/.*|www\.99dollarmusicvideos\.com\/.*\/episode\/.*|99dollarmusicvideos\.com\/episode\/.*|www\.99dollarmusicvideos\.com\/episode\/.*|ultrakawaii\.com\/.*\/episode\/.*|www\.ultrakawaii\.com\/.*\/episode\/.*|ultrakawaii\.com\/episode\/.*|www\.ultrakawaii\.com\/episode\/.*|barelypolitical\.com\/.*\/episode\/.*|www\.barelypolitical\.com\/.*\/episode\/.*|barelypolitical\.com\/episode\/.*|www\.barelypolitical\.com\/episode\/.*|barelydigital\.com\/.*\/episode\/.*|www\.barelydigital\.com\/.*\/episode\/.*|barelydigital\.com\/episode\/.*|www\.barelydigital\.com\/episode\/.*|threadbanger\.com\/.*\/episode\/.*|www\.threadbanger\.com\/.*\/episode\/.*|threadbanger\.com\/episode\/.*|www\.threadbanger\.com\/episode\/.*|vodcars\.com\/.*\/episode\/.*|www\.vodcars\.com\/.*\/episode\/.*|vodcars\.com\/episode\/.*|www\.vodcars\.com\/episode\/.*|confreaks\.net\/videos\/.*|www\.confreaks\.net\/videos\/.*|video\.allthingsd\.com\/video\/.*|aniboom\.com\/animation-video\/.*|www\.aniboom\.com\/animation-video\/.*|clipshack\.com\/Clip\.aspx\?.*|www\.clipshack\.com\/Clip\.aspx\?.*|grindtv\.com\/.*\/video\/.*|www\.grindtv\.com\/.*\/video\/
.*|ifood\.tv\/recipe\/.*|ifood\.tv\/video\/.*|ifood\.tv\/channel\/user\/.*|www\.ifood\.tv\/recipe\/.*|www\.ifood\.tv\/video\/.*|www\.ifood\.tv\/channel\/user\/.*|logotv\.com\/video\/.*|www\.logotv\.com\/video\/.*|lonelyplanet\.com\/Clip\.aspx\?.*|www\.lonelyplanet\.com\/Clip\.aspx\?.*|streetfire\.net\/video\/.*\.htm.*|www\.streetfire\.net\/video\/.*\.htm.*|trooptube\.tv\/videos\/.*|www\.trooptube\.tv\/videos\/.*|www\.godtube\.com\/featured\/video\/.*|godtube\.com\/featured\/video\/.*|www\.godtube\.com\/watch\/.*|godtube\.com\/watch\/.*|www\.tangle\.com\/view_video.*|mediamatters\.org\/mmtv\/.*|www\.clikthrough\.com\/theater\/video\/.*|soundcloud\.com\/.*|soundcloud\.com\/.*\/.*|soundcloud\.com\/.*\/sets\/.*|soundcloud\.com\/groups\/.*|snd\.sc\/.*|www\.last\.fm\/music\/.*|www\.last\.fm\/music\/+videos\/.*|www\.last\.fm\/music\/+images\/.*|www\.last\.fm\/music\/.*\/_\/.*|www\.last\.fm\/music\/.*\/.*|www\.mixcloud\.com\/.*\/.*\/|www\.radionomy\.com\/.*\/radio\/.*|radionomy\.com\/.*\/radio\/.*|www\.entertonement\.com\/clips\/.*|www\.rdio\.com\/#\/artist\/.*\/album\/.*|www\.rdio\.com\/artist\/.*\/album\/.*|www\.zero-inch\.com\/.*|.*\.bandcamp\.com\/|.*\.bandcamp\.com\/track\/.*|.*\.bandcamp\.com\/album\/.*|freemusicarchive\.org\/music\/.*|www\.freemusicarchive\.org\/music\/.*|freemusicarchive\.org\/curator\/.*|www\.freemusicarchive\.org\/curator\/.*|www\.npr\.org\/.*\/.*\/.*\/.*\/.*|www\.npr\.org\/.*\/.*\/.*\/.*\/.*\/.*|www\.npr\.org\/.*\/.*\/.*\/.*\/.*\/.*\/.*|www\.npr\.org\/templates\/story\/story\.php.*|huffduffer\.com\/.*\/.*|www\.audioboo\.fm\/boos\/.*|audioboo\.fm\/boos\/.*|boo\.fm\/b.*|www\.xiami\.com\/song\/.*|xiami\.com\/song\/.*|www\.saynow\.com\/playMsg\.html.*|www\.saynow\.com\/playMsg\.html.*|listen\.grooveshark\.com\/s\/.*|radioreddit\.com\/songs.*|www\.radioreddit\.com\/songs.*|radioreddit\.com\/\?q=songs.*|www\.radioreddit\.com\/\?q=songs.*|espn\.go\.com\/video\/clip.*|espn\.go\.com\/.*\/story.*|abcnews\.com\/.*\/video\/.*|abcnews\.com\/video\/playerIndex.*|washingtonpost\.com\/wp-dyn\/.*\/video\/.*\/.*\/.*\/.*|www\.washingtonpost\.com\/wp-dyn\/.*\/video\/.*\/.*\/.*\/.*|www\.boston\.com\/video.*|boston\.com\/video.*|www\.facebook\.com\/photo\.php.*|www\.facebook\.com\/video\/video\.php.*|www\.facebook\.com\/v\/.*|cnbc\.com\/id\/.*\?.*video.*|www\.cnbc\.com\/id\/.*\?.*video.*|cnbc\.com\/id\/.*\/play\/1\/video\/.*|www\.cnbc\.com\/id\/.*\/play\/1\/video\/.*|cbsnews\.com\/video\/watch\/.*|www\.google\.com\/buzz\/.*\/.*\/.*|www\.google\.com\/buzz\/.*|www\.google\.com\/profiles\/.*|google\.com\/buzz\/.*\/.*\/.*|google\.com\/buzz\/.*|google\.com\/profiles\/.*|www\.cnn\.com\/video\/.*|edition\.cnn\.com\/video\/.*|money\.cnn\.com\/video\/.*|today\.msnbc\.msn\.com\/id\/.*\/vp\/.*|www\.msnbc\.msn\.com\/id\/.*\/vp\/.*|www\.msnbc\.msn\.com\/id\/.*\/ns\/.*|today\.msnbc\.msn\.com\/id\/.*\/ns\/.*|multimedia\.foxsports\.com\/m\/video\/.*\/.*|msn\.foxsports\.com\/video.*|www\.globalpost\.com\/video\/.*|www\.globalpost\.com\/dispatch\/.*|guardian\.co\.uk\/.*\/video\/.*\/.*\/.*\/.*|www\.guardian\.co\.uk\/.*\/video\/.*\/.*\/.*\/.*|bravotv\.com\/.*\/.*\/videos\/.*|www\.bravotv\.com\/.*\/.*\/videos\/.*|video\.nationalgeographic\.com\/.*\/.*\/.*\.html|dsc\.discovery\.com\/videos\/.*|animal\.discovery\.com\/videos\/.*|health\.discovery\.com\/videos\/.*|investigation\.discovery\.com\/videos\/.*|military\.discovery\.com\/videos\/.*|planetgreen\.discovery\.com\/videos\/.*|science\.discovery\.com\/videos\/.*|tlc\.discovery\.com\/videos\/.*|.*amazon\..*\/gp\/product\/.*|.*amazon\..*\/.*\/dp\/.*|.*amazon\..*\/dp\/.*|.*amazon\..*\/o\/ASIN\/.*|.*amazon\..*\/gp\/offer-listing\/.*|.*amazon\..*\/.*\/ASIN\/.*|.*amazon\..*\/gp\/product\/images\/.*|.*amazon\..*\/gp\/aw\/d\/.*|www\.amzn\.com\/.*|amzn\.com\/.*|www\.shopstyle\.com\/browse.*|www\.shopstyle\.com\/action\/apiVisitRetailer.*|api\.shopstyle\.com\/action\/apiVisitRetailer.*|www\.shopstyle\.com\/action\/viewLook.*|gist\.github\.com\/.*|twitter\.com\/.*\/status\/.*|twitter\.com\/.*\/statuses\/.*|www\.twitter\.com\/.*\/status\/.*|www\.twitter\.com\/.*\/statuses\/.*|mobile\.twitter\.com\/.*\/status\/.*|mobile\.twitter\.com\/.*\/statuses\/.*|https:\/\/twitter\.com\/.*\/status\/.*|https:\/\/twitter\.com\/.*\/statuses\/.*|https:\/\/www\.twitter\.com\/.*\/status\/.*|https:\/\/www\.twitter\.com\/.*\/statuses\/.*|https:\/\/mobile\.twitter\.com\/.*\/status\/.*|https:\/\/mobile\.twitter\.com\/.*\/statuses\/.*|www\.crunchbase\.com\/.*\/.*|crunchbase\.com\/.*\/.*|www\.slideshare\.net\/.*\/.*|www\.slideshare\.net\/mobile\/.*\/.*|slidesha\.re\/.*|.*\.scribd\.com\/doc\/.*|screenr\.com\/.*|polldaddy\.com\/community\/poll\/.*|polldaddy\.com\/poll\/.*|answers\.polldaddy\.com\/poll\/.*|www\.5min\.com\/Video\/.*|www\.howcast\.com\/videos\/.*|www\.screencast\.com\/.*\/media\/.*|screencast\.com\/.*\/media\/.*|www\.screencast\.com\/t\/.*|screencast\.com\/t\/.*|issuu\.com\/.*\/docs\/.*|www\.kickstarter\.com\/projects\/.*\/.*|www\.scrapblog\.com\/viewer\/viewer\.aspx.*|ping\.fm\/p\/.*|chart\.ly\/symbols\/.*|chart\.ly\/.*|maps\.google\.com\/maps\?.*|maps\.google\.com\/\?.*|maps\.google\.com\/maps\/ms\?.*|.*\.craigslist\.org\/.*\/.*|my\.opera\.com\/.*\/albums\/show\.dml\?id=.*|my\.opera\.com\/.*\/albums\/showpic\.dml\?album=.*&picture=.*|tumblr\.com\/.*|.*\.tumblr\.com\/post\/.*|www\.polleverywhere\.com\/polls\/.*|www\.polleverywhere\.com\/multiple_choice_polls\/.*|www\.polleverywhere\.com\/free_text_polls\/.*|www\.quantcast\.com\/wd:.*|www\.quantcast\.com\/.*|siteanalytics\.compete\.com\/.*|statsheet\.com\/statplot\/charts\/.*\/.*\/.*\/.*|statsheet\.com\/statplot\/charts\/e\/.*|statsheet\.com\/.*\/teams\/.*\/.*|statsheet\.com\/tools\/chartlets\?chart=.*|.*\.status\.net\/notice\/.*|identi\.ca\/notice\/.*|brainbird\.net\/notice\/.*|shitmydadsays\.com\/notice\/.*|www\.studivz\.net\/Profile\/.*|www\.studivz\.net\/l\/.*|www\.studivz\.net\/Groups\/Overview\/.*|www\.studivz\.net\/Gadgets\/Info\/.*|www\.studivz\.net\/Gadgets\/Install\/.*|www\.studivz\.net\/.*|www\.meinvz\.net\/Profile\/.*|www\.meinvz\.net\/l\/.*|www\.meinvz\.net\/Groups\/Overview\/.*|www\.meinvz\.net\/Gadgets\/Info\/.*|www\.meinvz\.net\/Gadgets\/Install\/.*|www\.meinvz\.net\/.*|www\.schuelervz\.net\/Profile\/.*|www\.schuelervz\.net\/l\/.*|www\.schuelervz\.net\/Groups\/Overview\/.*|www\.schuelervz\.net\/Gadgets\/Info\/.*|www\.schuelervz\.net\/Gadgets\/Install\/.*|www\.schuelervz\.net\/.*|myloc\.me\/.*|pastebin\.com\/.*|pastie\.org\/.*|www\.pastie\.org\/.*|redux\.com\/stream\/item\/.*\/.*|redux\.com\/f\/.*\/.*|www\.redux\.com\/stream\/item\/.*\/.*|www\.redux\.com\/f\/.*\/.*|cl\.ly\/.*|cl\.ly\/.*\/content|speakerdeck\.com\/u\/.*\/p\/.*|www\.kiva\.org\/lend\/.*|www\.timetoast\.com\/timelines\/.*|storify\.com\/.*\/.*|.*meetup\.com\/.*|meetu\.ps\/.*|www\.dailymile\.com\/people\/.*\/entries\/.*|.*\.kinomap\.com\/.*|www\.metacdn\.com\/api\/users\/.*\/content\/.*|www\.metacdn\.com\/api\/users\/.*\/media\/.*|prezi\.com\/.*\/.*|.*\.uservoice\.com\/.*\/suggestions\/.*|formspring\.me\/.*|www\.formspring\.me\/.*|formspring\.me\/.*\/q\/.*|www\.formspring\.me\/.*\/q\/.*|twitlonger\.com\/show\/.*|www\.twitlonger\.com\/show\/.*|tl\.gd\/.*|www\.qwiki\.com\/q\/.*|crocodoc\.com\/.*|.*\.crocodoc\.com\/.*|https:\/\/crocodoc\.com\/.*|https:\/\/.*\.crocodoc\.com\/.*|4sq\.com\/.*|.*\.4sq\.com\/.*)/i";
	
	//Tokenise the string (on whitespace) and search through it
	$tok = strtok($tmp, " \n\t");
	while ($tok !== false) 
	{
		if (preg_match_all($embedly_re, $tok, $matches, PREG_PATTERN_ORDER) > 0)
		{
			foreach ($matches[1] as $key => $match)
			{
				//Should use &maxwidth, but hard to know width of device - so using tinysrc to resize to 50%
				$url = "http://api.embed.ly/1/oembed?url=" . $match . "&format=json";
				
				$embedly_json = twitter_fetch($url);
				$embedly_data = json_decode($embedly_json);
				$thumb = $embedly_data->thumbnail_url;
				
				//We can use the height and width for better HTML, but some thumbnails are very large. Using tinysrc for now.
				$height = $embedly_data->thumbnail_height;
				$width = $embedly_data->thumbnail_width;
				
				if ($thumb) //Not all services have thumbnails
				{
					$images[] = theme('external_link', "http://$match", "<img src='http://i.tinysrc.mobi/x50/$thumb' />");
				}
			}
		}
		$tok = strtok(" \n\t");
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
		'秒' => 1
	);
	$output = '';
	foreach ($units as $key => $value) {
		if ($timestamp >= $value) {
			$output .= ($output ? ' ' : '').floor($timestamp / $value).''.$key;//时间空格
			$timestamp %= $value;
			$granularity--;
		}
		if ($granularity == 0) {
			break;
		}
	}
	return $output ? $output : '0 秒';
}

function twitter_status_page($query) {
	$id = (string) $query[1];
	if (is_numeric($id)) {
		$request = "statuses/show";
		$status = twitter_process($request, array('id'=>$id));
		$content = theme('status', $status);
		
		theme('page', "Status $id", $content);
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
		$request = "statuses/show";
		$tl = twitter_process($request, array('id'=>$id));
		$content = theme('retweet', $tl);
		theme('page', 'Retweet', $content);
	}
}

function twitter_comment_page($query) {
	$id = (string) $query[1];
	if (is_numeric($id)) {
		$request = "comments/show_batch";
		$tl = twitter_process($request, array("cids"=>$id));
		$content = theme('comment', $tl[0]);
		theme('page', '评论', $content);
	}
}

function weibo_recomment_page($query) {
	$id = (string) $query[1];
	$id2 = (string) $query[2];
	if (is_numeric($id)) {
		$request = "comments/show";
		$tl = twitter_process($request, array("id"=>$id, "max_id"=>$id2, "count"=>1));
        $content = theme('comments_status', $tl->comments[0]->status);
        $content .= theme_recomment_form($id,$id2);
        $tl = twitter_standard_timeline($tl->comments, 'cmts');
        $content .= theme('weibocomments', $tl);
		theme('page', '评论', $content);
	}
}
/*
function twitter_replycomment_page($query) {
	$id = (string) $query[1];
	$cid = (string) $query[2];
	if (is_numeric($id)) {
		$request = "http://twitter.com/statuses/show/{$id}.json";
		$tl = twitter_process($request);
		$content = theme('comment', $tl);
		theme('page', 'Comment', $content);
	}
}*/

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
		$request = "statuses/destroy";
		$post_data = array('id'=>$id);
		$tl = twitter_process($request, $post_data, 'post');
		twitter_refresh('user/'.user_current_username());
	}
}

function twitter_delcmt_page($query) {
	twitter_ensure_post_action();

	$id = (string) $query[1];
	if (is_numeric($id)) {
		$request = "comments/destroy";
		$post_data = array('cid'=>$id);
		$tl = twitter_process($request, $post_data, 'post');
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
			$request = "friendships/create";
		} else {
			$request = "friendships/destroy";
		}
		twitter_process($request, array('screen_name'=>$user),'post');
		twitter_refresh('friends');
	}
}

function twitter_block_page($query) {
	twitter_ensure_post_action();
	$user = $query[1];
	if ($user) {
		if($query[0] == 'block'){
			$request = API_URL."blocks/create/{$user}.json";
		} else {
			$request = API_URL."blocks/destroy/{$user}.json";
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

	$request = API_URL."report_spam.json";
	twitter_process($request, $post_data);

	//Where should we return the user to?	Back to the user
	twitter_refresh("user/{$user}");
}


function twitter_confirmation_page($query)
{
	// the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
	$action = $query[1];
	$target = $query[2];	//The name of the user we are doing this action on
	$target_id = $query[3];	//The targets's ID.	Needed to check if they are being blocked.

	switch ($action) {
		case 'block':
			if (twitter_block_exists($target_id)) //Is the target blocked by the user?
			{
				$action = 'unblock';
				$content	= "<p>您确定你要 <strong>取消屏蔽 $target</strong>?</p>";
				$content .= '<ul><li>如果对方重新关注你,那么你的消息会显示在他们的首页上。</li><li>您<em>可以</em>随时重新屏蔽对方。</li></ul>';
			}
			else
			{
				$content = "<p>您确定你要 <strong>$action $target</strong>?</p>";
				$content .= "<ul><li>您将不会出现在他们的好友列表中</li><li>他们将无法看到您的消息</li><li>他们将无法关注您</li><li>您<em>可以</em>取消屏蔽他们但您需要重新关注他们。</li></ul>";
			}
			break;

		case 'delete':
			$content = '<p>你确定要删除这条微博？</p>';
			$content .= "<ul><li>微博ID: <strong>$target</strong></li><li>删除后将无法恢复。</li></ul>";
			break;
		
		case 'delcmt':
			$content = '<p>你确定要删除这条评论？</p>';
			$content .= "<ul><li>评论ID: <strong>$target</strong></li><li>删除后将无法恢复。</li></ul>";
			break;

		case 'spam':
			$content	= "<p>您真的想要举报 <strong>$target</strong> 吗？</p>";
			$content .= "<p>他们将不能关注您。</p>";
			break;

	}
	$content .= "<form action='$action/$target' method='post'>
						<input type='submit' value='确定' />
					</form>";
	theme('Page', '您确定吗', $content);
}

function twitter_friends_page($query) {
	$user = $query[1];
	if (!$user) {
		user_ensure_authenticated();
		$user = $GLOBALS['user']['screen_name'];
	}
	$cursor = isset($_GET['cursor']) ? ($_GET['cursor']) : -1;
	$request =  "friendships/friends";
	$tl = twitter_process($request, array('cursor'=>$cursor, 'screen_name'=>$user));
	$content = theme('followers', $tl);
	theme('page', 'Friends', $content);
}

function twitter_followers_page($query) {
	$user = $query[1];
	if (!$user) {
		user_ensure_authenticated();
		$user = $GLOBALS['user']['screen_name'];
	}
	$cursor = isset($_GET['cursor']) ? ($_GET['cursor']) : -1;
	$request =  "friendships/followers";
	$tl = twitter_process($request, array('cursor'=>$cursor, 'screen_name'=>$user));
	$content = theme('followers', $tl);
	theme('page', 'Followers', $content);
}

function twitter_blockings_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = $GLOBALS['user']['screen_name'];
  }
  $request = API_URL."/blocks/blocking.json?&page=".intval($_GET['page']);
  //$request ="http://twitter.com/statuses/followers/{$user}.json?page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $content = theme('followers', $tl);
  theme('page', '黑名单', $content);
}

function twitter_update() {
	twitter_ensure_post_action();
	$status = twitter_url_shorten(stripslashes(trim($_POST['status'])));
	if ($status) {
		$status = setting_fetch('fixedtagspre')." ".$status." ".setting_fetch('fixedtagspost');
		$request = 'statuses/update';
		$post_data = array('source' => OAUTH_CONSUMER_KEY, 'status' => $status);
		
		// Geolocation parameters
		list($lat, $long) = explode(',', $_POST['location']);
		$geo = 'N';
		if (is_numeric($lat) && is_numeric($long)) {
			$geo = 'Y';
			$post_data['lat'] = $lat;
			$post_data['long'] = $long;
		// $post_data['display_coordinates'] = 'false';
		}
		setcookie_year('geo', $geo);
		$b = twitter_process($request, $post_data, "post");
	}
	twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_retweet($query) {
	twitter_ensure_post_action();
	$id = $query[1];
	if (is_numeric($id)) {
		$request = 'statuses/repost';
		$status = twitter_url_shorten(stripslashes(trim($_POST['status'])));
		$post_data = array('status' => $status, 'id'=>$id);
		twitter_process($request, $post_data, 'POST');
	}
	twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_comment($query) {
	twitter_ensure_post_action();
	$comment = twitter_url_shorten(stripslashes(trim($_POST['comment'])));
	// $id = $query[1];
	$id = $_POST['id'];
	if (is_numeric($id)) {
		$request = 'comments/create';
		$post_data = array('comment' => $comment, 'id' => $id, 'comment_ori'=>1);
		$b = twitter_process($request, $post_data, 'post');
	}
	twitter_refresh("cmts/{$id}/1");
}

function weibo_recomment($query) {
	twitter_ensure_post_action();
	$comment = twitter_url_shorten(stripslashes(trim($_POST['comment'])));
	// $id = $query[1];
	$id = $_POST['id'];
	$cid = $_POST['cid'];
	if (is_numeric($id)) {
		$request = 'comments/reply';
		$post_data = array('comment' => $comment, 'id' => $id, 'cid'=> $cid);
		$b = twitter_process($request, $post_data, 'post');
	}
	twitter_refresh("cmts/{$id}/1");
}

function twitter_public_page() {
	$request = 'http://twitter.com/statuses/public_timeline.json?page=';
	$content = theme('status_form');
	$tl = twitter_standard_timeline(twitter_process($request), 'public');
	$content .= theme('timeline', $tl);
	theme('page', 'Public Timeline', $content);
}

function twitter_replies_page() {
	$count = setting_fetch('tpp', 20);
	$request = 'statuses/mentions';
	$tl = twitter_process($request, array('count'=>$count,"max_id"=>$_GET['max_id']));
	$tl = twitter_standard_timeline($tl->statuses, 'mentions');
	$content = theme('status_form');
	$content .= theme('timeline', $tl);
	theme('page', '提到我的', $content);
}

function twitter_cmts_page($query) {
	$action = strtolower(trim($query[1]));
	switch ($action) {
	case 'by_me':
	$count = setting_fetch('tpp', 20);
	$request = 'comments/by_me';
        $tl = twitter_process($request, array('count'=>$count,'max_id'=>$_GET['max_id']));
        $tl = twitter_standard_timeline($tl->comments, 'cmts');
        $content = theme_cmts_menu();
        $content .= theme('timeline', $tl);
        theme('page', '评论', $content);

	case '':
	case 'to_me':
	$count = setting_fetch('tpp', 20)+4;
	$request = 'comments/to_me';
        $tl = twitter_process($request, array('count'=>$count,'max_id'=>$_GET['max_id']));
        $tl = twitter_standard_timeline($tl->comments, 'cmts');
        $content = theme_cmts_menu();
        $content .= theme('timeline', $tl);
        theme('page', '评论', $content);
		
	case 'mentions':
	$count = setting_fetch('tpp', 20)+4;
	$request = 'comments/mentions';
        $tl = twitter_process($request, array('count'=>$count,'max_id'=>$_GET['max_id']));
        $tl = twitter_standard_timeline($tl->comments, 'cmts');
        $content = theme_cmts_menu();
        $content .= theme('timeline', $tl);
        theme('page', '评论', $content);

    case 'reply': // reply comment
	$count = setting_fetch('tpp', 20);
        $rid = strtolower(trim($query[2]));
        $request = 'statuses/comments_by_me.json';
        $tl = twitter_process($request, array('count'=>$count,'max_id'=>$_GET['max_id']));
        $tl = twitter_standard_timeline($tl, 'cmts');
        $content = theme_cmts_menu();
        $content .= theme('timeline', $tl);
        theme('page', '评论', $content);

    default:
	$request = "comments/show";
    if ($query[2] == "0") {
        $request = "statuses/show";
		$status = twitter_process($request, array('id'=>$action));
		$content = theme('comments_status', $status);
        $content .= theme_comment_form($action);
    } else {
        $tl = twitter_process($request, array("id"=>$action, "page"=>1+$_GET['page']));
        $content = theme('comments_status', $tl->comments[0]->status);
        $content .= theme_comment_form($action);
        $tl = twitter_standard_timeline($tl->comments, 'cmts');
        $content .= theme('weibocomments', $tl);
    }
	theme('page', '评论', $content);
	}
}

function twitter_directs_page($query) {
	$action = strtolower(trim($query[1]));
	switch ($action) {
		case 'delete':
			$id = $query[2];
			if (!is_numeric($id)) return;
			$request = API_URL."direct_messages/destroy/$id.json";
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
			$request = API_URL.'direct_messages/new.json';
			twitter_process($request, array('screen_name' => $to, 'text' => $message), 'post');
			twitter_refresh('directs/sent');

		case 'sent':
			$request = API_URL.'direct_messages/sent.json?page='.intval($_GET['page']);
			$tl = twitter_standard_timeline(twitter_process($request), 'directs_sent');
			$content = theme_directs_menu();
			$content .= theme('timeline', $tl);
			theme('page', '已发送', $content);

		case 'inbox':
		default:
			$request = 'direct_messages';
			$tl = twitter_standard_timeline(twitter_process($request, $_GET), 'directs_inbox');
			$content = theme_directs_menu();
			$content .= theme('timeline', $tl);
			theme('page', '收件箱', $content);
	}
}

function theme_directs_menu() {
	return '<p><a href="directs/create">新私信</a> | <a href="directs/inbox">收件箱</a> | <a href="directs/sent">已发送</a></p>';
}

function theme_cmts_menu() {
	return '<p><a href="cmts/to_me">收到的评论</a> | <a href="cmts/by_me">发出的评论</a> | <a href="cmts/mentions">提到我的评论</a></p>';
}

function theme_directs_form($to) {
	if ($to) {

		if (friendship_exists($to) != 1)
		{
			$html_to = "<em>提示</em> <b>" . $to . "</b> 没有关注您，所以您不能发私信给 TA :-(<br/>";
		}
		$html_to .= "发私信给 <b>$to</b><input name='to' value='$to' type='hidden'>";
	} else {
		$html_to .= "收信人: <input name='to'><br />消息:";
	}
	$content = "<form action='directs/send' method='post'>$html_to<br><textarea name='message' style='width:90%; max-width: 400px;' rows='3' id='message'></textarea><br><input type='submit' value='Send'><span id='remaining'>140</span></form>";
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
	$request = 'search/topics';
	$tl = twitter_process($request, array('q'=>urlencode($search_query), 'page'=>$page));
	$tl = twitter_standard_timeline($tl->results, 'search');
	return $tl;
}

function twitter_user_page($query) {
	$screen_name = $query[1];
	if ($screen_name) {
		
		$user = twitter_user_info($screen_name);
		if (!user_is_current_user($user->screen_name)) {
			$status = "@{$user->screen_name} ";
		} else {
			$status = '';
		}
		$content = theme('status_form', $status);
		$content .= theme('user_header', $user);
		
		if (isset($user->status)) {
			$request = "statuses/user_timeline";
			$postdata = array("screen_name"=>$screen_name);
			$tl = twitter_process($request, array_merge($postdata,$_GET));
			$tl = twitter_standard_timeline($tl->statuses, 'user');
			$content .= theme('timeline', $tl);
		}
		theme('page', "用户 {$screen_name}", $content);
	} else {
		// TODO: user search screen
	}
}

function twitter_favourites_page($query) {
	$screen_name = $query[1];
	$count = setting_fetch('tpp', 20);
	
	if (!$screen_name) {
		user_ensure_authenticated();
		$screen_name = $GLOBALS['user']['screen_name'];
	}
	$request = "favorites";
	$tl = twitter_process($request, array('count'=>$count, 'page'=>$_GET['page']));
	$tl = twitter_standard_timeline($tl->favorites, 'favourites');
	$content = theme('status_form');
	$content .= theme('favourites', $tl);
	theme('page', '收藏', $content);
}

function twitter_mark_favourite_page($query) {
	$id = (string) $query[1];
	if (!is_numeric($id)) return;
	if ($query[0] == 'unfavourite') {
		$request = "favorites/destroy";
	} else {
		$request = "favorites/create";
	}
	twitter_process($request, array('id'=>$id),'post');
	twitter_refresh();
}

function twitter_home_page() {
	user_ensure_authenticated();

	$count = setting_fetch('tpp');
	$request = 'statuses/home_timeline';
	$postdata = array('count'=>$count,'page'=>$_GET['page'],'base_app'=>0,'feature'=>0);
	if ($_GET['max_id'])
	{
		$postdata = array_merge($postdata, array('max_id'=>$_GET['max_id']));
	}

	if ($_GET['since_id'])
	{
		$postdata = array_merge($postdata, array('since_id='=>$_GET['since_id']));
	}

	//echo $request;
    $tl = twitter_process($request, $postdata);
	$tl = twitter_standard_timeline($tl->statuses, 'friends');
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

function theme_status_form($text = '') {
	if (user_is_authenticated()) {
		return "<form method='post' action='update'><input name='status' value='{$text}' maxlength='140' /> <input type='submit' value='Update' /></form>";
	}
}

function theme_comment_form($in_reply_to_id, $text = '') {
	if (user_is_authenticated()) {
		return "<form method='post' action='twitter-comment'><input name='comment' value='{$text}' maxlength='140' /> <input name='id' value='{$in_reply_to_id}' type='hidden' /><input type='submit' value='Comment' /></form>";
	}
}

function theme_recomment_form($in_reply_to_id, $reply_id, $text = '') {
	if (user_is_authenticated()) {
		return "<form method='post' action='weibo-recomment'><input name='comment' value='{$text}' maxlength='140' /> <input name='id' value='{$in_reply_to_id}' type='hidden' /><input name='cid' value='{$reply_id}' type='hidden' /><input type='submit' value='回复' /></form>";
	}
}

function theme_comments_status($status) {
	$time_since = theme('status_time_link', $status, false);
	$parsed = twitter_parse_tags($status->text);
	$avatar = theme('avatar', $status->user->profile_image_url);

	$out = "<div class='timeline'>\n";
	$out .= " <div class='tweet odd'>\n";
	$out .= "	<span class='avatar'>$avatar</span>\n";
	$out .= "	<span class='status shift'><b><a href='user/{$status->user->screen_name}'>{$status->user->screen_name}</a></b> $time_since<br />$parsed</span>\n";
	$out .= " </div>\n";
	$out .= "</div>\n";
	if (user_is_current_user($status->user->screen_name)) {
		$out .= "<form action='delete/{$status->id}' method='post'><input type='submit' value='Delete without confirmation' /></form>";
	}
	return $out;
}

function theme_status($status) {
	$time_since = theme('status_time_link', $status);
	$parsed = twitter_parse_tags($status->text);
	$avatar = theme('avatar', $status->user->profile_image_url);

	$out = theme('status_form', "@{$status->user->screen_name} ");
	$out .= "<div class='timeline'>\n";
	$out .= " <div class='tweet odd'>\n";
	$out .= "	<span class='avatar'>$avatar</span>\n";
	$out .= "	<span class='status shift'><b><a href='user/{$status->user->screen_name}'>{$status->user->screen_name}</a></b> $time_since<br />$parsed</span>\n";
    
    if ($status->retweeted_status) {
        $source2 = $status->retweeted_status->source ? " from {$status->retweeted_status->source}" : '';
        $text = twitter_parse_tags($status->retweeted_status->text);
        if ($status->retweeted_status->deleted) {
            $text = "Original weibo is deleted. try <a target='_blank' href='https://freeweibo.com/weibo/{$status->retweeted_status->mid}'>freeweibo</a>.";
        }
        $row = "原文<br/> <b> <a href='user/{$status->retweeted_status->user->screen_name}'>{$status->retweeted_status->user->screen_name}</a></b> <br />{$text} <small>$source2</small>" ;
        $out.= $row; 
    }
    $out .= " </div>\n";
	$out .= "</div>\n";
	if (user_is_current_user($status->user->screen_name)) {
		$out .= "<form action='delete/{$status->id}' method='post'><input type='submit' value='Delete without confirmation' /></form>";
	}
	return $out;
}

function theme_retweet($status) {
	$text = "{$status->text}";
	$length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen($text);
	$from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));
	$content = "<!--p>Old style \"organic\" retweet:</p><form action='update' method='post'><input type='hidden' name='from' value='$from' /><textarea name='status' cols='50' rows='3' id='status'>$text</textarea><br><input type='submit' value='Retweet'><span id='remaining'>" . (140 - $length) ."</span></form-->";
	$content .= js_counter("status");	
	if($status->user->protected == 0){
		$content.="<br />repost comment<br /><form action='twitter-retweet/{$status->id}' method='post'>
<textarea name='status' cols='50' rows='3' id='status'></textarea><br>$text
<input type='hidden' name='from' value='$from' /><input type='submit' value='repost'></form>";
	}
	return $content;
}

function theme_comment($status) {
	$text = "@{$status->user->screen_name}: ";
	$length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen($text);
	$from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));
	$content = "<p>{$status->user->screen_name}:{$status->text}</p><form action='twitter-comment/{$status->id}' method='post'><input type='hidden' name='id' value='$status->id' /><input type='hidden' name='from' value='$from' /><textarea name='comment' cols='50' rows='3' id='comment'>$text</textarea><br><input type='submit' value='Comment'><span id='remaining'>" . (140 - $length) ."</span></form>";
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
		$out .= '<br /><strong>微博认证</strong>';
	}
	if ($user->protected == true) {
		$out .= '<br /><strong>Private/Protected Tweets</strong>';
	}
	$link = $link ? "<br />Link: ".$link : "";
	$out .= "
<br />个人简介：{$user->description}
{$link}
<br />所在位置：{$user->location}
<br />于{$date_joined}注册，每天约{$tweets_per_day}条微博。
</small>
<br />
{$user->statuses_count} 微博 |
<a href='followers/{$user->screen_name}'>{$user->followers_count} 粉丝</a> ";

	if ($user->following !== true) {
		$out .= "| <a href='follow/{$user->screen_name}'>加关注</a>";
	} else {
		$out .= " | <a href='unfollow/{$user->screen_name}'>取消关注</a>";
	}
	
	//We need to pass the User Name and the User ID.	The Name is presented in the UI, the ID is used in checking
	//$out.= " | <a href='confirm/block/{$user->screen_name}/{$user->id}'>屏蔽 | 取消屏蔽</a>";
	//$out .= " | <a href='confirm/spam/{$user->screen_name}/{$user->id}'>举报</a>";
	$out.= " | <a href='friends/{$user->screen_name}'>{$user->friends_count} 关注</a>
| <a href='favourites/{$user->screen_name}'>{$user->favourites_count} 收藏</a>
</td></table>";
// | <a href='directs/create/{$user->screen_name}'>发私信</a>
	return $out;
}

function theme_avatar($url, $force_large = false) {
	$size = $force_large ? 48 : 24;
	if (setting_fetch('avataro') !== 'yes') {
		return "<img class='shead' src='$url' height='$size' width='$size' />";
	} else {
		return '';
	}
}

function theme_status_time_link($status, $is_link = true) {
	$time = strtotime($status->created_at);
	if ($time > 0) {
		/*if (twitter_date('dmy') == twitter_date('dmy', $time)) {
			$out = format_interval(time() - $time, 1). '前';
		} else {*/
		if (setting_fetch('timestamp') == 'yes') {
			$out = twitter_date('n月d日 H:i:s', ($time + 60 * 60 * 8) );
		} else {
			$out = format_interval(time() - $time, 1). '前';
		}
	} else {
		$out = $status->created_at;
	}
	if ($is_link)
		$out = "<a href='status/{$status->id}' class='time'>$out</a>";
    else 
        $out = "<span class='time'>$out</span>";
	return $out;
}

function twitter_date($format, $timestamp = null) {
	if (!isset($timestamp)) {
		$timestamp = time();
	}
	return gmdate($format, $timestamp);
}

function twitter_standard_timeline($feed, $source) {
	$output = array();
	if (!is_array($feed) && $source != 'thread') return $output;
	switch ($source) {
		case 'friends':
			#if(FILE_IO) file_put_contents("/tmp/timeline.dump", var_export($feed, true));
			$retweeted_status_to_index = array();
			foreach ($feed as $idx => $status) if ($status->retweeted_status) {
				$retweeted_status_id = $status->retweeted_status->id;
				if (empty($retweeted_status_to_index[$retweeted_status_id])) {
					$retweeted_status_to_index[$retweeted_status_id]=array();
				}
				$retweeted_status_to_index[$retweeted_status_id][] = $idx;
			}
			#if(FILE_IO) file_put_contents("/tmp/retweeted_status_to_index.dump",var_export($retweeted_status_to_index, true));
			foreach ($retweeted_status_to_index as $retweeted_status_id => $list) {
				if (count($list) > 1) {
					$retweet_users = array();
					for($idx = 1; $idx<count($list); $idx+=1) {
						$retweet_users[] = "@".$feed[$list[$idx]]->user->screen_name;
						unset($feed[$list[$idx]]);
					}
					$feed[$list[0]]->text .= (" || " . implode(", " , array_unique($retweet_users)). " 也转发了 ||");
				}
			}
		case 'favourites':
		case 'public':
		case 'mentions':
		case 'user':
			foreach ($feed as $status) {
				$new = $status;
				$new->from = $new->user;
				unset($new->user);
				if ($source == 'favourites'){
					$output[(string) $new->status->id] = $new;
				}else{
					$output[(string) $new->id] = $new;
				}
			}
			return $output;
	 
		case 'cmts':
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
					'geo' => $status->geo,
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

		default:
			echo "<h1>$source</h1><pre>";
        debug_print_backtrace	();
			die();
	}
}

function preg_match_one($pattern, $subject, $flags = NULL) {
	preg_match($pattern, $subject, $matches, $flags);
	return trim($matches[1]);
}

function twitter_user_info($username = null) {
	if (!$username) {
        debug_print_backtrace	();
        exit;
	}
 
	#$username = urlencode($username); 
    $request = "users/show";
	$user = twitter_process($request, array("screen_name"=>$username));
	return $user;
}

function theme_weibocomments($feed)
{
	if (count($feed) == 0) return theme('no_tweets');
	$rows = array();
	$page = menu_current_page();
	$date_heading = false;
	$first=0;

	foreach ($feed as $status)
	{
		if ($first==0)
		{
			$since_id = $status->id;
			$first++;
		}
		else
		{
			$max_id =	$status->id;
			if ($status->original_id)
			{
				$max_id =	$status->original_id;
			}
		}
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
		if($status->status) { // comment
			$text = twitter_parse_tags($status->text);
			$srctext = twitter_parse_tags($status->status->text);
			if ($status->status->thumbnail_pic){
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$srctext .= "<br/> <a href='{$status->status->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$srctext .= "<br/> <a href='{$status->status->original_pic}' target=_blank><img src='{$status->status->thumbnail_pic}' /></a> <br />";
				}
			}
			$link = theme('status_time_link', $status, false);
			$actions = theme('action_icons', $status);
			$avatar = theme('avatar', $status->from->profile_image_url);
			$source2 = $status->status->source ? " from {$status->status->source}" : '';
			$row = array(
				"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link<br />{$text}",
			);
		}
		
		if ($page != 'user' && $avatar) {
			array_unshift($row, $avatar);
		}
		if ($page != 'mentions' && twitter_is_reply($status)) {
			$row = array('class' => 'reply', 'data' => $row);
		}
		$rows[] = $row;
	}
	$content = theme('table', array(), $rows, array('class' => 'timeline'));
	
		$links[] = "<a href='{$_GET['q']}?max_id=$max_id' accesskey='9'>Older</a> 9";
		$content .= '<p>'.implode(' | ', $links).'</p>';
	return $content;
}

function theme_favourites($feed)
{
	if (count($feed) == 0) return theme('no_tweets');
	$rows = array();
	$page = menu_current_page();
	$date_heading = false;
	$first=0;

	foreach ($feed as $status)
	{
		if ($first==0)
		{
			$since_id = $status->id;
			$first++;
		}
		else
		{
			$max_id =	$status->id;
			if ($status->original_id)
			{
				$max_id =	$status->original_id;
			}
		}
		$time = strtotime($status->favorited_time);
		if ($time > 0) {
			//中文星期
			$cweekday = array("星期日","星期一","星期二","星期三","星期四","星期五","星期六"); 
			$now = getdate(time()); 
			$cur_wday=$now['wday'];
			
			$date = twitter_date('收藏日期：Y年n月j日 '.date($cweekday[$cur_wday]).'  ', strtotime($status->favorited_time));
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
		if($status->status->retweeted_status) { // 转发
			$text = twitter_parse_tags($status->status->text);
			$srctext = twitter_parse_tags($status->status->retweeted_status->text);
			
			if ($status->status->retweeted_status->thumbnail_pic){//缩略图
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$srctext .= "<br/> <a href='{$status->status->retweeted_status->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$srctext .= "<br/> <a href='{$status->status->retweeted_status->original_pic}' target=_blank><img src='{$status->status->retweeted_status->thumbnail_pic}' /></a> <br />";
				}
			}
			
			if (setting_fetch('buttontime', 'yes') == 'yes') {//时间
				$link = theme('status_time_link', $status->status, !$status->is_direct);
				$link2 = theme('status_time_link', $status->status->retweeted_status, !$status->is_direct);
			}
			$actions = theme('action_icons', $status->status);
			
			//头像
			$avatar = theme('avatar', $status->status->user->profile_image_url);
			$avatar_retweeted = theme('avatar', $status->status->retweeted_status->user->profile_image_url);
			if (setting_fetch('buttonfrom', 'yes') == 'yes') {//客户端
				if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
					$source = $status->status->source ? " 来自 {$status->status->source}" : '';
					$source2 = $status->status->retweeted_status->source ? " 来自 {$status->status->retweeted_status->source}" : '';
				}else{
					$source = $status->status->source ? " 来自 ".strip_tags($status->status->source) ."" : '';
					$source2 = $status->status->retweeted_status->source ? " 来自 ".strip_tags($status->status->retweeted_status->source)."" : '';
				}
			}
			$row = array(
				"<b><a href='user/{$status->status->user->screen_name}'>{$status->status->user->screen_name}</a></b> $actions $link <small>$source</small><br />{$text} <br /> <blockquote style='margin: 10px 20px;'>$avatar_retweeted<b> <a href='user/{$status->status->retweeted_status->user->screen_name}'>{$status->status->retweeted_status->user->screen_name}</a></b> $link2 <br />{$srctext} <small>$source2</small></blockquote>",
			);
			//print_r($row);	
		}else{ // 原创
			$text = twitter_parse_tags($status->status->text);
			
			if ($status->status->thumbnail_pic){//缩略图
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$text .= "<br/> <a href='{$status->status->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$text .= "<br/> <a href='{$status->status->original_pic}' target=_blank><img src='{$status->status->thumbnail_pic}' /></a> <br />";
				}
			}
			
			if (setting_fetch('buttontime', 'yes') == 'yes') {//时间
				$link = theme('status_time_link', $status->status, !$status->is_direct);
			}
			$actions = theme('action_icons', $status->status);
			
			//头像
			$avatar = theme('avatar', $status->status->user->profile_image_url);
			$avatar_retweeted = theme('avatar', $status->status->retweeted_status->user->profile_image_url);
			if (setting_fetch('buttonfrom', 'yes') == 'yes') {//客户端
				if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
					$source = $status->status->source ? " 来自 {$status->status->source}" : '';
					$source2 = $status->status->retweeted_status->source ? " 来自 {$status->status->retweeted_status->source}" : '';
				}else{
					$source = $status->status->source ? " 来自 ".strip_tags($status->status->source) ."" : '';
					$source2 = $status->status->retweeted_status->source ? " 来自 ".strip_tags($status->status->retweeted_status->source)."" : '';
				}
			}
			$row = array(
				"<b><a href='user/{$status->status->user->screen_name}'>{$status->status->user->screen_name}</a></b> $actions $link <br />{$text} <br /><small>$source</small>",
			);
			//print_r($row);	
		}

		if ($page != 'user' && $avatar) {
			array_unshift($row, $avatar);
		}
		$rows[] = $row;
	}
	$content = theme('table', array(), $rows, array('class' => 'timeline'));
	$content .= theme('pagination');
	return $content;
}

function theme_timeline($feed)
{
	if (count($feed) == 0) return theme('no_tweets');
	$rows = array();
	$page = menu_current_page();
	$date_heading = false;
	$first=0;

	foreach ($feed as $status)
	{
		if ($first==0)
		{
			$since_id = $status->id;
			$first++;
		}
		else
		{
			$max_id =	$status->id;
			if ($status->original_id)
			{
				$max_id =	$status->original_id;
			}
		}
		$time = strtotime($status->created_at);
		if ($time > 0) {
			//中文星期
			$cweekday = array("星期日","星期一","星期二","星期三","星期四","星期五","星期六"); 
			$now = getdate(time()); 
			$cur_wday=$now['wday'];
			
			$date = twitter_date('Y年n月j日 '.date($cweekday[$cur_wday]).'  ', strtotime($status->created_at));
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
		if($status->status) { // comment
			$text = twitter_parse_tags($status->text);
			$srctext = twitter_parse_tags($status->status->text);
			$replytext = twitter_parse_tags($status->reply_comment->text);
			$retweetedtext = twitter_parse_tags($status->status->retweeted_status->text);
			if ($status->status->thumbnail_pic){
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$srctext .= "<br/> <a href='{$status->status->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$srctext .= "<br/> <a href='{$status->status->original_pic}' target=_blank><img src='{$status->status->thumbnail_pic}' /></a> <br />";
				}
			}
			if ($status->reply_comment->thumbnail_pic){
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$replytext .= "<br/> <a href='{$status->reply_comment->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$replytext .= "<br/> <a href='{$status->reply_comment->original_pic}' target=_blank><img src='{$status->reply_comment->thumbnail_pic}' /></a> <br />";
				}
			}
			if ($status->status->retweeted_status->thumbnail_pic){
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$retweetedtext .= "<br/> <a href='{$status->status->retweeted_status->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$retweetedtext .= "<br/> <a href='{$status->status->retweeted_status->original_pic}' target=_blank><img src='{$status->status->retweeted_status->thumbnail_pic}' /></a> <br />";
				}
			}
			
			if (setting_fetch('buttontime', 'yes') == 'yes') {//时间
				$link = theme('status_time_link', $status, false);
				$link2 = theme('status_time_link', $status->status, !$status->is_direct);
				$link_reply = theme('status_time_link', $status->reply_comment, !$status->is_direct);
				$link_retweeted = theme('status_time_link', $status->status->retweeted_status, !$status->is_direct);
			}
			
			//按钮
			$actions = theme('action_icons', $status);
			$actions2 = theme('action_icons', $status->status);
			$actions_reply = theme('action_icons_reply', $status);
			$actions_retweeted = theme('action_icons', $status->status->retweeted_status);
			
			//头像
			$avatar = theme('avatar', $status->from->profile_image_url);
			$avatar_comment = theme('avatar', $status->status->user->profile_image_url);
			$avatar_reply = theme('avatar', $status->reply_comment->user->profile_image_url);
			$avatar_retweeted = theme('avatar', $status->status->retweeted_status->user->profile_image_url);
			
			if (setting_fetch('buttonfrom', 'yes') == 'yes') {//客户端
				if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
					$source = $status->source ? " 来自 {$status->source}" : '';
					$source2 = $status->status->source ? " 来自 {$status->status->source}" : '';
					$source_reply = $status->reply_comment->source ? " 来自 {$status->reply_comment->source}" : '';
					$source_retweeted = $status->status->retweeted_status->source ? " 来自 {$status->status->retweeted_status->source}" : '';
				}else{
					$source = $status->source ? " 来自 ".strip_tags($status->source)."" : '';
					$source2 = $status->status->source ? " 来自 ".strip_tags($status->status->source)."" : '';
					$source_reply = $status->reply_comment->source ? " 来自 ".strip_tags($status->reply_comment->source)."" : '';
					$source_retweeted = $status->status->retweeted_status->source ? " 来自 ".strip_tags($status->status->retweeted_status->source)."" : '';
				}
			}
			if ($status->reply_comment){
			$row = array(
				"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link <small>$source</small><br />{$text} <br /> 
				$avatar_reply<b><a href='user/{$status->reply_comment->user->screen_name}'>{$status->reply_comment->user->screen_name}</a></b> $actions_reply $link_reply <small>$source_reply</small><br />{$replytext} <br /> 
				<blockquote style='margin: 10px 20px;'>原博：<br />$avatar_comment<b> <a href='user/{$status->status->user->screen_name}'>{$status->status->user->screen_name}</a></b> $actions2 $link2 <small>$source2</small><br />{$srctext} </blockquote>",
			);
			}elseif($status->status->retweeted_status){//提到的带转发评论
			$row = array(
				"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link <small>$source</small><br />{$text} <br /> 
				<blockquote style='margin: 10px 20px;'>$avatar_comment<b> <a href='user/{$status->status->user->screen_name}'>{$status->status->user->screen_name}</a></b> $actions2 $link2 <small>$source2</small><br />{$srctext} 
				<blockquote style='margin: 10px 20px;'>转发：<br />$avatar_retweeted<b> <a href='user/{$status->status->retweeted_status->user->screen_name}'>{$status->status->retweeted_status->user->screen_name}</a></b> $actions_retweeted $link_retweeted <small>$source_retweeted</small><br />{$retweetedtext} </blockquote>
				</blockquote>",
			);
			}else{
			$row = array(
				"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link <small>$source</small><br />{$text} <br /> 
				<blockquote style='margin: 10px 20px;'>$avatar_comment<b> <a href='user/{$status->status->user->screen_name}'>{$status->status->user->screen_name}</a></b> $actions2 $link2 <small>$source2</small><br />{$srctext} </blockquote>",
			);
			}
		}elseif($status->retweeted_status){
			//$avatar = theme('avatar',$status->retweeted_status->user->profile_image_url);
			$avatar = theme('avatar', $status->from->profile_image_url);
			$avatar_retweeted = theme('avatar', $status->retweeted_status->user->profile_image_url);
			if (setting_fetch('buttontime', 'yes') == 'yes') {//时间
				$link = theme('status_time_link', $status, !$status->is_direct);
				$link2 = theme('status_time_link', $status->retweeted_status, !$status->is_direct);
			}
			$actions = theme('action_icons', $status);
			$actions2 = theme('action_icons', $status->retweeted_status);

			$reason = twitter_parse_tags($status->text);
			$text = twitter_parse_tags($status->retweeted_status->text);
			if ($status->retweeted_status->thumbnail_pic){
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$text .= "<br/> <a href='{$status->retweeted_status->original_pic}' target=_blank>[图片]</a> <br />";
				}else{
					$text .= "<br/> <a href='{$status->retweeted_status->original_pic}' target=_blank><img src='{$status->retweeted_status->thumbnail_pic}' /></a> <br />";
				}
			}
            if ($status->retweeted_status->deleted) {
                $text = "原微博已被删除，试试 <a target='_blank' href='https://freeweibo.com/weibo/{$status->retweeted_status->mid}'>freeweibo</a> 吧。";
            }
			if (setting_fetch('buttonfrom', 'yes') == 'yes') {//客户端
				if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
					$source = $status->source ? " 来自 {$status->source}" : '';
					$source2 = $status->retweeted_status->source ? " 来自 {$status->retweeted_status->source}" : '';
				}else{
					$source = $status->source ? " 来自 ".strip_tags($status->source) ."" : '';
					$source2 = $status->retweeted_status->source ? " 来自 ".strip_tags($status->retweeted_status->source) ."" : '';
				}
			}
			$row = array(
				"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link <br /> $reason <br /><small>$source</small> <br /> <blockquote style='margin: 10px 20px;'>$avatar_retweeted<b> <a href='user/{$status->retweeted_status->user->screen_name}'>{$status->retweeted_status->user->screen_name}</a></b> $actions2 $link2<br />{$text} <small>$source2</small></blockquote>",
			);
		}
		else{
			$text = twitter_parse_tags($status->text);
			if ($status->thumbnail_pic){
				if ((setting_fetch('piclink', 'yes') == 'yes') || (setting_fetch('browser') == 'text')) {
					$text .= "<br/> <a href='$status->original_pic' target=_blank>[图片]</a> <br />";
				}else{
					$text .= "<br/> <a href='$status->original_pic' target=_blank><img src='$status->thumbnail_pic' /></a> <br />";
				}
			}
			if (setting_fetch('buttontime', 'yes') == 'yes') {//时间
				$link = theme('status_time_link', $status, !$status->is_direct);
			}
			$actions = theme('action_icons', $status);
			$avatar = theme('avatar', $status->from->profile_image_url);
			if (setting_fetch('buttonfrom', 'yes') == 'yes') {//客户端
				if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'naiping')) {
					$source = $status->source ? "来自 {$status->source}" : '';
				}else{
					$source = $status->source ? " 来自 ".strip_tags($status->source) ."" : '';
				}
			} else {
				$source = NULL;
			}
			$row = array(
				"<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link<br />{$text} <small>$source</small>",
			);
		}

		if ($page != 'user' && $avatar) {
			array_unshift($row, $avatar);
		}
		if ($page != 'mentions' && twitter_is_reply($status)) {
			$row = array('class' => 'reply', 'data' => $row);
		}
		$rows[] = $row;
	}
	$content = theme('table', array(), $rows, array('class' => 'timeline'));
	//$content .= theme('pagination');
	$links[] = "<a href='{$_GET['q']}?max_id=".number_format($max_id,0,'','')."' accesskey='9'>更旧的</a> 9";
	$content .= '<p>'.implode(' | ', $links).'</p>';
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
	if (count($feed) == 0 || $feed == '[]') return '<p>No users to display.</p>';
	if(is_array($feed->users)){
		foreach ($feed->users as $user) {
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
			"{$user->statuses_count} 条微博 | 关注 {$user->friends_count} 人 | 粉丝 {$user->followers_count} 人 | 每天约 {$tweets_per_day} 条微博</small>"
		);
		}
	}else{
		return '<p>因为微博接口限制，无法显示的内容</p>';
	}
	$content = theme('table', array(), $rows, array('class' => 'followers'));
	#if(FILE_IO) file_put_contents('/tmp/urls', $feed->previous_cursor.":". $feed->next_cursor."\n", FILE_APPEND);
	if (!$hide_pagination)
		$content .= theme('cursor', $feed->previous_cursor, $feed->next_cursor);
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
	return '<p>因为微博接口限制，没有可显示的微博</p>';
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
	return "<form action='search' method='get'><input name='query' value=\"$query\" /><input type='submit' value='Search' /></form>";
}

function theme_external_link($url, $content = null) {
	//Long URL functionality.	Also uncomment function long_url($shortURL)
		if (strlen($url) <= 8) return "";
	if (!$content) 
	{
		return "<a href='$url' target='_blank'>".long_url($url)."</a>";
	}
	else
	{
		return "<a href='$url' target='_blank'>$content</a>";
	}

}

function theme_pagination() {
	$page = intval($_GET['page']);
	if (preg_match('#&q(.*)#', $_SERVER['QUERY_STRING'], $matches)) {
		$query = $matches[0];
	}
	if ($page == 0) $page = 1;
	$links[] = "<a href='{$_GET['q']}?page=".($page+1)."$query' accesskey='9'>下一页</a> 9";
	if ($page > 1) $links[] = "<a href='{$_GET['q']}?page=".($page-1)."$query' accesskey='8'>上一页</a> 8";
	return '<p>'.implode(' | ', $links).'</p>';
}

function theme_cursor($prev, $next) {
	if (preg_match('#&q(.*)#', $_SERVER['QUERY_STRING'], $matches)) {
		$query = $matches[0];
	}
	if ($prev and ($prev == $_GET["cursor"])) $prev -= 20;
    $links = array();
	if ($prev) $links[] = "<a href='{$_GET['q']}?cursor=".($prev)."$query' accesskey='9'>上一页</a> 9";
	if ($next) $links[] = "<a href='{$_GET['q']}?cursor=".($next)."$query' accesskey='8'>下一页</a> 8";
	return '<p>'.implode(' | ', $links).'</p>';
}

function theme_action_icons_reply($status) {
	$from = $status->reply_comment->user->screen_name;
	$retweeted_by = $status->retweeted_by->user->screen_name;
	$retweeted_id = $status->retweeted_by->id;
	$geo = $status->geo;
	$actions = array();

	if (!$status->is_direct) {
		
	if ($status->reply_comment) {
		$actions[] = theme('action_icon', "recomment/".number_format($status->status->id,0,'','')."/".number_format($status->reply_comment->id,0,'',''), 'images/comments.gif', 'CMS');
	}

	if (setting_fetch('buttondel', 'yes') == 'yes') {
		if (user_is_current_user($from)) {
			$actions[] = theme('action_icon', "confirm/delcmt/".number_format($status->reply_comment->id,0,'',''), 'images/trash.gif', 'DEL');
		}
	}
	} else {
		$actions[] = theme('action_icon', "directs/delete/".number_format($status->id,0,'',''), 'images/trash.gif', 'DEL');
	}

	return implode(' ', $actions);
}

function theme_action_icons($status) {
	$from = $status->from->screen_name;
	$from2 = $status->user->screen_name;
	$retweeted_by = $status->retweeted_by->user->screen_name;
	$retweeted_id = $status->retweeted_by->id;
	$geo = $status->geo;
	$actions = array();


	/*
	if (!user_is_current_user($from)) {
		$actions[] = theme('action_icon', "directs/create/{$from}", 'images/dm.png', 'DM');
	}*/
	if (!$status->is_direct) {
		
	if (!$status->status) {
		if (setting_fetch('buttonfav', 'yes') == 'yes') {
			if ($status->favorited == '1') {
				$actions[] = theme('action_icon', "unfavourite/".number_format($status->id,0,'',''), 'images/star.png', '取消收藏');
			} else {
				$actions[] = theme('action_icon', "favourite/".number_format($status->id,0,'',''), 'images/star_grey.png', '收藏');
			}
		}
		if (setting_fetch('buttonrt', 'yes') == 'yes') {
			$actions[] = theme('action_icon', "retweet/".number_format($status->id,0,'',''), 'images/retweet.png', '转发');
		}
		if (setting_fetch('buttonco', 'yes') == 'yes') {
			$actions[] = theme('action_icon', "cmts/".number_format($status->id,0,'','')."/".number_format($status->comments_count), 'images/list.png', '评论')."<span class='time'>{$status->comments_count}</span> ";
		}
	} else {
		$actions[] = theme('action_icon', "recomment/".number_format($status->status->id,0,'','')."/".number_format($status->id,0,'',''), 'images/comments.gif', 'CMS');
	}
	
	if (setting_fetch('buttondel', 'yes') == 'yes') {
		if (user_is_current_user($from)) {
			if ($status->status) {
				$actions[] = theme('action_icon', "confirm/delcmt/".number_format($status->id,0,'',''), 'images/trash.gif', 'DEL');
			}else{
				$actions[] = theme('action_icon', "confirm/delete/".number_format($status->id,0,'',''), 'images/trash.gif', 'DEL');
			}
		}elseif (user_is_current_user($from2)) {
			$actions[] = theme('action_icon', "confirm/delete/".number_format($status->id,0,'',''), 'images/trash.gif', 'DEL');
		}
	}

	} else {
		$actions[] = theme('action_icon', "directs/delete/".number_format($status->id,0,'',''), 'images/trash.gif', 'DEL');
	}
	if ($geo !== null)
	{
		$latlong = $geo->coordinates;
		$lat = $latlong[0];
		$long = $latlong[1];
		$actions[] = theme('action_icon', "http://maps.google.com/maps?q={$lat},{$long}", 'images/map.png', 'MAP');
	}
	//Search for @ to a user
	//$actions[] = theme('action_icon',"search?query=%40{$from}",'images/q.png','?');

	return implode(' ', $actions);
}

function theme_action_icon($url, $image_url, $text) {
	// alt attribute left off to reduce bandwidth by about 720 bytes per page
	if ($text == 'MAP')
	{
		return "<a href='$url' alt='$text' target='_blank'><img src='$image_url' /></a>";
	}

	return "<a href='$url'><img src='$image_url' alt='$text' /></a>";
}

function pluralise($word, $count, $show = FALSE) {
	if($show) $word = "{$count} {$word}";
	return $word . (($count != 1) ? 's' : '');
}
