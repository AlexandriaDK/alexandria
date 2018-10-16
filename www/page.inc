<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>
			Alexandria <?php if ($title) print " - $title"; ?>
		</title>

		<link rel="stylesheet" type="text/css" href="alexstyle.css" />
		<link rel="SHORTCUT ICON" href="/favicon.ico" />
		<style type="text/css">
			BODY {
				background: #fff;
			}
		</style>

<?php
print "<!-- {$_SERVER['REMOTE_ADDR']} -->\n";
?>

</head>
<body>

<?php
print $header;
if (!$hidebar) {
	print topbar();
}


?>
