<?php
	#####################################################
	#
	# Connect til database
	#
	#####################################################
	require_once('../../includes/db.auth.php');
	$db_name = DB_NAME;
	$db_user = DB_USER;
	$db_pass = DB_PASS;
	$db_host = DB_HOST;
	$db_connector = DB_CONNECTOR;

	if (!@mysql_connect($db_host, "$db_user","$db_pass") ) {
		print "<h1>Alexandria under maintenance</h1>";
		print "<!--".dberror()."-->\n";
		exit;
	}
	mysql_select_db($db_name) or die("Unable to select db\n");
	mysql_set_charset("utf8mb4");
	mysql_query("SET sql_mode = ''"); // allow dates such as 0000-00-00
?>
