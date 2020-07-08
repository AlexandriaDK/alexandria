<?php
chdir("../../");
require("./connect.php");
require("./base.inc.php");

$id = (int) $_GET['id'];

$a = getrow("SELECT id, label, description, points FROM achievements WHERE id = $id AND available = 1");

if (!$a) {
	exit;
}

$points = $a['points'] ? $a['points'] : 5;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">
<head>
	<title><?php print htmlspecialchars($a['label']); ?></title>
	<meta property="og:type" content="game.achievement" />
	<meta property="og:url" content="http://alexandria.dk/graph/achievements/<?php print $a['id']; ?>.html" />
	<meta property="og:title" content="<?php print htmlspecialchars($a['label']); ?>" />
	<meta property="og:description" content="<?php print htmlspecialchars($a['description']); ?>" />
	<meta property="og:image" content="http://alexandria.dk/gfx/achievements/alexandria_logo.png" />
	<meta property="game:points" content="<?php print $points; ?>" />
	<meta property="fb:app_id" content="6044298682" />
	</head>
	<body>
		<h1><?php print htmlspecialchars($a['label']); ?></h1>
	</body>
</html>
