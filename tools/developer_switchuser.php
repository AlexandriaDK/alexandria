<?php
// Change your own credentials. On imported systems without any users this
// makes it easier to test and access editor and admin sections of web site.
// 
// Copy this file www folder. Comment out the exit line below. Do not use in production!
exit;

session_start();
$_SESSION = [
	'user_id' => 1,
	'user_author_id' => 1,
	'user_site' => 'Admin Service',
	'user_name' => 'User Admin',
	'user_editor' => TRUE,
	'user_admin' => TRUE,
	'token' => md5(rand())
];
header("Content-Type: text/plain");
var_dump($_SESSION);

?>
