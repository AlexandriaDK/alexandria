<?php
session_start();
unset($auth, $authuser, $authusers, $authusernames);
$useridauthed = false;

if ($admonly && $_SESSION['user_editor'] &&!$_SESSION['user_admin']) {
	$must_be_admin = TRUE;
}

if ((!$_SESSION['user_editor'] && !$_SESSION['user_admin']) || ($admonly && !$_SESSION['user_admin']) ) {

	print '
<!DOCTYPE html>
<HTML><HEAD><TITLE>Alexandria...</TITLE></HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="./" style="text-decoration: none">Det Store Administrationssystem</a></i></font>
</p>

<p align="center">
Kun adgang for Alexandria-brødre <small>(m/k/i)</small>
</p>

';

	if ($must_be_admin) {

		print '
		<p align="center">
		(Denne side kræver udvidet adgang)
		</p>';

	}

	print '
	<p align="center">
		Log ind via <a href="/fblogin">Facebook</a> eller <a href="/twitterlogin">Twitter</a>
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
