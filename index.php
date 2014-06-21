<?php
if (!file_exists('config.php')) {
	$root .= ($directory = trim(dirname($_SERVER["SCRIPT_NAME"]), "/\,")) ? "/$directory/" : "/";

	header('Location: '.$root.'setup.php');
	exit;
}

$dabr_start = microtime(1);

session_start();

require('languages/languages.php');
require('config.php');
require('common/OAuth.php');
require('common/browser.php');
require('common/menu.php');
require('common/user.php');
require('common/theme.php');
require('common/twitter.php');
#require('common/lists.php');
require('common/settings.php');

menu_register(array (
	'about' => array (
		'callback' => 'about_page',
		'title' => __("About"),
	),
	'logout' => array (
		'security' => true,
		'callback' => 'logout_page',
		'title' => __("Logout"),
	),
));

function logout_page() {
	user_logout();
	$content = theme('logged_out');
	theme('page', __("Logged out"), $content);
}

function about_page() {
	$content = file_get_contents('about.html');
	theme('page', __("About"), $content);
}

browser_detect();
menu_execute_active_handler();
?>
