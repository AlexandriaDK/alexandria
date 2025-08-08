<?php
require("./connect.php");
require("base.inc.php");

$systems = getall("SELECT id, name FROM gamesystem ORDER BY name");
$genres = getall("SELECT id, name FROM genre WHERE genre = 1 ORDER BY name");
$categories = getall("SELECT id, name FROM genre WHERE genre = 0 ORDER BY name");
$consets = getall("SELECT id, name FROM conset ORDER BY name");
$filelanguages = getall("SELECT COUNT(DISTINCT game_id) AS count, language FROM files WHERE language != '' AND downloadable = 1 GROUP BY language HAVING COUNT(DISTINCT game_id) > 0 ORDER BY count DESC", FALSE);

$suggestlanguages = [];
foreach ($filelanguages as $filelanguage) {
  $languagename = getLanguageName($filelanguage['language']);
  if ($languagename != $filelanguage['language']) { // only accept known languages
    $suggestlanguages[] = ['count' => $filelanguage['count'], 'code' => $filelanguage['language'], 'name' => $languagename];
  } else {
  }
}

$t->assign('systems', $systems);
$t->assign('genres', $genres);
$t->assign('categories', $categories);
$t->assign('consets', $consets);
$t->assign('suggestlanguages', $suggestlanguages);
$t->display('find_advanced.tpl');
