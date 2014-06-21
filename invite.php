<?php
require('config.php');
require('languages/languages.php');

header('Content-type: text/html; charset=utf-8');

if (isset($_POST['p']) && isset($_POST['u'])) {

	if (INVITE == 0) {
		exit(__('Now In Open Mode, No Need To Invite'));
	}

	if ($_POST['p'] == INVITE_CODE) {
		$user = strtolower($_POST['u'])."\n";

		if (is_writable('invited')) {

			if (!$handle = fopen('invited', 'a')) {
				exit(__('Cannot Open the Invited List'));
			}

			if (fwrite($handle, $user) == FALSE) {
				echo __('Cannot Write to the Invited List');
			} else {
				echo __('The User ').trim($user).__(' Has Been Added to the Invited List');
			}

			fclose($handle);
		} else {
			echo __('Invited List is Not Writable');
		}
		
		exit;
	} else {
        exit(__('Invite Code Error'));
    }
}

?><!doctype html><meta charset="utf-8" /><title><?php echo __("Add invited user") ?></title><form action="invite.php" method="POST"><label><? echo __("Username")?> <input name="u" /></label> <label><? echo __("Invite Code")?> <input name="p" /></label> <input type="submit" /></form>
