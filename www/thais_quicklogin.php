<?php
session_start();

	$_SESSION['user_id'] = 63;
	$_SESSION['user_name'] = 'Thais Munk';
	$_SESSION['user_site'] = 'lokal login';
	$_SESSION['user_site_id'] = 5;
	$_SESSION['user_editor'] = true;
	check_login_achievements();
	header("Location: /");


?>
