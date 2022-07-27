<?php
$this_type = 'tag';

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

list($tag_id, $ttag, $description) = getrow("SELECT id, tag, description FROM tag WHERE tag = '" . dbesc($tag) . "'");
$this_id = $tag_id ?? 0;

$tag = getone("SELECT tag FROM tags WHERE tag = '" . dbesc($tag) . "'");
if (!$tag && !$tag_id) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}
if (!$tag) {
	$tag = $ttag;
}
$q = getall("
	SELECT g.id, title, c.name, c.id AS con_id, c.year, c.begin, c.end, c.cancelled, person_extra, COUNT(f.id) AS files, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	INNER JOIN tags ON g.id = tags.game_id
	LEFT JOIN cgrel ON cgrel.game_id = g.id AND cgrel.presentation_id = 1
	LEFT JOIN convention c ON cgrel.convention_id = c.id
	LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE tags.tag = '" . dbesc($tag). "'
	GROUP BY g.id, c.id
	ORDER BY title_translation
");

$slist = [];
$sl = 0;

if (count($q) > 0) {
	foreach($q AS $rs) {
		if ($_SESSION['user_id']) {
			foreach(array('read','gmed','played') AS $type) {
				$slist[$sl][$type] = getdynamicgamehtml($rs['id'],$type,$userlog[$rs['id']][$type] ?? FALSE);
			}
		}
		$game_id = (int) $rs['id'];
		// query-i-løkke... skal optimeres!
		$slist[$sl]['files'] = $rs['files'];
		$slist[$sl]['link'] = "data?scenarie=".$rs['id'];
		$slist[$sl]['title'] = $rs['title_translation'];
		$slist[$sl]['origtitle'] = $rs['title'];
		$slist[$sl]['personlist'] = "";
		$slist[$sl]['cancelled'] = $rs['cancelled'];

		$personlist = [];
		$qq = getall("
			SELECT p.id, CONCAT(firstname,' ',surname) AS name
			FROM person p, pgrel
			WHERE pgrel.game_id = $game_id AND pgrel.person_id = p.id AND pgrel.title_id IN(1,5)
			ORDER BY firstname, surname
		");
		foreach($qq AS $thisforfatter) {
			list($forfid,$forfname) = $thisforfatter;
			$personlist[] = "<a href=\"data?person={$forfid}\" class=\"person\">$forfname</a>";
		}
		if (!$personlist && $rs['person_extra']) {
			$personlist[] = $rs['person_extra'];
		}
		if ($personlist) {
			$slist[$sl]['personlist'] = join("<br />",$personlist);
		}

		if ($rs['con_id']) {
		$coninfo = nicedateset($rs['begin'],$rs['end']);
			$slist[$sl]['coninfo'] = $coninfo;
			$slist[$sl]['conlink'] = "data?con=".$rs['con_id'];
			$slist[$sl]['conname'] = $rs['name'] . " (" . yearname($rs['year']) . ")";
		}

		$sl++;
	}
}

// List of files
$filelist = getfilelist($this_id, $this_type);

// Trivia, links and articles
$trivialist = gettrivialist($this_id, $this_type);
$linklist = getlinklist($this_id, $this_type);
$articles = getarticlereferences($this_id, $this_type);

// Thumbnail
$available_pic = hasthumbnailpic($this_id, $this_type);

// Smarty
$t->assign('pagetitle',$tag);
$t->assign('type',$this_type);

$t->assign('id',$tag_id);
$t->assign('tag',$tag);
$t->assign('pic',$available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type) );
$t->assign('description',$description);
$t->assign('slist',$slist);
$t->assign('trivia',$trivialist);
$t->assign('link',$linklist);
$t->assign('articles',$articles);
$t->assign('filelist',$filelist);
$t->assign('filedir', getcategorydir($this_type) );
if (in_array(strtolower($tag), ['lgbtq', 'queer', 'queerness'] ) ) {
	$t->assign('lgbtmenu', TRUE);
}

$t->display('data.tpl');
