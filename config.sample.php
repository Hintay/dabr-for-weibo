<?php
//请根据注释修改，大部分请修改putyourinfohere

//网站名称
define('DABR_TITLE', 'Dabr for Weibo');

//网站主的UID，用于注册链接邀请
define('REGUID', '1061630973');

// Weibo API地址
define('API_URL','http://api.weibo.com');

//邀请模式，0为开放模式，1为邀请模式
define('INVITE', 0);

//邀请码
define('INVITE_CODE', 'putyourinfohere');

// Cookie加密密匙，最大52字符
define('ENCRYPTION_KEY', 'Example Key - Change Me!');

// OAuth授权使用的App Key和App Secret
// 注意您的授权回调页要设置为http://dabr路径/oauth，否则会出现绑定错误
define('OAUTH_KEY', 'putyourinfohere');
define('OAUTH_SECRET', 'putyourinfohere');

//已有高级接口设定，1为启用。高级接口需要您自己向新浪申请
//高级微博读取接口statuses/timeline_batch，用于读取非登录用户本人的其他用户时间线
define('API_TLBATCH', 0);
//高级提醒写入接口remind/set_count，用于对当前登录用户某一种消息未读数进行清零
define('API_RMSC', 0);

//Google Analytics 跟踪 ID，类似于UA-19890535-X
define('GA_ACCOUNT', 'putyourinfohere');
$GA_PIXEL = "ga.php";

// bit.ly LOGIN及API key，用于url缩短
define('BITLY_LOGIN', '');
define('BITLY_API_KEY', '');

// 可选：缩略图生成API keys
define('FLICKR_API_KEY', '');

// Optional: Allows you to turn shortened URLs into long URLs http://www.longurlplease.com/docs
// Uncomment to enable.
// define('LONGURL_KEY', 'true');

// 可选：启用可查看页面载入进程和API响应时间
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

if (stristr($_SERVER["HTTP_HOST"], "sinaapp.com") === "sinaapp.com") {
    $uri = $_SERVER["REQUEST_URI"];
    list($action, $param) = explode("?", $uri);
    if ($param) {
        parse_str($param, $_get);
        foreach ($_get as $k => $v) if (!isset($_GET[$k])) $_GET[$k]=$v;
    }
}
