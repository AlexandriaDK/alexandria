<?php
session_start();
unset($auth, $authuser, $authusers, $authusernames);
$useridauthed = false;

if (isset($admonly) && $_SESSION['user_editor'] &&!$_SESSION['user_admin']) {
	$must_be_admin = TRUE;
}

if ((!$_SESSION['user_editor'] && !$_SESSION['user_admin']) || (isset($admonly) && !$_SESSION['user_admin']) ) {

	print '
<!DOCTYPE html>
<HTML><HEAD><TITLE>Alexandria...</TITLE></HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="./" style="text-decoration: none">Alexandria editors</a></i></font>
</p>

';

	if ($must_be_admin) {

		print '
		<p align="center">
		(This page requires admin privileges)
		</p>';

	}

	print '
	<p align="center">
		Log in with <a href="../fblogin">Facebook</a> - <a href="../login/google/">Google</a> - <a href="../login/twitter/">Twitter</a> - <a href="../steamlogin">Steam</a> - <a href="../login/twitch/">Twitch</a> - <a href="../login/discord/">Discord</a>
	</p>
	';
	exit;
} else {
// Så er brugeren i orden!
	$authuser = $_SESSION['user_name'];

// Skal brugeren kunne se ændringer?
	$viewlog = TRUE;
}

?>
