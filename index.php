<?php
$dabr_start = microtime(1);

session_start();

require 'config.php';
require 'common/OAuth.php';
require 'common/browser.php';
require 'common/menu.php';
require 'common/user.php';
require 'common/theme.php';
require 'common/twitter.php';
#require 'common/lists.php';
require 'common/settings.php';

menu_register(array (
	'about' => array (
		'callback' => 'about_page',
	),
	'logout' => array (
		'security' => true,
		'callback' => 'logout_page',
		'title' => '登出',
	),
));

function logout_page() {
	user_logout();
	$content = theme('logged_out');
	theme('page', '登出', $content);
}

function about_page() {
	$content = file_get_contents('about.html');
	theme('page', '关于', $content);
}

browser_detect();
menu_execute_active_handler();
?>
