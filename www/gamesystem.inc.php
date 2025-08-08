<?php
$this_type = 'gamesystem';
$this_id = $system;

if (isset($_SESSION) && isset($_SESSION['user_id'])) {
  $userlog = getuserloggames($_SESSION['user_id']);
}

// achievements
if ($system == 33) award_achievement(53); // Vampire
if ($system == 23) award_achievement(54); // MERP
if ($system == 3)  award_achievement(68); // Paranoia

$r = getrow("SELECT id, name, description FROM gamesystem WHERE id = '$system'");
$showname = $sysname = $r['name'];

if ($r['id'] == 0) {
  $t->assign('content', $t->getTemplateVars('_nomatch'));
  $t->assign('pagetitle', $t->getTemplateVars('_find_nomatch'));
  $t->display('default.tpl');
  exit;
}
$q = getall("
	SELECT g.id, g.title, c.name, c.id AS con_id, c.year, c.begin, c.end, c.cancelled, c.country, person_extra, COUNT(f.id) AS files, p.id AS person_id, CONCAT(p.firstname,' ',p.surname) AS autname, pr.id AS presentation_id, pr.event_label, pr.iconfile, pr.textsymbol, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	LEFT JOIN pgrel ON pgrel.game_id = g.id AND pgrel.title_id IN (1,5)
	LEFT JOIN person p ON pgrel.person_id = p.id
	LEFT JOIN cgrel ON cgrel.game_id = g.id
	LEFT JOIN convention c ON cgrel.convention_id = c.id
	LEFT JOIN presentation pr ON cgrel.presentation_id = pr.id
	LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE g.gamesystem_id = '$system'
	GROUP BY g.id, c.id, p.id
	ORDER BY title_translation, c.year, c.begin, c.end, p.surname, p.firstname
");

$gamelist = [];

if (count($q) > 0) {
  foreach ($q as $rs) { // Put all together
    #		$game_id = $rs['id'];
    if (!isset($gamelist[$rs['id']])) {
      $gamelist[$rs['id']] = ['game' => ['title' => $rs['title_translation'], 'origtitle' => $rs['title'], 'person_extra' => $rs['person_extra'], 'files' => $rs['files']], 'person' => [], 'convention' => []];
    }
    if ($rs['person_id']) {
      $gamelist[$rs['id']]['person'][$rs['person_id']] = $rs['autname'];
    }
    if ($rs['con_id']) {
      $gamelist[$rs['id']]['convention'][$rs['con_id']] = ['id' => $rs['con_id'], 'name' => $rs['name'], 'year' => $rs['year'], 'begin' => $rs['begin'], 'end' => $rs['end'], 'cancelled' => $rs['cancelled'], 'country' => $rs['country'], 'iconfile' => $rs['iconfile'], 'textsymbol' => $rs['textsymbol'], 'event_label' => $rs['event_label'], 'presentation_id' => $rs['presentation_id']];
    }
  }

  if (isset($_SESSION) && isset($_SESSION['user_id'])) {
    foreach ($gamelist as $id => $game) {
      foreach (['read', 'gmed', 'played'] as $type) {
        $gamelist[$id]['userdata']['html'][$type] = getdynamicgamehtml($id, $type, $userlog[$id][$type] ?? false);
      }
    }
  }
  // Always provide defaults for template keys
  foreach ($gamelist as $id => $game) {
    $gamelist[$id]['userdata']['html'] = $gamelist[$id]['userdata']['html'] ?? ['read' => '', 'gmed' => '', 'played' => ''];
  }
  if (isset($_SESSION) && isset($_SESSION['user_id'])) {
    foreach ($gamelist as $id => $game) {
      foreach (['read', 'gmed', 'played'] as $type) {
        $gamelist[$id]['userdata']['html'][$type] = getdynamicgamehtml($id, $type, $userlog[$id][$type] ?? false);
      }
    }
  }
}

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE gamesystem_id = $system AND language = '$lang' AND visible = 1");
if (count($alttitle) == 1) {
  $showname = $alttitle[0];
  $aliaslist = getaliaslist($system, $this_type, $showname);
  if ($aliaslist) {
    $aliaslist = "<b title=\"" . $t->getTemplateVars("_g.original_title") . "\">" . htmlspecialchars($sysname) . "</b>, " . $aliaslist;
  } else {
    $aliaslist = "<b title=\"" . $t->getTemplateVars("_g.original_title") . "\">" . htmlspecialchars($sysname) . "</b>";
  }
} else {
  $aliaslist = getaliaslist($system, $this_type);
}

// List of files
$filelist = getfilelist($this_id, $this_type);

// Trivia, links and articles
$trivialist = gettrivialist($this_id, $this_type);
$linklist = getlinklist($this_id, $this_type);
$articles = getarticlereferences($this_id, $this_type);

// Thumbnail
$available_pic = hasthumbnailpic($system, $this_type);

// Smarty
$t->assign('pagetitle', $showname);
$t->assign('type', $this_type);

$t->assign('id', $system);
$t->assign('name', $showname);
$t->assign('pic', $available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type));
$t->assign('alias', $aliaslist);
$t->assign('description', $r['description']);
$t->assign('gamelist', $gamelist);
$t->assign('trivia', $trivialist);
$t->assign('link', $linklist);
$t->assign('articles', $articles);
$t->assign('filelist', $filelist);
$t->assign('filedir', getcategorydir($this_type));

$t->display('data.tpl');
