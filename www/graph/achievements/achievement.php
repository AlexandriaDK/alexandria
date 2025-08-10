<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../../base.inc.php';

$id = (int) $_GET['id'];

$a = getrow("SELECT id, label, description, points FROM achievements WHERE id = $id AND available = 1");

if (!$a) {
  http_response_code(404);
  exit('Achievement not found.');
}

$points = $a['points'] ?? 5;
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($a['label']) ?></title>
  <meta property="og:type" content="game.achievement" />
  <meta property="og:url" content="http://alexandria.dk/graph/achievements/<?= $a['id'] ?>.html" />
  <meta property="og:title" content="<?= htmlspecialchars($a['label']) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($a['description']) ?>" />
  <meta property="og:image" content="http://alexandria.dk/gfx/achievements/alexandria_logo.png" />
  <meta property="game:points" content="<?= $points ?>" />
  <meta property="fb:app_id" content="6044298682" />
</head>

<body>
  <h1><?= htmlspecialchars($a['label']) ?></h1>
</body>

</html>
