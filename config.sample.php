<?php

//��վ����
define('SINA_TITLE', 'Dabr for Weibo');

//��վ����UID������ע����������
define('REGUID', '');

// Weibo API��ַ
define('API_URL','http://api.weibo.com');

// Cookie�����ܳף����52�ַ�
define('ENCRYPTION_KEY', 'Example Key - Change Me!');

// OAuth��Ȩʹ�õ�App Key��App Secret
// ע��������Ȩ�ص�ҳҪ����Ϊhttp://dabr·��/oauth���������ְ󶨴���
define('OAUTH_CONSUMER_KEY', '');
define('OAUTH_CONSUMER_SECRET', '');

// bit.ly LOGIN��API key������url����
define('BITLY_LOGIN', '');
define('BITLY_API_KEY', '');

// ��ѡ������ͼ����API keys
define('FLICKR_API_KEY', '');

// Optional: Allows you to turn shortened URLs into long URLs http://www.longurlplease.com/docs
// Uncomment to enable.
// define('LONGURL_KEY', 'true');

// ��ѡ�����ÿɲ鿴ҳ��������̺�API��Ӧʱ��
define('DEBUG_MODE', 'OFF');

// Base URL, should point to your website, including a trailing slash
// Can be set manually but the following code tries to work it out automatically.
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
if ($directory = trim(dirname($_SERVER['SCRIPT_NAME']), '/\,')) {
  $base_url .= '/'.$directory;
}
define('BASE_URL', $base_url.'/');



// MySQL storage of OAuth login details for users
define('MYSQL_USERS', 'OFF');
// mysql_connect('localhost', 'username', 'password');
// mysql_select_db('dabr');

/* The following table is needed to store user login details if you enable MYSQL_USERS:

CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(64) NOT NULL,
  `oauth_key` varchar(128) NOT NULL,
  `oauth_secret` varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`username`)
)

*/

// Google Analytics Mobile tracking code
// You need to download ga.php from the Google Analytics website for this to work
// Copyright 2009 Google Inc. All Rights Reserved.
$GA_ACCOUNT = "";
$GA_PIXEL = "ga.php";

function googleAnalyticsGetImageUrl() {
	global $GA_ACCOUNT, $GA_PIXEL;
	$url = "";
	$url .= $GA_PIXEL . "?";
	$url .= "utmac=" . $GA_ACCOUNT;
	$url .= "&utmn=" . rand(0, 0x7fffffff);
	$referer = $_SERVER["HTTP_REFERER"];
	$query = $_SERVER["QUERY_STRING"];
	$path = $_SERVER["REQUEST_URI"];
	if (empty($referer)) {
		$referer = "-";
	}
	$url .= "&utmr=" . urlencode($referer);
	if (!empty($path)) {
		$url .= "&utmp=" . urlencode($path);
	}
	$url .= "&guid=ON";
	return str_replace("&", "&amp;", $url);
}

if (stristr($_SERVER["HTTP_HOST"], "sinaapp.com") === "sinaapp.com") {
    $uri = $_SERVER["REQUEST_URI"];
    list($action, $param) = explode("?", $uri);
    if ($param) {
        parse_str($param, $_get);
        foreach ($_get as $k => $v) if (!isset($_GET[$k])) $_GET[$k]=$v;
    }
}
