<?php
session_start();
unset($auth, $authuser, $authusers, $authusernames);
$useridauthed = false;

if (isset($admonly) && $_SESSION['user_editor'] && !$_SESSION['user_admin']) {
  $must_be_admin = TRUE;
}

if ((!$_SESSION['user_editor'] && !$_SESSION['user_admin']) || (isset($admonly) && !$_SESSION['user_admin'])) {

  print '
<!DOCTYPE html>
<HTML><HEAD><TITLE>Alexandria - login</TITLE></HEAD>

<body>

<p align="center"><font style="font-size: 30pt" face="Garamond, georgia, times New Roman, times" size="7" color="#990000">
<i><a href="./" style="text-decoration: none">Alexandria editors</a></i></font>
</p>

	';

  if ($must_be_admin) {

    print '
		<p align="center">
		(This page requires extended admin privileges)
		</p>';
  }

  print '
	<p align="center">
		Log in with <a href="../fblogin">Facebook</a> - <a href="../../login/google/">Google</a> - <a href="../../login/twitter/">Twitter</a> - <a href="../steamlogin">Steam</a> - <a href="../../login/twitch/">Twitch</a> - <a href="../../login/discord/">Discord</a>
	</p>
	';
  print '</body></html>';

  exit;
} else {
  // The user is an admin and is allowed to access this section. Check for token!
  $authuser = $_SESSION['user_name'];
  $token = $_REQUEST['token'] ?? '';
  if (! $_SESSION['token']) {
    print '
		<p align="center">
			You are missing a token! Please <a href="../logout">log out</a> and in again.
		</p>
	';
    exit;
  }

  // Should the user be allowed to view the log?
  $viewlog = TRUE;
}
