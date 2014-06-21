<?php
header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL ^ E_WARNING);

function is_https() {
	if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1)) {
		return TRUE;
	}

	// Nginx 专用方法检测
	if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
		return TRUE;
	}

	return FALSE;
}

function available($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return $code < 400;
}

function display_header($n) {
	$content = '<!doctype html><meta charset=utf-8 /><title>安装向导 - Dabr for Weibo</title><link rel="stylesheet" href="images/setup.css" type="text/css" /><h1 id="logo">Dabr for Weibo</h1>';

	if ($n != '') {
		$content .= '<div id="error">'.$n.'</div>';
	}

	echo $content;
}

$base_url = is_https() ? 'https' : 'http';
$base_url .= '://'.$_SERVER['HTTP_HOST'];

if ($directory = trim(dirname($_SERVER['SCRIPT_NAME']), '/\,')){
	$base_url .= '/'.$directory;
}

define('BASE_URL', $base_url.'/');
define('ABSPATH', dirname(__FILE__).'/');

$notice = '';

if (!function_exists("curl_init")) {
	$notice = '<strong>提示：</strong>服务器不支持 cURL 函数，Dabr for Weibo将无法使用。';
} elseif (!available(BASE_URL.'settings')) {
	$notice = '<strong>提示：</strong>服务器不支持 URL Rewrite ，Dabr for Weibo将无法使用。';
} elseif (file_exists(ABSPATH.'config.php')) {
	$notice = '<strong>提示：</strong> config.php 文件已存在。如果您想更改 config.php 内已有的设定，请先删除它，本向导会重新创建 config.php 。<a href="setup.php">重试</a>。';
} elseif (!file_exists(ABSPATH.'config.sample.php')) {
	$notice = '<strong>提示：</strong>未能检测到 config.sample.php 文件。请确认该目录存在此文件或重新上传。';
} elseif (!is_writable(ABSPATH)) {
	$notice = '<strong>提示：</strong>目录不可写。请更改目录属性或者手动创建 config.php （参考 config.sample.php）和 invited 文件（内容为空）。';
}

$step = isset($_GET['step']) ? $_GET['step'] : 0;

switch($step) {
	case 0:
		display_header($notice);
		$content = '<p><strong>欢迎使用「Dabr for Weibo」！</strong></p>
<p><a href="https://github.com/Hintay/dabr-for-weibo">Dabr for Weibo</a> 是第三方微博网页客户端（更适合在移动设备上使用）。本项目以 <a href="http://dabr.co.uk">Dabr</a> (By <a href="https://twitter.com/davidcarrington">@davidcarrington</a>) 为基础，在<a href="http://www.weibo.com/liruqi">@liruqi</a> 的项目 <a href="https://github.com/liruqi/dabr-for-weibo"> dabr-for-weibo</a> 进行衍生修改，同时也感谢最先提交到open.t.sina.cn上的  <a href="http://timyang.net/">Tim Yang</a> 和奶瓶腿的作者<a href="http://weibo.com/NetPuter">@NetPuter</a>。<br />本页面基于<a href="https://github.com/netputer/netputweets">奶瓶腿</a>项目。</p>
<p>如果你关注 <a href="http://www.kugeek.com">Hintay</a> 和他折腾的一些项目，并且希望帮助他，欢迎以捐助的形式使他更好地折腾。</p>
<p>在正式使用之前，你可能需要填写一些信息，包括：</p>
<ol>
	<li>微博开放平台应用App Key & App Secret <a href="http://open.weibo.com/apps" title="申请地址">#</a></li>
</ol>
<p><strong>如果无法进入下一步，别着急。此向导的目的在于创建「Dabr for Weibo」的配置文件，所以您还可以直接用文本编辑器打开 <code>config.sample.php</code> ，根据提示填写相应信息，然后保存并将它重命名为 <code>config.php</code> 。同时创建 invited 文件（内容为空）。</strong></p>
<p>建议在安装前仔细阅读<a href="https://github.com/Hintay/dabr-for-weibo/blob/master/README.md">说明文档</a>，如果还有不明白的地方，可到 <a href="https://github.com/Hintay/dabr-for-weibo/issues/new">GitHub Issues</a> 提出。
<p>如果已经准备好了 &hellip; &hellip;</p>';

		if ($notice != '') {
			$content .= '<p class="step"><a href="setup.php" class="button">还不能开始！</a></p>';
		} else {
			$content .= '<p class="step"><a href="setup.php?step=1" class="button">现在就开始吧！</a></p>';
		}

		echo $content;
		break;

	case 1:
		display_header($notice);
		$content = '<form method="post" action="setup.php?step=2">
	<p>请在下面的表单中填入对应的信息。</p>
	<table class="form-table">
		<tr><th scope="row"><label for="t_title">网站名称</label></th><td><input name="t_title" id="t_title" type="text" value="Dabr for Weibo" size="35" /></td><td>如「M78星云」</td></tr>
		<tr><th scope="row"><label for="t_api">Weibo API地址</label></th><td><input name="t_api" id="t_api" type="text" size="35" value="http://api.weibo.com" /></td><td>一般无需修改</td></tr>
		<tr><th scope="row"><label for="t_tck">Weibo App Key <a href="http://open.weibo.com/apps" title="申请地址">#</a></label></th><td><input name="t_tck" id="t_tck" type="text" size="35" value="" /></td><td>必须填写</td></tr>
		<tr><th scope="row"><label for="t_tcs">Weibo App Secret <a href="http://open.weibo.com/apps" title="申请地址">#</a></label></th><td><input name="t_tcs" id="t_tcs" type="text" size="35" value="" /></td><td>必须填写</td></tr>
		<tr><th scope="row"><label for="t_uid">站长Weibo UID</label></th><td><input name="t_uid" id="t_uid" type="text" size="35" value="1061630973" /></td><td>可选</td></tr>
		<tr><th scope="row"><label for="t_ivt">仅受邀用户可登录</label></th><td><select name="t_ivt"><option value="1">开启</option><option selected="selected" value="0">停用</option></select></td><td>请根据您的需要选择</td></tr>
		<tr><th scope="row"><label for="t_psw">设置邀请码</label></th><td><input name="t_psw" id="t_psw" type="text" value="weibo" size="35" /></td><td>仅当「仅受邀用户可登录」开启时生效，用于「 <a href="invite.php">invite.php</a> 」</td></tr>
		<tr><th scope="row"><label for="t_gac">Google Analytics 跟踪 ID</label></th><td><input name="t_gac" id="t_gac" type="text" value="" size="35" /></td><td>如「UA-19890535-X」，不需要请留空（填写后请放入ga.php至dabr的目录）</td></tr>
		<tr><th scope="row"><label for="t_tlb">请选择您已申请到的高级接口</label></th><td><input type="checkbox" name="t_tlb" value="1" />statuses/timeline_batch <a href="#" title="用于查在他人用户页面查看时间线">[?]</a><br/><input type="checkbox" name="t_rsc" value="1" />remind/set_count <a href="#" title="用于对当前登录用户某一种消息未读数进行清零">[?]</a></td><td>因为微博限制，部分功能需要申请高级接口才可使用</td></tr></table>';

		if ($notice !== '') {
			$content .= '<p class="step"><a href="setup.php" class="button">出错了！</a></p>';
		} else {
			$content .= '<p class="step"><input name="submit" type="submit" value="填好了！" class="button" /></p>';
		}

		$content .= '</form>';

		echo $content;
		break;

	case 2:
		if (!isset($_POST['submit'])) {
			header('location: index.php');
			exit;
		}

		display_header($notice);

		$t_title = !empty($_POST['t_title']) ? trim($_POST['t_title']) : 'Dabr for Weibo';
		$t_uid = !empty($_POST['t_uid']) ? trim($_POST['t_uid']) : '1061630973';
		$t_tck = trim($_POST['t_tck']);
		$t_tcs = trim($_POST['t_tcs']);
		$t_ipc = !empty($_POST['t_ipc']) ? trim($_POST['t_ipc']) : '0';
		$t_ivt = trim($_POST['t_ivt']);
		$t_psw = !empty($_POST['t_psw']) ? trim($_POST['t_psw']) : 'weibo';
		$t_gac = trim($_POST['t_gac']);
		$t_api = !empty($_POST['t_api']) ? trim($_POST['t_api']) : 'http://api.weibo.com';
		$t_tlb = !empty($_POST['t_tlb']) ? trim($_POST['t_tlb']) : '0';
		$t_rsc = !empty($_POST['t_rsc']) ? trim($_POST['t_rsc']) : '0';

		if ($notice == '') {
			$config = file(ABSPATH . 'config.sample.php');
			$handle = fopen(ABSPATH . 'config.php', 'w');

			foreach ($config as $line_num => $line) {
				switch (substr($line, 11, 5)) {
					case 'RYPTI'://自动生成Cookie加密密匙
						fwrite($handle, str_replace('Example Key - Change Me!', md5($t_tck), $line));
						break;
					case 'TH_KE'://App Key
						fwrite($handle, str_replace('putyourinfohere', $t_tck, $line));
						break;
					case 'TH_SE'://App Secre
						fwrite($handle, str_replace('putyourinfohere', $t_tcs, $line));
						break;
					case 'R_TIT'://网站标题
						fwrite($handle, str_replace('Dabr for Weibo', $t_title, $line));
						break;
					case "ITE',"://邀请模式
						fwrite($handle, str_replace('0', $t_ivt, $line));
						break;
					case 'ITE_C'://邀请码
						fwrite($handle, str_replace('putyourinfohere', $t_psw, $line));
						break;
					case "UID',"://站长UID
						fwrite($handle, str_replace('1061630973', $t_uid, $line));
						break;
					case 'ACCOU'://Google Analytics 跟踪 ID
						fwrite($handle, str_replace('putyourinfohere', $t_gac, $line));
						break;
					case "_URL'"://Weibo API地址
						fwrite($handle, str_replace('http://api.weibo.com', $t_api, $line));
						break;
					case '_TLBA'://高级时间线
						fwrite($handle, str_replace('0', $t_tlb, $line));
						break;
					case '_RMSC'://高级提醒写入接口
						fwrite($handle, str_replace('0', $t_rsc, $line));
						break;
					default:
						fwrite($handle, $line);
				}
			}

			$handle = fopen(ABSPATH.'invited', 'a');
			fclose($handle);

			chmod(ABSPATH.'config.php', 0666);
			chmod(ABSPATH.'invited', 0666);

			echo '<p>恭喜！Dabr for Weibo 已经安装成功。准备好了？开始 &hellip; &hellip;</p><p class="step"><a href="index.php" class="button">开始使用！</a></p>';
		}

		break;
}
