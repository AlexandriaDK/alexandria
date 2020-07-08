<?php
require("./connect.php");
require("base.inc.php");

if ($_REQUEST['remote']) {
	if ($loginurl = getone("SELECT loginurl FROM remotelogin WHERE site = '".$_REQUEST['remote']."'") ) {
		header("Location: ".$loginurl);
	}
	exit;
}

if ($_SERVER['QUERY_STRING'] == 'logout') {
	award_achievement(49);
	session_destroy();
	$redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
	header("Location: $redirect_url");
	exit;
}

$siteuserid = intval($_REQUEST['id']);
$hash = $_REQUEST['hash'];
$name = stripslashes($_REQUEST['name']);
$time = $_REQUEST['time'];
$site = $_REQUEST['site'];
if (!$site) $site = "rpgforum";

if ($siteuserid && $hash && $time) {
	if ($user_id = validate_remote_login($siteuserid,$hash,$time,$site,$name)) {

		// validated user
		$_SESSION['user_id'] = $user_id;
		if (!$name) $name = "[navn]";
		$_SESSION['user_name'] = $name;
		$_SESSION['user_site'] = $site;
		$_SESSION['user_site_id'] = $siteuserid;
		$_SESSION['user_admin'] = (bool) getone("SELECT admin FROM users WHERE id = '$user_id'");
		$redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
		check_login_achievements();
		header("Location: $redirect_url");
	} else {
		// invalid login
		print "Invalid login!";
	}
	
} else { // print directions to login
	print "Other!";
}

?>
